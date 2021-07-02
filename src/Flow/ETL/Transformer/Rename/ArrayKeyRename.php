<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Rename;

/**
 * @psalm-immutable
 */
final class ArrayKeyRename
{
    private string $arrayEntry;

    private string $path;

    private string $newName;

    public function __construct(string $arrayEntry, string $path, string $newName)
    {
        $this->arrayEntry = $arrayEntry;
        $this->path = $path;
        $this->newName = $newName;
    }

    public function arrayEntry() : string
    {
        return $this->arrayEntry;
    }

    public function path() : string
    {
        return $this->path;
    }

    public function newName() : string
    {
        return $this->newName;
    }
}
