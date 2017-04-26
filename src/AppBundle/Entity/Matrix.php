<?php

namespace AppBundle\Entity;


class Matrix
{
    const UP = 'UP';
    const DOWN = 'DOWN';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';

    private $side;
    private $squares;

    public static function create()
    {
        return new self();
    }

    public static function createWithSide(int $side): self
    {
        return new self($side);
    }

    public static function createWithSquares(array $squares): self
    {
        return new self(0, $squares);
    }

    public static function createWithSideAndSquares($side, $squares): self
    {
        return new self($side, $squares);
    }

    private function __construct(int $side = 0, array $squares = [])
    {
        $this->side = $side;
        $this->squares = $squares;
    }

    public function getSide(): int
    {
        return $this->side;
    }

    public function getSquares(): array
    {
        return $this->squares;
    }

    public function getSquareFromCoordinates(int $coordinateX, int $coordinateY): Square
    {
        /** @var Square $square */
        foreach ($this->squares AS $square) {
            if ($square->getCoordinateX() === $coordinateX && $square->getCoordinateY() === $coordinateY) {
                return $square;
            }
        }

        return Square::create();
    }

    public function getSquareFromId(int $idSquare): Square
    {
        /** @var Square $square */
        foreach ($this->squares AS $square) {
            if ($square->getId() === $idSquare) {
                return $square;
            }
        }

        return Square::create();
    }

    public function getCornerSquare(Square $middleSquare, int $range): Square
    {
        $moveToLeftTimes = $moveToUpTimes = $range;

        $squareLeft = $this->getSquareFromTimesDirection($middleSquare, self::LEFT, $moveToLeftTimes);
        $squareUp = $this->getSquareFromTimesDirection($squareLeft, self::UP, $moveToUpTimes);

        return $squareUp;
    }

    private function getSquareFromTimesDirection(Square $square, string $direction, int $times): Square
    {
        if ($times === 0) {
            return $square;
        }

        $squareMoved = $this->getSquareFromDirection($square, $direction);
        $times--;

        return $this->getSquareFromTimesDirection($squareMoved, $direction, $times);
    }

    public function getSquareFromDirection(Square $square, string $direction): Square
    {
        $idSquare = $square->getId();

        // Last -1 to get care of difference between index and square's id
        switch ($direction) {
            case self::UP:
                return $this->squares[$idSquare - $this->side - 1];
                break;
            case self::DOWN:
                return $this->squares[$idSquare + $this->side - 1];
                break;
            case self::LEFT:
                return $this->squares[$idSquare - 1 - 1];
                break;
            case self::RIGHT:
                return $this->squares[$idSquare + 1 - 1];
                break;
            default:
                return Square::create();
                break;
        }
    }

    public function getSquaresFromRangeAndDirection(Square $cornerSquare, int $range, string $direction): array
    {
        $side = $this->getSideOfSubMatrix($range);

        $result = [$cornerSquare];
        $squareMoved = $cornerSquare;
        for($i = 1; $i < $side; $i++) {
            $squareMoved = $this->getSquareFromDirection($squareMoved, $direction);
            $result[] = $squareMoved;
        }

        return $result;
    }

    public function getSubMatrixFromCoordinatesAndRange(int $coordinateX, int $coordinateY, int $range): self
    {
        $square = $this->getSquareFromCoordinates($coordinateX, $coordinateY);

        return $this->getSubMatrixFromSquareAndRange($square, $range);
    }

    private function getSubMatrixFromSquareAndRange(Square $square, int $range): self
    {
        $cornerSquare = $this->getCornerSquare($square, $range);

        $side = $this->getSideOfSubMatrix($range);

        $subMatrixSquares = $this->getSubMatrixSquares($cornerSquare, $range, $side);

        return self::createWithSideAndSquares($side, $subMatrixSquares);
    }

    private function getSubMatrixSquares(Square $square, int $range, int $times): array
    {
        $subMatrixArray = [];

        $row = $this->getSquaresFromRangeAndDirection($square, $range,self::RIGHT);
        $times--;

        $subMatrixArray = array_merge($subMatrixArray, $row);

        if ($times === 0) {
            return $subMatrixArray;
        }

        $square = $this->getSquareFromDirection($square, self::DOWN);

        return array_merge($subMatrixArray, $this->getSubMatrixSquares($square, $range, $times));
    }

    public static function createSquares(int $side): array
    {
        $squares = [];
        $id = 1;
        for($i = 0; $i < $side; $i++) {
            for($j = 0; $j < $side; $j++) {
                $squares[] = Square::createWithIdAndCoordinates($id, $i, $j);
                $id++;
            }
        }

        return $squares;
    }

    private function getSideOfSubMatrix(int $range): int
    {
        return 1 + $range*2;
    }

    public function prepareForTwig()
    {
        $side = $this->side;

        $squares = $this->squares;
        $matrix = [];
        $position = 0;
        for ($i = 0; $i < $side; $i++) {
            for ($j = 0; $j < $side; $j++) {
                $matrix[$i][$j] = $squares[$position++]->getId();
            }
        }

        return $matrix;
    }
}