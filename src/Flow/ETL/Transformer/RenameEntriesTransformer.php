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
     * @var RenameEntries\EntryRename[]
     */
    private array $entryRenames;

    public function __construct(Transformer\RenameEntries\EntryRename ...$entryRenames)
    {
        $this->entryRenames = $entryRenames;
    }

    public function transform(Rows $rows) : Rows
    {
        foreach ($this->entryRenames as $entryRename) {
            $rows = $rows->map(function (Row $row) use ($entryRename) : Row {
                $newEntry = $row->entries()->get($entryRename->form())->rename($entryRename->to());

                return (new Row($row->entries()->remove($entryRename->form())))->add($newEntry);
            });
        }

        return $rows;
    }
}
