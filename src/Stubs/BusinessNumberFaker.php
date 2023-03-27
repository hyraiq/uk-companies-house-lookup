<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Stubs;

final class BusinessNumberFaker
{
    private function __construct()
    {
    }

    public static function validBusinessNumber(): string
    {
        // @phpstan-ignore-next-line it can find an appropriate source of randomness
        $businessNumber = \str_pad((string) \random_int(1, 99999999), 8, '0');

        // @phpstan-ignore-next-line it can find an appropriate source of randomness
        $useLetterPrefix = (bool) \random_int(0, 1);
        if ($useLetterPrefix) {
            $businessNumber = \substr_replace($businessNumber, self::randomLetterPrefix(), 0, 2);
        }

        return $businessNumber;
    }

    public static function invalidBusinessNumber(): string
    {
        return \sprintf(
            'XX%s',
            // @phpstan-ignore-next-line it can find an appropriate source of randomness
            \str_pad((string) \random_int(1, 999999), 6, '0')
        );
    }

    private static function randomLetterPrefix(): string
    {
        $prefixes = [
            'AC',
            'ZC',
            'FC',
            'GE',
            'LP',
            'OC',
            'SE',
            'SA',
            'SZ',
            'SF',
            'GS',
            'SL',
            'SO',
            'SC',
            'ES',
            'NA',
            'NZ',
            'NF',
            'GN',
            'NL',
            'NC',
            'R0',
            'NI',
            'EN',
            'OE',
        ];

        return $prefixes[\array_rand($prefixes)];
    }
}
