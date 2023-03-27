<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup;

final class BusinessNumberValidator
{
    public static function isValidNumber(string $businessNumber): bool
    {
        // Replace whitespace and hyphens
        $businessNumber = \preg_replace('/[\s-]+/', '', $businessNumber);
        if (null === $businessNumber) {
            return false;
        }

        /*
         * Regex sourced from the gist below but with a small adjustment of adding OE (overseas entity)
         *
         * @link https://gist.github.com/rob-murray/01d43581114a6b319034732bcbda29e1
         */
        return 1 === \preg_match('/^(((AC|ZC|FC|GE|LP|OC|SE|SA|SZ|SF|GS|SL|SO|SC|ES|NA|NZ|NF|GN|NL|NC|R0|NI|EN|OE|\d{2}|SG|FE)\d{5}(\d|C|R))|((RS|SO)\d{3}(\d{3}|\d{2}[WSRCZF]|\d(FI|RS|SA|IP|US|EN|AS)|CUS))|((NI|SL)\d{5}[\dA])|(OC(([\dP]{5}[CWERTB])|([\dP]{4}(OC|CU)))))$/', $businessNumber);
    }
}
