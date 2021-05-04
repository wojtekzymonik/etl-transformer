<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Row\Entry;

class CastEntry
{
    private string $entryName;

    private string $newClass;

    /**
     * @var array<string>
     */
    private array $extraArguments;

    /**
     * @var ?callable
     */
    private $cast;

    /**
     * @param string $entryName
     * @param string $newClass
     * @param array<mixed> $extraArguments
     * @param callable(mixed $value) : mixed|null $cast
     * @psalm-suppress MixedPropertyTypeCoercion
     *
     * @throws InvalidArgumentException
     */
    protected function __construct(string $entryName, string $newClass, array $extraArguments, ?callable $cast = null)
    {
        if (!\class_exists($newClass) || !\is_a($newClass, Entry::class, true)) {
            throw new InvalidArgumentException("{$newClass} is not valid class or does not implement Entry interface");
        }

        $this->entryName = $entryName;
        $this->newClass = $newClass;
        $this->extraArguments = $extraArguments;
        $this->cast = $cast;
    }

    /**
     * @return string
     */
    final public function entryName() : string
    {
        return $this->entryName;
    }

    /**
     * @return string
     */
    final public function newClass()
    {
        return $this->newClass;
    }

    /**
     * @return array<mixed>
     */
    final public function extraArguments() : array
    {
        return $this->extraArguments;
    }

    /**
     * @return null|callable
     */
    final public function cast() : ?callable
    {
        return $this->cast;
    }
}
