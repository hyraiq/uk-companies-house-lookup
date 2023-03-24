<?php

declare(strict_types=1);

namespace Hyra\UkCompaniesHouseLookup\Exception;

class BusinessNumberInvalidException extends \RuntimeException
{
    public function __construct(
        \Throwable $previous = null
    ) {
        parent::__construct(
            'Invalid Business number',
            0,
            $previous
        );
    }
}
