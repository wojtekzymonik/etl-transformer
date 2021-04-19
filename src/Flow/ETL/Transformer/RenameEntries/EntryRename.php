<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\RenameEntries;

final class EntryRename
{
    private string $form;

    private string $to;

    public function __construct(string $form, string $to)
    {
        $this->form = $form;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function form() : string
    {
        return $this->form;
    }

    /**
     * @return string
     */
    public function to() : string
    {
        return $this->to;
    }
}
