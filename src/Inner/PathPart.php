<?php

namespace HenriqueBS0\Router\Inner;

class PathPart
{
    private int $position;
    private string $value;

    public function __construct(int $position, string $value)
    {
        $this->position = $position;
        $this->value = $value;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
