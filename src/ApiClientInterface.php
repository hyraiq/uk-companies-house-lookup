<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup;

use Hyra\UkCompaniesHouseLookup\Exception\BusinessNumberInvalidException;
use Hyra\UkCompaniesHouseLookup\Exception\BusinessNumberNotFoundException;
use Hyra\UkCompaniesHouseLookup\Exception\BusinessRegistryConnectionException;
use Hyra\UkCompaniesHouseLookup\Model\CompanyResponse;

interface ApiClientInterface
{
    /**
     * @throws BusinessNumberInvalidException
     * @throws BusinessRegistryConnectionException
     * @throws BusinessNumberNotFoundException
     */
    public function lookupNumber(string $businessNumber): CompanyResponse;
}
