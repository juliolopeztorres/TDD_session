<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\entityAPI;
use AppBundle\Exception\InvalidMatrixSide;
use AppBundle\Exception\InvalidMiddleCoordinates;
use AppBundle\Exception\InvalidRange;
use AppBundle\Exception\NonExistingSquare;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class entityAPITest extends WebTestCase
{
    const RANGE = 2;
    const TESTING_WIDTH = 5;

    const MIDDLE_SQUARE = 13;
    const CORNER_SQUARE = 1;
    const MIDDLE_SQUARE_COORDINATE_X = 2;
    const MIDDLE_SQUARE_COORDINATE_Y = 2;

    const DUMMY_COORDINATE_X = 9999;
    const DUMMY_COORDINATE_Y = 9999;
    const DUMMY_RANGE = 9999;

    const UP = 'UP';
    const DOWN = 'DOWN';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';

    /** @var entityAPI */
    private $entityAPI;

    public function setUp()
    {
        $this->entityAPI = entityAPI::createMatrixForTestWithHeightAndWidth(self::TESTING_WIDTH);
    }

    public function testCreateEntityWithEvenWidthHeight()
    {
        $this->expectException(InvalidMatrixSide::class);

        EntityAPI::createMatrixForTestWithHeightAndWidth(2);
    }

    public function testGetIdSquareFromCoordinates()
    {
        $idSquare = $this->entityAPI->getIdSquareFromCoordinates(self::MIDDLE_SQUARE_COORDINATE_X, self::MIDDLE_SQUARE_COORDINATE_Y);

        $this->assertIntAndEqualsToExpected($idSquare, self::MIDDLE_SQUARE);
    }

    public function testGetCoordinatesFromIdSquare()
    {
        list ($coordinateX, $coordinateY) = $this->entityAPI->getCoordinatesFromIdSquare(self::MIDDLE_SQUARE);

        $this->assertIntAndEqualsToExpected($coordinateX, self::MIDDLE_SQUARE_COORDINATE_X);
        $this->assertIntAndEqualsToExpected($coordinateY, self::MIDDLE_SQUARE_COORDINATE_Y);
    }

    public function testGetIdSquareFromNonExistingCoordinates()
    {
        $this->expectException(NonExistingSquare::class);

        $this->entityAPI->getIdSquareFromCoordinates(self::DUMMY_COORDINATE_X, self::DUMMY_COORDINATE_Y);
    }

    public function testGetIdSquareFromLeftDirection()
    {
        $idSquare = $this->entityAPI->getIdSquareFromDirection(self::MIDDLE_SQUARE, self::LEFT);

        $this->assertIntAndEqualsToExpected($idSquare, self::MIDDLE_SQUARE - 1);
    }

    public function testGetIdSquareFromRightDirection()
    {
        $idSquare = $this->entityAPI->getIdSquareFromDirection(self::MIDDLE_SQUARE, self::RIGHT);

        $this->assertIntAndEqualsToExpected($idSquare, self::MIDDLE_SQUARE + 1);
    }

    public function testGetIdSquareFromUpDirection()
    {
        $idSquare = $this->entityAPI->getIdSquareFromDirection(self::MIDDLE_SQUARE, self::UP);

        $this->assertIntAndEqualsToExpected($idSquare, self::MIDDLE_SQUARE - self::TESTING_WIDTH);
    }

    public function testGetIdSquareFromDownDirection()
    {
        $idSquare = $this->entityAPI->getIdSquareFromDirection(self::MIDDLE_SQUARE, self::DOWN);

        $this->assertIntAndEqualsToExpected($idSquare, self::MIDDLE_SQUARE + self::TESTING_WIDTH);
    }

    public function testGetCornerFromIdSquareAndRange()
    {
        $idSquare = $this->entityAPI->getIdCornerSquareFromIdMiddleSquareAndRange(self::MIDDLE_SQUARE, self::RANGE);

        $this->assertIntAndEqualsToExpected($idSquare, self::CORNER_SQUARE);
    }

    public function testGetFirstRowFromIdSquareAndRange()
    {
        $idFirstSquare = self::CORNER_SQUARE;
        $row = $this->entityAPI->getRowOrColumnFromIdSquareRangeDirection(
            $idFirstSquare,
            self::RANGE,
            self::RIGHT);

        for($i = 0; $i < self::TESTING_WIDTH; $i++) {
            $idSquare = $row[$i];
            $this->assertIntAndEqualsToExpected($idSquare, $i + 1);
        }
    }

    public function testGetFirstColumnFromIdSquareAndRange()
    {
        $idFirstSquare = self::CORNER_SQUARE;
        $column = $this->entityAPI->getRowOrColumnFromIdSquareRangeDirection(
            $idFirstSquare,
            self::RANGE,
            self::DOWN);

        for($i = 0; $i < self::TESTING_WIDTH; $i++) {
            $idSquare = $column[$i];
            $this->assertIntAndEqualsToExpected($idSquare, 1 + self::TESTING_WIDTH*$i);
        }
    }

    public function testGetSubMatrixFromCoordinatesAndRange()
    {
        $subMatrix = $this->entityAPI->getSubMatrixFromCoordinatesAndRange(
            self::MIDDLE_SQUARE_COORDINATE_X,
            self::MIDDLE_SQUARE_COORDINATE_Y,
            self::RANGE);

        $this->assertSubMatrix($subMatrix);
    }

    public function testGetSubMatrixFromIdSquareAndRange()
    {
        $subMatrix = $this->entityAPI->getSubMatrixFromIdSquareAndRange(self::MIDDLE_SQUARE, self::RANGE);

        $this->assertSubMatrix($subMatrix);
    }

    public function testGetSubMatrixFromNonExistingCoordinates()
    {
        $this->expectException(NonExistingSquare::class);

        $this->entityAPI->getSubMatrixFromCoordinatesAndRange(
            self::DUMMY_COORDINATE_X,
            self::DUMMY_COORDINATE_Y,
            self::RANGE);
    }

    public function testGetSubMatrixFromWrongMiddleCoordinatesAndRange()
    {
        $this->expectException(InvalidMiddleCoordinates::class);

        $this->entityAPI->getSubMatrixFromCoordinatesAndRange(
            self::MIDDLE_SQUARE_COORDINATE_X + 1,
            self::MIDDLE_SQUARE_COORDINATE_Y + 1,
            self::RANGE);
    }

    public function testGetSubMatrixFromExistingCoordinatesAndInvalidRange()
    {
        $this->expectException(InvalidRange::class);

        $this->entityAPI->getSubMatrixFromCoordinatesAndRange(
            self::MIDDLE_SQUARE_COORDINATE_X,
            self::MIDDLE_SQUARE_COORDINATE_Y,
            self::DUMMY_RANGE);
    }

    private function assertIntAndEqualsToExpected(int $int, int $expectedInt)
    {
        $this->assertInternalType('int', $int);
        $this->assertEquals($expectedInt, $int);
    }

    private function assertSubMatrix($subMatrix)
    {
        $numberOfSquares = self::TESTING_WIDTH * self::TESTING_WIDTH;

        for ($i = 0; $i < $numberOfSquares; $i++) {
            $idSquare = $subMatrix[$i];
            $this->assertIntAndEqualsToExpected($idSquare, $i + 1);
        }
    }
}