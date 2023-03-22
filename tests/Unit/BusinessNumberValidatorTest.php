<?php

declare(strict_types=1);

namespace Hyra\Tests\AbnLookup\Unit;

use Hyra\UkCompaniesHouseLookup\BusinessNumberValidator;
use PHPUnit\Framework\TestCase;

class BusinessNumberValidatorTest extends TestCase
{
    /**
     * @dataProvider getValidTests
     */
    public function testValidNumber(string $abn): void
    {
        $result = BusinessNumberValidator::isValidNumber($abn);

        static::assertTrue($result);
    }

    /**
     * @dataProvider getInvalidTests
     */
    public function testInvalidNumber(string $abn): void
    {
        $result = BusinessNumberValidator::isValidNumber($abn);

        static::assertFalse($result);
    }

    /**
     * @return mixed[]
     */
    public function getValidTests(): array
    {
        return [
            'no spaces'   => ['03120645'],
            'with dashes' => ['03-12-06-45'],
            'with spaces' => ['03 12 06 45'],
            'random 1'    => ['SC311560'], // Scotland
            'random 2'    => ['NI649790'], // Northern Ireland
            'random 3'    => ['OE017184'], // Overseas entity
        ];
    }

    /**
     * @return mixed[]
     */
    public function getInvalidTests(): array
    {
        return [
            'less than 8 characters' => ['1234567'],
            'more than 8 characters' => ['123456789'],
            'invalid prefix'         => ['XX123456'],
        ];
    }
}
