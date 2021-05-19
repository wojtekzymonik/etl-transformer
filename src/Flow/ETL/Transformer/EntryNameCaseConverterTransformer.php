<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Jawira\CaseConverter\Convert;

/**
 * @psalm-immutable
 */
final class EntryNameCaseConverterTransformer implements Transformer
{
    public const STYLE_CAMEL = 'camel';

    public const STYLE_PASCAL = 'pascal';

    public const STYLE_SNAKE = 'snake';

    public const STYLE_ADA = 'ada';

    public const STYLE_MACRO = 'macro';

    public const STYLE_KEBAB = 'kebab';

    public const STYLE_TRAIN = 'train';

    public const STYLE_COBOL = 'cobol';

    public const STYLE_LOWER = 'lower';

    public const STYLE_UPPER = 'upper';

    public const STYLE_TITLE = 'title';

    public const STYLE_SENTENCE = 'sentence';

    public const STYLE_DOT = 'dot';

    private string $style;

    /**
     * @var array<string>
     */
    private array $styles = [
        self::STYLE_CAMEL,
        self::STYLE_PASCAL,
        self::STYLE_SNAKE,
        self::STYLE_ADA,
        self::STYLE_MACRO,
        self::STYLE_KEBAB,
        self::STYLE_TRAIN,
        self::STYLE_COBOL,
        self::STYLE_LOWER,
        self::STYLE_UPPER,
        self::STYLE_TITLE,
        self::STYLE_SENTENCE,
    ];

    public function __construct(string $style)
    {
        /** @psalm-suppress ImpureFunctionCall */
        if (!\class_exists('Jawira\CaseConverter\Convert')) {
            throw new RuntimeException("Jawira\CaseConverter\Convert class not found, please add jawira/case-converter dependency to the project first.");
        }

        if (!\in_array($style, $this->styles, true)) {
            throw new InvalidArgumentException("Unrecognized style {$style}, please use one of following: " . \implode(', ', $this->styles));
        }

        $this->style = $style;
    }

    public function transform(Rows $rows) : Rows
    {
        /** @psalm-var pure-callable(Row $row) : Row $rowTransformer */
        $rowTransformer = function (Row $row) : Row {
            return $row->map(function (Entry $entry) : Entry {
                switch ($this->style) {
                    case self::STYLE_CAMEL:
                        return $entry->rename((new Convert($entry->name()))->toCamel());
                    case self::STYLE_PASCAL:
                        return $entry->rename((new Convert($entry->name()))->toPascal());
                    case self::STYLE_SNAKE:
                        return $entry->rename((new Convert($entry->name()))->toSnake());
                    case self::STYLE_ADA:
                        return $entry->rename((new Convert($entry->name()))->toAda());
                    case self::STYLE_MACRO:
                        return $entry->rename((new Convert($entry->name()))->toMacro());
                    case self::STYLE_KEBAB:
                        return $entry->rename((new Convert($entry->name()))->toKebab());
                    case self::STYLE_TRAIN:
                        return $entry->rename((new Convert($entry->name()))->toTrain());
                    case self::STYLE_COBOL:
                        return $entry->rename((new Convert($entry->name()))->toCobol());
                    case self::STYLE_LOWER:
                        return $entry->rename((new Convert($entry->name()))->toLower());
                    case self::STYLE_UPPER:
                        return $entry->rename((new Convert($entry->name()))->toUpper());
                    case self::STYLE_TITLE:
                        return $entry->rename((new Convert($entry->name()))->toTitle());
                    case self::STYLE_SENTENCE:
                        return $entry->rename((new Convert($entry->name()))->toSentence());

                    default:
                        throw new RuntimeException("Unrecognized style {$this->style}, please use one of following: " . \implode(', ', $this->styles));
                }
            });
        };

        return $rows->map($rowTransformer);
    }
}
