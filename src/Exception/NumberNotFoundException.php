<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Exception;

class NumberNotFoundException extends \RuntimeException
{
    public function __construct(
        \Throwable $previous = null
    ) {
        parent::__construct(
            'Business number not found',
            0,
            $previous
        );
    }
}
