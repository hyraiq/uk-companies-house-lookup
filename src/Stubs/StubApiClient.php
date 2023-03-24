<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Stubs;

use Hyra\UkCompaniesHouseLookup\ApiClientInterface;
use Hyra\UkCompaniesHouseLookup\BusinessNumberValidator;
use Hyra\UkCompaniesHouseLookup\Exception\BusinessNumberInvalidException;
use Hyra\UkCompaniesHouseLookup\Exception\BusinessNumberNotFoundException;
use Hyra\UkCompaniesHouseLookup\Model\CompanyResponse;

final class StubApiClient implements ApiClientInterface
{
    /** @var array<string, CompanyResponse> */
    private array $companyResponses = [];

    /** @var string[] */
    private array $notFoundBusinessNumbers = [];

    public function lookupNumber(string $businessNumber): CompanyResponse
    {
        if (false === BusinessNumberValidator::isValidNumber($businessNumber)) {
            throw new BusinessNumberInvalidException();
        }

        if (\array_key_exists($businessNumber, $this->companyResponses)) {
            return $this->companyResponses[$businessNumber];
        }

        if (\in_array($businessNumber, $this->notFoundBusinessNumbers, true)) {
            throw new BusinessNumberNotFoundException();
        }

        throw new \LogicException(
            'Make sure you set a stub response for the business number before calling the ApiClient'
        );
    }

    public function addMockResponse(CompanyResponse ...$companyResponse): void
    {
        foreach ($companyResponse as $response) {
            $this->companyResponses[$response->companyNumber] = $response;
        }
    }

    public function addNotFoundBusinessNumbers(string ...$businessNumbers): void
    {
        $this->notFoundBusinessNumbers = \array_merge(
            $this->notFoundBusinessNumbers,
            $businessNumbers,
        );
    }
}
