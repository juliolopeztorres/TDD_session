<?php

namespace AppBundle\Exception;

class InvalidMatrixSide extends \RuntimeException
{
    const ERROR_CODE = 9997;
    const CUSTOM_MESSAGE = 'Invalid Matrix side. It must be an odd number (entered %s).';

    public function __construct(int $enteredWidthHeight)
    {
        parent::__construct(sprintf(self::CUSTOM_MESSAGE, $enteredWidthHeight), self::ERROR_CODE);
    }
}