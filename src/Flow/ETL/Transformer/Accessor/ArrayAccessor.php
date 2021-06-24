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
        /** @var array<string> $takenSteps */
        $takenSteps = [];

        foreach ($pathSteps as $step) {
            $takenSteps[] = $step;

            if ($step === '*') {
                $stepsLeft = \array_slice($pathSteps, \count($takenSteps), \count($pathSteps));
                $results = [];

                foreach (\array_keys($arraySlice) as $key) {
                    /**
                     * @psalm-suppress MixedAssignment
                     * @psalm-suppress MixedArgument
                     */
                    $results[] = self::value($arraySlice[$key], \implode('.', $stepsLeft));
                }

                return $results;
            }

            if ($step === '?*') {
                $stepsLeft = \array_diff($pathSteps, $takenSteps);
                $results = [];

                foreach (\array_keys($arraySlice) as $key) {
                    /**
                     * @psalm-suppress MixedArgument
                     */
                    if (self::pathExists($arraySlice[$key], \implode('.', $stepsLeft))) {
                        /**
                         * @psalm-suppress MixedAssignment
                         * @psalm-suppress MixedArgument
                         */
                        $results[] = self::value($arraySlice[$key], \implode('.', $stepsLeft));
                    }
                }

                return $results;
            }

            if (\in_array($step, ['\\*', '\\?*'], true)) {
                $step = \ltrim($step, '\\');
                \array_pop($takenSteps);
                $takenSteps[] = $step;
            }

            $nullSafe = false;

            if (\strpos($step, '?') === 0 && $step !== '?*') {
                $nullSafe = true;
                $step = \ltrim($step, '?');
                \array_pop($takenSteps);
                $takenSteps[] = $step;
            }

            if (!\array_key_exists($step, $arraySlice)) {
                if (!$nullSafe) {
                    throw new InvalidArgumentException(
                        \sprintf(
                            'Path "%s" does not exists in array "%s".',
                            $path,
                            \preg_replace('/\s+/', '', \trim(\var_export($array, true)))
                        )
                    );
                }

                return null;
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
