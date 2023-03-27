<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup;

use Hyra\UkCompaniesHouseLookup\Exception\ConnectionException;
use Hyra\UkCompaniesHouseLookup\Exception\NumberInvalidException;
use Hyra\UkCompaniesHouseLookup\Exception\NumberNotFoundException;
use Hyra\UkCompaniesHouseLookup\Model\CompanyResponse;

interface ApiClientInterface
{
    /**
     * @throws NumberInvalidException
     * @throws ConnectionException
     * @throws NumberNotFoundException
     */
    public function lookupNumber(string $businessNumber): CompanyResponse;
}
