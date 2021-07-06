<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter;
use Flow\ETL\Transformer\Filter\Filter\ValidValue\Validator;

/**
 * @psalm-immutable
 */
final class ValidValue implements Filter
{
    private string $entryName;

    private Validator $validator;

    public function __construct(string $entryName, Validator $validator)
    {
        $this->entryName = $entryName;
        $this->validator = $validator;
    }

    public function keep(Row $row) : bool
    {
        /** @psalm-suppress ImpureMethodCall */
        return $this->validator->isValid($row->valueOf($this->entryName));
    }
}
