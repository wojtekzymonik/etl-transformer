<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class ChainTransformer implements Transformer
{
    /**
     * @var Transformer[]
     */
    private array $transformers;

    public function __construct(Transformer ...$transformers)
    {
        $this->transformers = $transformers;
    }

    public function transform(Rows $rows) : Rows
    {
        foreach ($this->transformers as $transformer) {
            $rows = $transformer->transform($rows);
        }

        return $rows;
    }
}
