<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Accessor;

use Flow\ETL\Exception\InvalidArgumentException;

final class ArrayAccessor
{
    /**
     * @param array<mixed> $array
     * @param string $path
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public static function value(array $array, string $path)
    {
        if (\count($array) === 0) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Path "%s" does not exists in array "%s".',
                    $path,
                    \preg_replace('/\s+/', '', \trim(\var_export($array, true)))
                )
            );
        }

        $pathSteps = \explode('.', $path);

        $arraySlice = $array;

        foreach ($pathSteps as $step) {
            if (!\array_key_exists($step, $arraySlice)) {
                throw new InvalidArgumentException(
                    \sprintf(
                        'Path "%s" does not exists in array "%s".',
                        $path,
                        \preg_replace('/\s+/', '', \trim(\var_export($array, true)))
                    )
                );
            }

            /** @var array<mixed> $arraySlice */
            $arraySlice = $arraySlice[$step];
        }

        return $arraySlice;
    }

    /**
     * @param array<mixed> $array
     * @param string $path
     *
     * @return bool
     */
    public static function pathExists(array $array, string $path) : bool
    {
        try {
            self::value($array, $path);

            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}
