<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Model;

use Hyra\UkCompaniesHouseLookup\SicCodes;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

final class CompanyResponse extends AbstractResponse
{
    #[SerializedName('company_name')]
    #[NotBlank]
    public string $companyName;

    #[SerializedName('company_number')]
    #[NotBlank]
    public string $companyNumber;

    #[NotBlank]
    public string $type;

    #[SerializedName('company_status')]
    #[NotBlank]
    public string $status;

    #[SerializedName('company_status_detail')]
    public ?string $statusDetail = null;

    #[SerializedName('date_of_creation')]
    #[NotBlank]
    public \DateTimeImmutable $dateOfCreation;

    #[NotBlank]
    public string $jurisdiction;

    #[SerializedName('registered_office_address')]
    public ?AddressResponse $address = null;

    /**
     * @var PreviousNamesResponse[]
     *
     * @Assert\All({
     *
     *     @Assert\Type("Hyra\UkCompaniesHouseLookup\Model\PreviousNamesResponse")
     * })
     */
    #[SerializedName('previous_company_names')]
    public array $previousCompanyNames = [];

    /**
     * @var array<array-key, array{code: string, description: null|string}>|string[]
     */
    #[SerializedName('sic_codes')]
    public array $sicCodes = [];

    /**
     * @param string[] $sicCodes
     */
    public function setSicCodes(array $sicCodes): void
    {
        $this->sicCodes = \array_map(
            fn (string $code): array => [
                'code'        => $code,
                'description' => SicCodes::getDescriptionByCode($code),
            ],
            $sicCodes
        );
    }

    public function isActive(): bool
    {
        return 'active' === \mb_strtolower($this->status);
    }
}
