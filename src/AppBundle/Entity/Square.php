<?php

namespace AppBundle\Entity;

class Square
{
    /** @var int */
    private $id;
    /** @var int */
    private $coordinateX;
    /** @var int */
    private $coordinateY;

    public static function create(): self
    {
        return new self();
    }

    public static function createWithId(int $id): self
    {
        return new self($id);
    }

    public static function createWithCoordinates(int $coordinateX, int $coordinateY): self
    {
        return new self(0, $coordinateX, $coordinateY);
    }

    public static function createWithIdAndCoordinates(int $id, int $coordinateX, int $coordinateY): self
    {
        return new self($id, $coordinateX, $coordinateY);
    }

    private function __construct(int $id = 0, int $coordinateX = 0, int $coordinateY = 0)
    {
        $this->id = $id;
        $this->coordinateX = $coordinateX;
        $this->coordinateY = $coordinateY;
    }

    public function getCoordinateX(): int
    {
        return $this->coordinateX;
    }

    public function getCoordinateY(): int
    {
        return $this->coordinateY;
    }

    public function getId(): int
    {
        return $this->id;
    }
}