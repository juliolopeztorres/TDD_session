<?php

namespace AppBundle\Exception;

class NonExistingSquare extends \RuntimeException
{
    const ERROR_CODE = 9999;
    const CUSTOM_MESSAGE = 'There is no Square for these coordinates (X: %s, Y:%s).';

    public function __construct(int $coordinateX, int $coordinateY)
    {
        parent::__construct(sprintf(self::CUSTOM_MESSAGE, $coordinateX, $coordinateY), self::ERROR_CODE);
    }
}