<?php

declare(strict_types=1);

namespace Hyra\Tests\UkCompaniesHouseLookup\Model;

use Hyra\UkCompaniesHouseLookup\Model\AddressResponse;
use Hyra\UkCompaniesHouseLookup\Model\CompanyResponse;

final class CompanyResponseTest extends BaseModelTest
{
    public function testValidModel(): void
    {
        $data = $this->getValidResponse();

        $parsed = $this->valid($data, CompanyResponse::class);

        static::assertSame('04264132', $parsed->companyNumber);
        static::assertSame('BENTLEY CARS LIMITED', $parsed->companyName);
        static::assertSame('ltd', $parsed->type);
        static::assertSame('active', $parsed->status);
        static::assertNull($parsed->statusDetail);
        static::assertSame('2001-08-03', $parsed->dateOfCreation->format('Y-m-d'));
        static::assertSame('england-wales', $parsed->jurisdiction);
        static::assertCount(0, $parsed->previousCompanyNames);

        $expectedSicCodes = [
            [
                'code'        => '45190',
                'description' => 'Sale of other motor vehicles',
            ],
        ];
        static::assertSame($expectedSicCodes, $parsed->sicCodes);

        /** @var AddressResponse $address */
        $address = $parsed->address;
        static::assertSame('86 - 88 Uxbridge Road', $address->addressLine1);
        static::assertSame('Uxbridge Road Hanwell', $address->addressLine2);
        static::assertSame('W7 3SU', $address->postalCode);
        static::assertSame('London', $address->locality);
        static::assertNull($address->region);
        static::assertNull($address->country);
    }

    /**
     * @dataProvider getInValidTests
     *
     * @param string[] $keys
     */
    public function testInvalidModel(array $keys): void
    {
        $data = $this->getValidResponse();

        foreach ($keys as $key) {
            $data = $this->removeProperty($data, $key);
        }
        $this->invalid($data, CompanyResponse::class);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getInValidTests(): array
    {
        return [
            'missing company name'     => [['company_name']],
            'missing company number'   => [['company_number']],
            'missing type'             => [['type']],
            'missing status'           => [['company_status', 'status']],
            'missing date of creation' => [['date_of_creation']],
        ];
    }

    private function removeProperty(string $data, string $key): string
    {
        /** @var mixed[] $decoded */
        $decoded = \json_decode($data, true);

        unset($decoded[$key]);

        return \json_encode($decoded, \JSON_THROW_ON_ERROR);
    }

    private function getValidResponse(): string
    {
        return <<<JSON
        {
            "accounts": {
                "accounting_reference_date": {
                    "day": "31",
                    "month": "08"
                },
                "last_accounts": {
                    "made_up_to": "2021-08-31",
                    "period_end_on": "2021-08-31",
                    "period_start_on": "2020-09-01",
                    "type": "dormant"
                },
                "next_accounts": {
                    "due_on": "2023-05-31",
                    "overdue": false,
                    "period_end_on": "2022-08-31",
                    "period_start_on": "2021-09-01"
                },
                "next_due": "2023-05-31",
                "next_made_up_to": "2022-08-31",
                "overdue": false
            },
            "can_file": true,
            "company_name": "BENTLEY CARS LIMITED",
            "company_number": "04264132",
            "company_status": "active",
            "confirmation_statement": {
                "last_made_up_to": "2022-08-03",
                "next_due": "2023-08-17",
                "next_made_up_to": "2023-08-03",
                "overdue": false
            },
            "date_of_creation": "2001-08-03",
            "etag": "5ef4751d5062c7c033ae6ba0909f1215a94b247e",
            "has_been_liquidated": false,
            "has_charges": false,
            "has_insolvency_history": false,
            "has_super_secure_pscs": false,
            "jurisdiction": "england-wales",
            "last_full_members_list_date": "2015-08-03",
            "links": {
                "filing_history": "/company/04264132/filing-history",
                "officers": "/company/04264132/officers",
                "persons_with_significant_control": "/company/04264132/persons-with-significant-control",
                "self": "/company/04264132"
            },
            "registered_office_address": {
                "address_line_1": "86 - 88 Uxbridge Road",
                "address_line_2": "Uxbridge Road Hanwell",
                "locality": "London",
                "postal_code": "W7 3SU"
            },
            "registered_office_is_in_dispute": false,
            "sic_codes": [
                "45190"
            ],
            "status": "active",
            "type": "ltd",
            "undeliverable_registered_office_address": false
        }
        JSON;
    }
}
