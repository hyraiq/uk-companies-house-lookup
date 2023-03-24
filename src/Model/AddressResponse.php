<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class AddressResponse extends AbstractResponse
{
    #[SerializedName('address_line_1')]
    public ?string $addressLine1 = null;

    #[SerializedName('address_line_2')]
    public ?string $addressLine2 = null;

    #[SerializedName('postal_code')]
    public ?string $postalCode = null;

    #[SerializedName('locality')]
    public ?string $locality = null;

    #[SerializedName('region')]
    public ?string $region = null;

    #[SerializedName('country')]
    public ?string $country = null;
}
