<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Rename;

/**
 * @psalm-immutable
 */
final class EntryRename
{
    private string $from;

    private string $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function from() : string
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function to() : string
    {
        return $this->to;
    }
}
