<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Matrix;
use AppBundle\Entity\MatrixException;
use AppBundle\Entity\Square;
use AppBundle\Exception\InvalidMatrixSide;
use AppBundle\Exception\InvalidMiddleCoordinates;
use AppBundle\Exception\InvalidRange;
use AppBundle\Exception\NonExistingSquare;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MatrixExceptionTest extends WebTestCase
{
    const DUMMY_COORDINATE_X = 9999;
    const DUMMY_COORDINATE_Y = 9999;
    const MIDDLE_COORDINATE_X = 2;
    const MIDDLE_COORDINATE_Y = 2;
    const DUMMY_RANGE = 9999;
    const EVEN_SIDE = 2;

    const SIDE = 5;
    const RANGE = 1;
    const INVALID_RANGE = -1;

    /** @var MatrixException */
    private $matrixException;
    /** @var Square */
    private $middleSquare;

    public function setUp()
    {
        $this->matrixException = MatrixException::createWithSideAndRange(self::SIDE, self::RANGE);

        $this->middleSquare = Square::createWithCoordinates(self::MIDDLE_COORDINATE_X, self::MIDDLE_COORDINATE_Y);
        $this->matrixException->setMiddleSquare($this->middleSquare);
    }

    public function testCreateMatrixWithEvenSide()
    {
        $this->expectException(InvalidMatrixSide::class);

        $matrixException = MatrixException::createWithSide(self::EVEN_SIDE);

        $matrixException->checkValidSide();
    }

    public function testCreateMatrixWithNonPositiveRange()
    {
        $this->expectException(InvalidRange::class);

        $matrixException = MatrixException::createWithSideAndRange(self::EVEN_SIDE, self::INVALID_RANGE);

        $matrixException->checkPositiveRange();
    }

    public function testGetSquareFromNonExistingCoordinates()
    {
        $this->expectException(NonExistingSquare::class);

        $matrixException = $this->matrixException;

        $square = Square::createWithCoordinates(self::DUMMY_COORDINATE_X, self::DUMMY_COORDINATE_Y);

        $matrixException->checkValidCoordinates($square);
    }

    public function testGetSubMatrixFromWrongMiddleCoordinates()
    {
        $this->expectException(InvalidMiddleCoordinates::class);

        $matrixException = $this->matrixException;

        $square = Square::createWithCoordinates(self::DUMMY_COORDINATE_X, self::DUMMY_COORDINATE_Y);
        $matrixException->setMiddleSquare($square);

        $matrixException->checkValidMiddleCoordinates();
    }

    public function testCheckCoordinatesInsideSubMatrix()
    {
        $matrixException = $this->matrixException;

        $listRowInOfSubMatrix = [[1, 1], [1, 2], [1, 3]];
        $listRowInOfSubMatrix2 = [[3, 1], [3, 2], [3, 3]];

        $listColumnInOfSubMatrix = [[1, 1], [1, 2], [1, 3]];
        $listColumnInOfSubMatrix2 = [[3, 1], [3, 2], [3, 3]];

        $listInOfSubMatrix = array_merge($listRowInOfSubMatrix, $listRowInOfSubMatrix2, $listColumnInOfSubMatrix, $listColumnInOfSubMatrix2);
        foreach ($listInOfSubMatrix AS $coordinates) {
            $square = Square::createWithCoordinates($coordinates[0], $coordinates[1]);
            $result = $matrixException->checkCoordinatesInsideSubMatrix($square);

            $this->assertTrue($result);
        }
    }

    public function testCheckCoordinatesOutsideSubMatrix()
    {
        $matrixException = $this->matrixException;

        $listRowOutOfSubMatrix = [[0, 0], [0, 1], [0, 2], [0, 3], [0, 4]];
        $listRowOutOfSubMatrix2 = [[4, 0], [4, 1], [4, 2], [4, 3], [4, 4]];

        $listColumnOutOfSubMatrix = [[0, 0], [1, 0], [2, 0], [3, 0], [4, 0]];
        $listColumnOutOfSubMatrix2 = [[0, 4], [1, 4], [2, 4], [3, 4], [4, 4]];

        $listOutOfSubMatrix = array_merge($listRowOutOfSubMatrix, $listRowOutOfSubMatrix2, $listColumnOutOfSubMatrix, $listColumnOutOfSubMatrix2);

        foreach ($listOutOfSubMatrix AS $coordinates) {
            $square = Square::createWithCoordinates($coordinates[0], $coordinates[1]);
            $result = $matrixException->checkCoordinatesInsideSubMatrix($square);

            $this->assertFalse($result);
        }
    }
}