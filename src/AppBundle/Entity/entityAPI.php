<?php

namespace AppBundle\Entity;

use AppBundle\Exception\InvalidMatrixSide;
use AppBundle\Exception\InvalidMiddleCoordinates;
use AppBundle\Exception\InvalidRange;
use AppBundle\Exception\NonExistingSquare;

class entityAPI
{
    const UP = 'UP';
    const DOWN = 'DOWN';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';

    /** @var array */
    private $matrix;
    /** @var int */
    private $matrixWidthHeight;

    public static function createMatrixForTestWithHeightAndWidth(int $matrixWidthHeight) : self
    {
        return new self($matrixWidthHeight);
    }

    private function __construct(int $matrixWidthHeight)
    {
        $this->checkValidMatrixWidthHeight($matrixWidthHeight);

        $this->setMatrixWidthHeight($matrixWidthHeight);
        $this->setMatrix($this->createRegularMatrixWithRange($matrixWidthHeight));
    }

    private function checkValidMatrixWidthHeight($matrixWidthHeight) : bool
    {
        if ($matrixWidthHeight % 2 === 0) {
            throw new InvalidMatrixSide($matrixWidthHeight);
        }

        return true;
    }

    private function setMatrixWidthHeight(int $matrixWidthHeight)
    {
        $this->matrixWidthHeight = $matrixWidthHeight;
    }

    private function setMatrix(array $matrix)
    {
        $this->matrix = $matrix;
    }

    private function createRegularMatrixWithRange(int $matrixWidthHeight) : array
    {
        $matrix = [];
        $position = 1;
        for ($i = 0; $i < $matrixWidthHeight; $i++) {
            for ($j = 0; $j < $matrixWidthHeight; $j++) {
                $matrix[$i][] = $position++;
            }
        }
        return $matrix;
    }

    public function getSubMatrixFromCoordinatesAndRange(int $coordinateX, int $coordinateY, int $range) : array
    {
        $idSquare = $this->getIdSquareFromCoordinates($coordinateX, $coordinateY);

        $this->checkValidRange($range);
        $this->checkValidMiddleCoordinates($coordinateX, $coordinateY, $range);

        return $this->getSubMatrixFromIdSquareAndRange($idSquare, $range);
    }

    private function checkValidRange(int $range) : bool
    {
        if ($this->getSideOfMatrix($range) > $this->matrixWidthHeight) {
            throw new InvalidRange($this->getMaximumRange());
        }

        return true;
    }

    private function getSideOfMatrix(int $range) : int
    {
        return 1 + $range*2;
    }

    private function getMaximumRange() : int
    {
        return (int) floor($this->matrixWidthHeight / 2);
    }

    private function checkValidMiddleCoordinates(int $coordinateX, int $coordinateY, $range) : bool
    {
        if ($this->checkValidRangeUp($coordinateX, $coordinateY, $range) &&
            $this->checkValidRangeDown($coordinateX, $coordinateY, $range) &&
            $this->checkValidRangeLeft($coordinateX, $coordinateY, $range) &&
            $this->checkValidRangeRight($coordinateX, $coordinateY, $range)) {
            return true;
        }

        throw new InvalidMiddleCoordinates($this->getMaximumRange());
    }

    private function checkValidRangeUp(int $coordinateX, int $coordinateY, int $range) : bool
    {
        try {
            return (bool) $this->getIdSquareFromCoordinates($coordinateX - $range, $coordinateY);
        } catch (NonExistingSquare $exp) {
            return false;
        }
    }

    private function checkValidRangeDown(int $coordinateX, int $coordinateY, int $range) : bool
    {
        try {
            return (bool) $this->getIdSquareFromCoordinates($coordinateX + $range, $coordinateY);
        } catch (NonExistingSquare $exp) {
            return false;
        }
    }

    private function checkValidRangeLeft(int $coordinateX, int $coordinateY, int $range) : bool
    {
        try {
            return (bool) $this->getIdSquareFromCoordinates($coordinateX, $coordinateY - $range);
        } catch (NonExistingSquare $exp) {
            return false;
        }
    }

    private function checkValidRangeRight(int $coordinateX, int $coordinateY, int $range) : bool
    {
        try {
            return (bool) $this->getIdSquareFromCoordinates($coordinateX, $coordinateY + $range);
        } catch (NonExistingSquare $exp) {
            return false;
        }
    }

    public function getIdSquareFromCoordinates(int $coordinateX, int $coordinateY) : int
    {
        try {
            return (int) $this->matrix[$coordinateX][$coordinateY];
        } catch (\Throwable $exp) {
            throw new NonExistingSquare($coordinateX, $coordinateY);
        }
    }

    public function getSubMatrixFromIdSquareAndRange(int $idSquare, int $range) : array
    {
        $idCornerSquare = $this->getIdCornerSquareFromIdMiddleSquareAndRange($idSquare, $range);

        $times = $this->getSideOfMatrix($range);
        return $this->getSubMatrix($idCornerSquare, $range, $times);
    }

    public function getIdCornerSquareFromIdMiddleSquareAndRange(int $idSquare, int $range) : int
    {
        $moveToLeftTimes = $moveToUpTimes = $range;

        $idSquareLeft = $this->getIdSquareFromTimesDirection($idSquare, self::LEFT, $moveToLeftTimes);
        $idSquareUp = $this->getIdSquareFromTimesDirection($idSquareLeft, self::UP, $moveToUpTimes);

        return $idSquareUp;
    }

    private function getIdSquareFromTimesDirection(int $idSquare, string $direction, int $times) : int
    {
        if ($times === 0) {
            return $idSquare;
        }

        $idSquareMoved = $this->getIdSquareFromDirection($idSquare, $direction);
        $times--;

        return $this->getIdSquareFromTimesDirection($idSquareMoved, $direction, $times);
    }

    private function getSubMatrix(int $idSquare, int $range, int $times) : array
    {
        $subMatrix = [];

        $row = $this->getRowOrColumnFromIdSquareRangeDirection($idSquare, $range, self::RIGHT);
        $times--;

        $subMatrix = array_merge($subMatrix, $row);

        if ($times === 0) {
            return $subMatrix;
        }

        $idSquare = $this->getIdSquareFromDirection($idSquare, self::DOWN);

        return array_merge($subMatrix, $this->getSubMatrix($idSquare, $range, $times));
    }

    public function getRowOrColumnFromIdSquareRangeDirection(int $idSquare, int $range, string $direction) : array
    {
        $side = $this->getSideOfMatrix($range);

        $result = [$idSquare];
        $idSquareMoved = $idSquare;
        for($i = 1; $i < $side; $i++) {
            $idSquareMoved = $this->getIdSquareFromDirection($idSquareMoved, $direction);
            $result[] = $idSquareMoved;
        }

        return $result;
    }

    public function getIdSquareFromDirection(int $idSquare, string $direction) : int
    {
        [$coordinateX, $coordinateY] = $this->getCoordinatesFromIdSquare($idSquare);

        return $this->getIdSquareFromCoordinatesAndDirection($coordinateX, $coordinateY, $direction);
    }

    public function getCoordinatesFromIdSquare(int $idSquare) : array
    {
        $position = 1;
        for ($i = 0; $i < $this->matrixWidthHeight; $i++) {
            for ($j = 0; $j < $this->matrixWidthHeight; $j++) {
                if ($position++ === $idSquare) {
                    return [$i, $j];
                }
            }
        }
    }

    private function getIdSquareFromCoordinatesAndDirection(int $coordinateX, int $coordinateY, string $direction): int
    {
        switch ($direction) {
            case self::UP:
                return (int) $this->matrix[$coordinateX - 1][$coordinateY];
                break;
            case self::DOWN:
                return (int) $this->matrix[$coordinateX + 1][$coordinateY];
                break;
            case self::LEFT:
                return (int) $this->matrix[$coordinateX][$coordinateY - 1];
                break;
            case self::RIGHT:
                return (int) $this->matrix[$coordinateX][$coordinateY + 1];
                break;
            default:
                return 0;
                break;
        }
    }

    public function getMatrix() : array
    {
        return $this->matrix;
    }
}