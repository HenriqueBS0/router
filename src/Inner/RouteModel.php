<?php

namespace HenriqueBS0\Router\Inner;

class RouteModel
{
    private int $numberPartsPath;
    private array $pathParts;
    private array $positionParametersPath;

    public function __construct(string $path)
    {
        $this->setNumberPartsPath($path);
        $this->setPathParts($path);
        $this->setPositionParametersPath($path);
    }

    private function setNumberPartsPath(string $path): void
    {
        $this->numberPartsPath = count($this->explodeByBar($path));
    }

    private function setPathParts(string $path): void
    {
        $parts = $this->explodeByBar($path);

        foreach ($parts as $position => $value) {
            if (!self::partIsParameter($value)) {
                $this->pathParts[] = new PathPart($position, $value);
            }
        }
    }

    private function setPositionParametersPath(string $path): void
    {
        $this->positionParametersPath = [];

        $parts = $this->explodeByBar($path);

        foreach ($parts as $position => $value) {
            if (self::partIsParameter($value)) {
                $this->positionParametersPath[] = $position;
            }
        }
    }

    public function getNumberPartsPath(): int
    {
        return $this->numberPartsPath;
    }

    public function getPathParts(): array
    {
        return $this->pathParts;
    }

    public function getPositionParametersPath(): array
    {
        return $this->positionParametersPath;
    }

    public function explodeByBar(string $path): array
    {
        return explode('/', $path);
    }

    private static function partIsParameter(string $pathPart): bool
    {
        $firstCharacter = substr($pathPart, 0, 1);
        $lastCharacter = substr($pathPart, -1);

        return $firstCharacter === '{' && $lastCharacter === '}';
    }
}
