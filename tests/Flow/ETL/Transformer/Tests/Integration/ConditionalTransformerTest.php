<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Integration;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ChainTransformer;
use Flow\ETL\Transformer\Condition\Opposite;
use Flow\ETL\Transformer\Condition\ValidValue;
use Flow\ETL\Transformer\ConditionalTransformer;
use Flow\ETL\Transformer\StaticEntryTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ConditionalTransformerTest extends TestCase
{
    public function test_symfony_filter_integration() : void
    {
        $rows = new Rows(
            Row::create(new Row\Entry\StringEntry('email', '')),
            Row::create(new Row\Entry\StringEntry('email', 'not_email')),
            Row::create(new Row\Entry\StringEntry('email', 'email@email.com'))
        );

        $transformer = new ChainTransformer(
            new ConditionalTransformer(
                new ValidValue(
                    'email',
                    new ValidValue\SymfonyValidator([
                        new NotBlank(),
                        new Email(),
                    ])
                ),
                new StaticEntryTransformer(new Row\Entry\BooleanEntry('valid', true))
            ),
            new ConditionalTransformer(
                new Opposite(
                    new ValidValue(
                        'email',
                        new ValidValue\SymfonyValidator([
                            new NotBlank(),
                            new Email(),
                        ])
                    )
                ),
                new StaticEntryTransformer(new Row\Entry\BooleanEntry('valid', false))
            )
        );

        $this->assertSame(
            [
                [
                    'email' => '',
                    'valid' => false,
                ],
                [
                    'email' => 'not_email',
                    'valid' => false,
                ],
                [
                    'email' => 'email@email.com',
                    'valid' => true,
                ],
            ],
            $transformer->transform($rows)->toArray()
        );
    }
}
