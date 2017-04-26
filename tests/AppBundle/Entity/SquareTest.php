<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Square;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SquareTest extends WebTestCase
{
    const COORDINATE_X = 10;
    const COORDINATE_Y = 10;
    const ID = 1;

    public function testCreateSquare()
    {
        $square = Square::create();

        $this->assertInstanceOf(Square::class, $square);
    }

    public function testCreateSquareWithId()
    {
        $square = Square::createWithId(self::ID);

        $this->assertInstanceOf(Square::class, $square);

        $this->assertIntAndEqualsToExpected($square->getId(), self::ID);
    }

    public function testCreateSquareWithCoordinates()
    {
        $square = Square::createWithCoordinates(self::COORDINATE_X, self::COORDINATE_Y);

        $this->assertInstanceOf(Square::class, $square);

        $this->assertIntAndEqualsToExpected($square->getCoordinateX(), self::COORDINATE_X);
        $this->assertIntAndEqualsToExpected($square->getCoordinateY(), self::COORDINATE_Y);
    }

    public function testCreateSquareWithIdAndCoordinates()
    {
        $square = Square::createWithIdAndCoordinates(self::ID, self::COORDINATE_X, self::COORDINATE_Y);

        $this->assertInstanceOf(Square::class, $square);

        $this->assertIntAndEqualsToExpected($square->getId(), self::ID);
        $this->assertIntAndEqualsToExpected($square->getCoordinateX(), self::COORDINATE_X);
        $this->assertIntAndEqualsToExpected($square->getCoordinateY(), self::COORDINATE_Y);
    }

    private function assertIntAndEqualsToExpected(int $int, int $expectedInt)
    {
        $this->assertInternalType('int', $int);
        $this->assertEquals($expectedInt, $int);
    }
}