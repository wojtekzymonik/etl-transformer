<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Row\Entry;

class CastEntry
{
    /**
     * @var array<string>
     */
    private array $entryNames;

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
     * @param array<string> $entryNames
     * @param string $newClass
     * @param array<mixed> $extraArguments
     * @param null|callable $cast
     *
     * @throws InvalidArgumentException
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    protected function __construct(array $entryNames, string $newClass, array $extraArguments, ?callable $cast = null)
    {
        if (\count($entryNames) === 0) {
            throw new InvalidArgumentException('{self::class} expects at least one entry name, none given');
        }

        if (!\class_exists($newClass) || !\is_a($newClass, Entry::class, true)) {
            throw new InvalidArgumentException("{$newClass} is not valid class or does not implement Entry interface");
        }

        $this->entryNames = $entryNames;
        $this->newClass = $newClass;
        $this->extraArguments = $extraArguments;
        $this->cast = $cast;
    }

    /**
     * @return array<string>
     */
    final public function entryNames() : array
    {
        return $this->entryNames;
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
