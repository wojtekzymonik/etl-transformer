<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use function Flow\ArrayDot\array_dot_rename;
use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Flow\ETL\Transformer\Rename\ArrayKeyRename;

/**
 * @psalm-immutable
 */
final class ArrayDotRenameTransformer implements Transformer
{
    /**
     * @var ArrayKeyRename[]
     */
    private array $arrayKeyRenames;

    public function __construct(ArrayKeyRename ...$arrayKeyRenames)
    {
        $this->arrayKeyRenames = $arrayKeyRenames;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            foreach ($this->arrayKeyRenames as $arrayKeyRename) {
                $arrayEntry = $row->get($arrayKeyRename->arrayEntry());

                if (!$arrayEntry instanceof Row\Entry\ArrayEntry) {
                    $entryClass = \get_class($arrayEntry);

                    throw new RuntimeException("{$arrayEntry->name()} is not ArrayEntry but {$entryClass}");
                }

                $row = $row->set(
                    new Row\Entry\ArrayEntry(
                        $arrayEntry->name(),
                        array_dot_rename($arrayEntry->value(), $arrayKeyRename->path(), $arrayKeyRename->newName())
                    )
                );
            }

            return $row;
        };

        return $rows->map($transformer);
    }
}
