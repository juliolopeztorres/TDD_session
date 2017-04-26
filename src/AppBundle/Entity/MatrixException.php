<?php

namespace AppBundle\Entity;

use AppBundle\Exception\InvalidMatrixSide;
use AppBundle\Exception\InvalidMiddleCoordinates;
use AppBundle\Exception\InvalidRange;
use AppBundle\Exception\NonExistingSquare;

class MatrixException
{
    /** @var int */
    private $side;
    /** @var int */
    private $range;
    /** @var Square */
    private $middleSquare;
    /** @var int */
    private $maximumDistance;
    /** @var int */
    private $maximumRange;

    public static function createWithSideAndRange(int $side, int $range): self
    {
        return new self($side, $range);
    }

    public static function createWithSide(int $side): self
    {
        return new self($side);
    }

    public static function create(): self
    {
        return new self();
    }

    private function __construct(int $side = 0, int $range = 0)
    {
        $this->side = $side;
        $this->range = $range;

        $this->maximumDistance = $this->getMaximumDistance();
        $this->maximumRange = $this->getMaximumRange();
    }

    private function getMaximumDistance(): float
    {
        $range = $this->range;

        return sqrt($range**2 + $range**2);
    }

    private function getMaximumRange(): int
    {
        return (int) floor($this->side / 2);
    }

    public function setMiddleSquare(Square $middleSquare)
    {
        $this->middleSquare = $middleSquare;
    }

    public function check(): bool
    {
        return $this->checkValidSide() &&
            $this->checkPositiveRange() &&
            $this->checkValidMiddleCoordinates();
    }

    public function checkValidSide(): bool
    {
        $side = $this->side;

        if ($side % 2 === 0) {
            throw new InvalidMatrixSide($side);
        }

        return true;
    }

    public function checkPositiveRange(): bool
    {
        $range = $this->range;
        if ($range < 0) {
            throw new InvalidRange($this->maximumRange);
        }

        return true;
    }

    public function checkValidCoordinates(Square $square): bool
    {
        if ($this->checkCoordinatesPositive($square) &&
            $this->checkCoordinatesInsideSubMatrix($square)) {
            return true;
        }

        throw new NonExistingSquare($square->getCoordinateX(), $square->getCoordinateY());
    }

    private function checkCoordinatesPositive(Square $square): bool
    {
        return $square->getCoordinateX() >= 0 && $square->getCoordinateY() >= 0;
    }

    public function checkCoordinatesInsideSubMatrix(Square $square): bool
    {
        $side = $this->side;
        /** @var Square $middleSquare */
        $middleSquare = $this->middleSquare;

        $a = ($middleSquare->getCoordinateX() - $square->getCoordinateX())**2;
        $b = ($middleSquare->getCoordinateY() - $square->getCoordinateY())**2;

        $distance = sqrt($a + $b);

        return $square->getCoordinateX() < $side &&
            $square->getCoordinateY() < $side &&
            $distance <= $this->maximumDistance;
    }

    public function checkValidMiddleCoordinates()
    {
        if ($this->checkValidRangeUp() &&
            $this->checkValidRangeDown() &&
            $this->checkValidRangeLeft() &&
            $this->checkValidRangeRight()) {
            return true;
        }

        throw new InvalidMiddleCoordinates();
    }

    private function checkValidRangeUp() : bool
    {
        $square = $this->middleSquare;
        $squareToCheck = Square::createWithCoordinates($square->getCoordinateX() - $this->range, $square->getCoordinateY());
        try {
            return $this->checkValidCoordinates($squareToCheck);
        } catch (NonExistingSquare $exp) {
            return false;
        }
    }

    private function checkValidRangeDown() : bool
    {
        $square = $this->middleSquare;
        $squareToCheck = Square::createWithCoordinates($square->getCoordinateX() + $this->range, $square->getCoordinateY());
        try {
            return (bool) $this->checkValidCoordinates($squareToCheck);
        } catch (NonExistingSquare $exp) {
            return false;
        }
    }

    private function checkValidRangeLeft() : bool
    {
        $square = $this->middleSquare;
        $squareToCheck = Square::createWithCoordinates($square->getCoordinateX(), $square->getCoordinateY() - $this->range);
        try {
            return (bool) $this->checkValidCoordinates($squareToCheck);
        } catch (NonExistingSquare $exp) {
            return false;
        }
    }

    private function checkValidRangeRight() : bool
    {
        $square = $this->middleSquare;
        $squareToCheck = Square::createWithCoordinates($square->getCoordinateX(), $square->getCoordinateY() + $this->range);
        try {
            return (bool) $this->checkValidCoordinates($squareToCheck);
        } catch (NonExistingSquare $exp) {
            return false;
        }
    }
}