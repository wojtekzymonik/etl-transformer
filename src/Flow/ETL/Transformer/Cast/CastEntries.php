<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row;

/**
 * @psalm-immutable
 */
abstract class CastEntries implements CastRow
{
    /**
     * @var array<string>
     */
    private array $entryNames;

    private EntryCaster $caster;

    private bool $nullable;

    /**
     * @param array<string> $entryNames
     * @param EntryCaster $caster
     * @param bool $nullable
     */
    public function __construct(array $entryNames, EntryCaster $caster, bool $nullable = false)
    {
        $this->entryNames = $entryNames;
        $this->nullable = $nullable;
        $this->caster = $caster;
    }

    final public function cast(Row $row) : Row
    {
        foreach ($this->entryNames as $entryName) {
            if ($row->entries()->has($entryName)) {
                $entry = $row->entries()->get($entryName);

                if ($this->nullable && $entry instanceof Row\Entry\NullEntry) {
                    continue;
                }

                $row = new Row($row->entries()
                    ->remove($entry->name())
                    ->add(
                        $this->caster->cast($entry)
                    ));
            }
        }

        return $row;
    }
}
