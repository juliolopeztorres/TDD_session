<?php

namespace AppBundle\Exception;


class InvalidRange extends \RuntimeException
{
    const ERROR_CODE = 9998;
    const CUSTOM_MESSAGE = 'Invalid range provided (maximum range: %s).';

    public function __construct(int $maximumRange)
    {
        parent::__construct(sprintf(self::CUSTOM_MESSAGE, $maximumRange), self::ERROR_CODE);
    }
}