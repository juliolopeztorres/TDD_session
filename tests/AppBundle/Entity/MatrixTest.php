<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Matrix;
use AppBundle\Entity\Square;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MatrixTest extends WebTestCase
{
    const UP = 'UP';
    const DOWN = 'DOWN';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';

    const SIDE = 5;
    const RANGE = 2;
    const MIDDLE_SQUARE_COORDINATE_X = 2;
    const MIDDLE_SQUARE_COORDINATE_Y = 2;
    const MIDDLE_SQUARE_ID = 13;

    const CORNER_SQUARE_COORDINATE_X = 0;
    const CORNER_SQUARE_COORDINATE_Y = 0;
    const CORNER_SQUARE_ID = 1;

    /** @var Square[] */
    private $squares;
    /** @var Matrix */
    private $matrix;
    /** @var Square */
    private $middleSquare;
    /** @var Square */
    private $cornerSquare;

    public function setUp()
    {
        $this->squares = Matrix::createSquares(self::SIDE);
        $this->matrix = Matrix::createWithSideAndSquares(self::SIDE, $this->squares);

        $this->middleSquare = Square::createWithIdAndCoordinates(
            self::MIDDLE_SQUARE_ID,
            self::MIDDLE_SQUARE_COORDINATE_X,
            self::MIDDLE_SQUARE_COORDINATE_Y);

        $this->cornerSquare = Square::createWithIdAndCoordinates(
            self::CORNER_SQUARE_ID,
            self::CORNER_SQUARE_COORDINATE_X,
            self::CORNER_SQUARE_COORDINATE_Y);
    }

    public function testCreateMatrix()
    {
        $matrix = Matrix::create();

        $this->assertInstanceOf(Matrix::class, $matrix);
    }

    public function testCreateMatrixWithSide()
    {
        $matrix = Matrix::createWithSide(self::SIDE);

        $this->assertInstanceOf(Matrix::class, $matrix);

        $this->assertIntAndEqualsToExpected($matrix->getSide(), self::SIDE);
    }

    public function testCreateWithSquares()
    {
        $squares = $this->squares;

        $matrix = Matrix::createWithSquares($squares);

        $this->assertInstanceOf(Matrix::class, $matrix);
        $this->assertEquals($squares, $matrix->getSquares());
    }

    public function testCreateWithSideAndSquares()
    {
        $squares = $this->squares;

        $matrix = Matrix::createWithSideAndSquares(self::SIDE, $squares);

        $this->assertInstanceOf(Matrix::class, $matrix);
        $this->assertIntAndEqualsToExpected($matrix->getSide(), self::SIDE);
        $this->assertEquals($squares, $matrix->getSquares());
    }

    public function testGetSquareFromCoordinates()
    {
        $matrix = $this->matrix;

        $square = $matrix->getSquareFromCoordinates(self::MIDDLE_SQUARE_COORDINATE_X, self::MIDDLE_SQUARE_COORDINATE_Y);

        $this->assertIntAndEqualsToExpected($square->getId(), self::MIDDLE_SQUARE_ID);
    }

    public function testGetSquareFromId()
    {
        $matrix = $this->matrix;

        $square = $matrix->getSquareFromId(self::MIDDLE_SQUARE_ID);

        $this->assertIntAndEqualsToExpected($square->getCoordinateX(), self::MIDDLE_SQUARE_COORDINATE_X);
        $this->assertIntAndEqualsToExpected($square->getCoordinateY(), self::MIDDLE_SQUARE_COORDINATE_Y);
    }

    public function testGetSquareFromLeftDirection()
    {
        $matrix = $this->matrix;
        $middleSquare = $this->middleSquare;

        $square = $matrix->getSquareFromDirection($middleSquare, self::LEFT);

        $this->assertIntAndEqualsToExpected($square->getId(), self::MIDDLE_SQUARE_ID - 1);
    }

    public function testGetSquareFromRightDirection()
    {
        $matrix = $this->matrix;
        $middleSquare = $this->middleSquare;

        $square = $matrix->getSquareFromDirection($middleSquare, self::RIGHT);

        $this->assertIntAndEqualsToExpected($square->getId(), self::MIDDLE_SQUARE_ID + 1);
    }

    public function testGetSquareFromUpDirection()
    {
        $matrix = $this->matrix;
        $middleSquare = $this->middleSquare;

        $square = $matrix->getSquareFromDirection($middleSquare, self::UP);

        $this->assertIntAndEqualsToExpected($square->getId(), self::MIDDLE_SQUARE_ID - self::SIDE);
    }

    public function testGetSquareFromDownDirection()
    {
        $matrix = $this->matrix;
        $middleSquare = $this->middleSquare;

        $square = $matrix->getSquareFromDirection($middleSquare, self::DOWN);

        $this->assertIntAndEqualsToExpected($square->getId(), self::MIDDLE_SQUARE_ID + self::SIDE);
    }

    public function testGetCornerSquare()
    {
        $matrix = $this->matrix;
        $middleSquare = $this->middleSquare;

        $cornerSquare = $matrix->getCornerSquare($middleSquare, self::RANGE);

        $this->assertIntAndEqualsToExpected($cornerSquare->getId(), self::CORNER_SQUARE_ID);
        $this->assertIntAndEqualsToExpected($cornerSquare->getCoordinateX(), self::CORNER_SQUARE_COORDINATE_X);
        $this->assertIntAndEqualsToExpected($cornerSquare->getCoordinateY(), self::CORNER_SQUARE_COORDINATE_Y);
    }

    public function testGetRow()
    {
        $matrix = $this->matrix;
        $cornerSquare = $this->cornerSquare;

        $row = $matrix->getSquaresFromRangeAndDirection(
            $cornerSquare,
            self::RANGE,
            self::RIGHT);

        /** @var Square $square $i */
        for($i = 0; $i < self::SIDE; $i++) {
            $square = $row[$i];
            $this->assertIntAndEqualsToExpected($square->getId(), $i + 1);
        }
    }

    public function testGetColumn()
    {
        $matrix = $this->matrix;
        $cornerSquare = $this->cornerSquare;

        $column = $matrix->getSquaresFromRangeAndDirection(
            $cornerSquare,
            self::RANGE,
            self::DOWN);

        /** @var Square $square $i */
        for($i = 0; $i < self::SIDE; $i++) {
            $square = $column[$i];
            $this->assertIntAndEqualsToExpected($square->getId(), 1 + self::SIDE*$i);
        }
    }

    public function testGetSubMatrixFromCoordinatesAndRange()
    {
        $matrix = $this->matrix;

        $subMatrix = $matrix->getSubMatrixFromCoordinatesAndRange(
            self::MIDDLE_SQUARE_COORDINATE_X,
            self::MIDDLE_SQUARE_COORDINATE_Y,
            self::RANGE);

        $this->assertSubMatrix($subMatrix);
    }

    public function testPrepareMatrixToTwig()
    {
        $matrix = $this->matrix;

        $matrixTwig = $matrix->prepareForTwig();

        $this->assertInternalType('array', $matrixTwig);
        $this->assertIntAndEqualsToExpected(count($matrixTwig), self::SIDE);
        $this->assertIntAndEqualsToExpected(count($matrixTwig[0]), self::SIDE);
    }

    private function assertIntAndEqualsToExpected(int $int, int $expectedInt)
    {
        $this->assertInternalType('int', $int);
        $this->assertEquals($expectedInt, $int);
    }

    private function assertSubMatrix(Matrix $subMatrix)
    {
        $numberOfSquares = self::SIDE * self::SIDE;

        $squares = $subMatrix->getSquares();
        /** @var Square $square $i */
        for ($i = 0; $i < $numberOfSquares; $i++) {
            $square = $squares[$i];
            $this->assertIntAndEqualsToExpected($square->getId(), $i + 1);
        }
    }
}