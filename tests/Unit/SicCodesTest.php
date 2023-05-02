<?php

declare(strict_types=1);

namespace Hyra\Tests\UkCompaniesHouseLookup\Unit;

use Hyra\UkCompaniesHouseLookup\SicCodes;
use PHPUnit\Framework\TestCase;

class SicCodesTest extends TestCase
{
    /**
     * @dataProvider getValidTests
     */
    public function testValidCodes(string $code, string $description): void
    {
        $result = SicCodes::getDescriptionByCode($code);

        static::assertSame($description, $result);
    }

    /**
     * @return array<array-key, array<array-key, string>>
     */
    public function getValidTests(): array
    {
        return [
            ['01120', 'Growing of rice'],
            ['16230', 'Manufacture of other builders\' carpentry and joinery'],
            ['99999', 'Dormant Company'],
        ];
    }

    /**
     * @dataProvider getInvalidTests
     */
    public function testInvalidCode(string $code): void
    {
        $result = SicCodes::getDescriptionByCode($code);

        static::assertNull($result);
    }

    /**
     * @return array<array-key, string[]>
     */
    public function getInvalidTests(): array
    {
        return [
            'invalid code' => ['123'],
            'empty code'   => [''],
        ];
    }
}
