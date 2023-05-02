<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Stubs;

final class MockCompanyResponse
{
    /**
     * @return array<string, mixed>
     */
    public static function valid(): array
    {
        return [
            'type'                      => 'ltd',
            'company_number'            => '03120645',
            'date_of_creation'          => '1995-11-01',
            'company_name'              => 'RED BULL RACING LIMITED',
            'jurisdiction'              => 'england-wales',
            'company_status'            => 'active',
            'company_status_detail'     => null,
            'registered_office_address' => [
                'address_line_1' => 'Building 2',
                'address_line_2' => 'Bradbourne Drive Tilbrook',
                'locality'       => 'Milton Keynes',
                'postal_code'    => 'MK7 8AT',
                'region'         => null,
                'country'        => null,
            ],
            'previous_company_names' => [
                [
                    'name'           => 'STEWART GRAND PRIX LIMITED',
                    'ceased_on'      => '2000-01-04',
                    'effective_from' => '1995-11-01',
                ],
                [
                    'ceased_on'      => '2004-11-16',
                    'name'           => 'JAGUAR RACING LIMITED',
                    'effective_from' => '2000-01-04',
                ],
            ],
            'sic_codes' => [
                '93199',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function noBusinessNumberFound(): array
    {
        return [
            'errors' => [
                [
                    'error' => 'company-profile-not-found',
                    'type'  => 'ch:service',
                ],
            ],
        ];
    }
}
