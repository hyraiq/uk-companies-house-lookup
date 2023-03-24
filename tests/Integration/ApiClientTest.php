<?php

declare(strict_types=1);

namespace Hyra\Tests\UkCompaniesHouseLookup\Integration;

use Faker\Factory;
use Faker\Generator;
use Hyra\UkCompaniesHouseLookup\ApiClient;
use Hyra\UkCompaniesHouseLookup\Dependencies;
use Hyra\UkCompaniesHouseLookup\Exception\BusinessNumberInvalidException;
use Hyra\UkCompaniesHouseLookup\Exception\BusinessNumberNotFoundException;
use Hyra\UkCompaniesHouseLookup\Exception\BusinessRegistryConnectionException;
use Hyra\UkCompaniesHouseLookup\Exception\UnexpectedResponseException;
use Hyra\UkCompaniesHouseLookup\Stubs\MockCompanyResponse;
use Hyra\UkCompaniesHouseLookup\Stubs\StubHttpClient;
use PHPUnit\Framework\TestCase;

final class ApiClientTest extends TestCase
{
    private const BusinessNumber = '03120645';

    private Generator $faker;

    private ApiClient $client;

    private StubHttpClient $stubHttpClient;

    private string $apiKey;

    protected function setUp(): void
    {
        $this->faker          = Factory::create();
        $denormalizer         = Dependencies::serializer();
        $validator            = Dependencies::validator();
        $this->stubHttpClient = new StubHttpClient();
        $this->apiKey         = $this->faker->uuid;

        $this->client = new ApiClient($denormalizer, $validator, $this->stubHttpClient, $this->apiKey);
    }

    /**
     * Yes, this is a bad test. It just reimplements logic in ApiClient. However, we want to ensure the defaults
     * don't change.
     */
    public function testClientInitialisedCorrectly(): void
    {
        $this->stubHttpClient->assertDefaultOptions([
            'base_uri' => 'https://api.company-information.service.gov.uk/',
            'auth_basic' => [$this->apiKey],
        ]);
    }

    public function testLookupNumberInvalidNumberDoesNotUseApi(): void
    {
        $this->expectException(BusinessNumberInvalidException::class);

        $this->client->lookupNumber($this->faker->numerify('#####'));

        $this->stubHttpClient->assertCompanyEndpointNotCalled();
    }

    public function testLookupNumberConnectionExceptionOnServerErrorResponse(): void
    {
        $this->stubHttpClient->setStubResponse([], 500);

        $this->expectException(BusinessRegistryConnectionException::class);

        $this->client->lookupNumber(self::BusinessNumber);
    }

    public function testLookupNumberWhenNumberNotFound(): void
    {
        $this->stubHttpClient->setStubResponse(MockCompanyResponse::noBusinessNumberFound(), 404);

        $this->expectException(BusinessNumberNotFoundException::class);

        $this->client->lookupNumber(self::BusinessNumber);
    }

    public function testLookupNumberWithInvalidBusinessNumber(): void
    {
        $this->expectException(BusinessNumberInvalidException::class);

        $this->client->lookupNumber('invalid');
    }

    public function testLookupNumberHandlesUnexpectedResponse(): void
    {
        $response                   = MockCompanyResponse::valid();
        $response['company_number'] = null;
        $this->stubHttpClient->setStubResponse($response);

        $this->expectException(UnexpectedResponseException::class);

        $this->client->lookupNumber(self::BusinessNumber);
    }

    public function testLookupNumberSuccess(): void
    {
        $mockResponse = MockCompanyResponse::valid();
        $this->stubHttpClient->setStubResponse($mockResponse);

        $response = $this->client->lookupNumber(self::BusinessNumber);

        $normalizedResponse = [
            'company_name'          => $response->companyName,
            'company_number'        => $response->companyNumber,
            'type'                  => $response->type,
            'company_status'        => $response->status,
            'company_status_detail' => $response->statusDetail,
            'date_of_creation'      => $response->dateOfCreation->format('Y-m-d'),
            'jurisdiction'          => $response->jurisdiction,
            'registered_office_address' => [
                'address_line_1' => $response->address?->addressLine1,
                'address_line_2' => $response->address?->addressLine2,
                'postal_code'    => $response->address?->postalCode,
                'locality'       => $response->address?->locality,
                'region'         => $response->address?->region,
                'country'        => $response->address?->country,
            ],
            'previous_company_names' => [
                [
                    'name'           => $response->previousCompanyNames[0]->name,
                    'effective_from' => $response->previousCompanyNames[0]->effectiveFrom?->format('Y-m-d'),
                    'ceased_on'      => $response->previousCompanyNames[0]->ceasedOn?->format('Y-m-d'),
                ],
                [
                    'name'           => $response->previousCompanyNames[1]->name,
                    'effective_from' => $response->previousCompanyNames[1]->effectiveFrom?->format('Y-m-d'),
                    'ceased_on'      => $response->previousCompanyNames[1]->ceasedOn?->format('Y-m-d'),
                ],
            ],
        ];

        $this->stubHttpClient->assertCompanyEndpointCalled([]);

        static::assertEqualsCanonicalizing($mockResponse, $normalizedResponse);
    }
}
