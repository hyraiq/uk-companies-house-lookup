<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Exception;

class NumberInvalidException extends \RuntimeException
{
    public function __construct(
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            'Invalid business number',
            0,
            $previous
        );
    }
}
