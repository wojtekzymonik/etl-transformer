<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class ArrayUnpackTransformer implements Transformer
{
    private const JSON_DEPTH = 512;

    private string $arrayEntryName;

    public function __construct(string $arrayEntryName)
    {
        $this->arrayEntryName = $arrayEntryName;
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress InvalidScalarArgument
     * @psalm-suppress MixedArgument
     */
    public function transform(Rows $rows) : Rows
    {
        return $rows->map(function (Row $row) : Row {
            if (!$row->entries()->has($this->arrayEntryName)) {
                throw new RuntimeException("\"{$this->arrayEntryName}\" not found");
            }

            if (!$row->entries()->get($this->arrayEntryName) instanceof Row\Entry\ArrayEntry) {
                throw new RuntimeException("\"{$this->arrayEntryName}\" is not ArrayEntry");
            }

            $entries = $row->entries()->remove($this->arrayEntryName);

            /**
             * @var int|string $key
             * @var mixed $value
             */
            foreach ($row->valueOf($this->arrayEntryName) as $key => $value) {
                $entryName = (string) $key;

                if (\is_string($value)) {
                    if (\class_exists('\\Flow\\ETL\\Row\\Entry\\JsonEntry') && $this->isJson($value)) {
                        $entries = $entries->add(new Row\Entry\JsonEntry($entryName, \json_decode($value, true, self::JSON_DEPTH, JSON_THROW_ON_ERROR)));

                        continue;
                    }

                    $entries = $entries->add(new Row\Entry\StringEntry($entryName, $value));

                    continue;
                }

                if (\is_float($value)) {
                    $entries = $entries->add(new Row\Entry\StringEntry($entryName, (string) $value));

                    continue;
                }

                if (\is_int($value)) {
                    $entries = $entries->add(new Row\Entry\IntegerEntry($entryName, $value));

                    continue;
                }

                if (\is_bool($value)) {
                    $entries = $entries->add(new Row\Entry\BooleanEntry($entryName, $value));

                    continue;
                }

                if (\is_object($value)) {
                    if ($value instanceof \DateTimeImmutable) {
                        $entries = $entries->add(new Row\Entry\DateTimeEntry($entryName, $value));

                        continue;
                    }

                    $entries = $entries->add(new Row\Entry\ObjectEntry($entryName, $value));

                    continue;
                }

                if (\is_array($value)) {
                    $entries = $entries->add(new Row\Entry\ArrayEntry($entryName, $value));

                    continue;
                }

                if (null === $value) {
                    $entries = $entries->add(new Row\Entry\NullEntry($entryName));
                }
            }

            return new Row($entries);
        });
    }

    private function isJson(string $string) : bool
    {
        try {
            /**
             * @psalm-suppress UnusedFunctionCall
             *
             * @var mixed $value
             */
            $value = \json_decode($string, true, self::JSON_DEPTH, JSON_THROW_ON_ERROR);

            if (\is_int($value)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
