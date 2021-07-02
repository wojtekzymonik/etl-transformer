<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class RenameEntriesTransformer implements Transformer
{
    /**
     * @var Rename\EntryRename[]
     */
    private array $entryRenames;

    public function __construct(Transformer\Rename\EntryRename ...$entryRenames)
    {
        $this->entryRenames = $entryRenames;
    }

    public function transform(Rows $rows) : Rows
    {
        foreach ($this->entryRenames as $entryRename) {
            $rows = $rows->map(function (Row $row) use ($entryRename) : Row {
                $newEntry = $row->entries()->get($entryRename->from())->rename($entryRename->to());

                return (new Row($row->entries()->remove($entryRename->from())))->add($newEntry);
            });
        }

        return $rows;
    }
}
