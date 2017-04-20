<?php

namespace AppBundle\Exception;

class InvalidMiddleCoordinates extends \RuntimeException
{
    const ERROR_CODE = 9996;
    const CUSTOM_MESSAGE = 'Invalid middle coordinates for sub matrix';

    public function __construct()
    {
        parent::__construct(self::CUSTOM_MESSAGE, self::ERROR_CODE);
    }

}