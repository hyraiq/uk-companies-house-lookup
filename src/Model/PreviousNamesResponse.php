<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class PreviousNamesResponse extends AbstractResponse
{
    public ?string $name = null;

    #[SerializedName('effective_from')]
    public ?\DateTimeImmutable $effectiveFrom = null;

    #[SerializedName('ceased_on')]
    public ?\DateTimeImmutable $ceasedOn = null;
}
