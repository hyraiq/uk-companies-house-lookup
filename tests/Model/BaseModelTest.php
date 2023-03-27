<?php

declare(strict_types=1);

namespace Hyra\Tests\UkCompaniesHouseLookup\Model;

use Hyra\UkCompaniesHouseLookup\Dependencies;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseModelTest extends TestCase
{
    private DenormalizerInterface $serializer;

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->serializer = Dependencies::serializer();
        $this->validator  = Dependencies::validator();
    }

    /**
     * @param mixed[]|string $data
     *
     * @psalm-param class-string<T> $modelClass
     *
     * @psalm-template T of object
     *
     * @psalm-return T
     */
    protected function valid(array | string $data, string $modelClass): object
    {
        try {
            $model         = $this->denormalize($data, $modelClass);
            $exceptionList = $this->validator->validate($model);
        } catch (ExceptionInterface $e) {
            throw new \LogicException(
                \sprintf('Unable to denormalise into %s: %s', $modelClass, $e->getMessage()),
                0,
                $e
            );
        }

        $errors = \array_map(
            fn (ConstraintViolationInterface $violation) => \sprintf(
                '%s: %s',
                $violation->getPropertyPath(),
                (string) $violation->getMessage()
            ),
            \iterator_to_array($exceptionList)
        );
        static::assertSame([], $errors, 'Model should be valid');

        return $model;
    }

    /**
     * @param mixed[]|string $data
     * @param class-string   $modelClass
     */
    protected function invalid(array | string $data, string $modelClass): void
    {
        try {
            $model         = $this->denormalize($data, $modelClass);
            $exceptionList = $this->validator->validate($model);
        } catch (ExceptionInterface $e) {
            $exceptionList = [$e];
        }

        static::assertGreaterThan(0, \count($exceptionList), 'Model should be invalid');
    }

    /**
     * @param mixed[]|string $data
     *
     * @psalm-param class-string<T> $modelClass
     *
     * @psalm-template T of object
     *
     * @psalm-return T
     *
     * @throws ExceptionInterface
     */
    private function denormalize(string | array $data, string $modelClass): object
    {
        if (\is_string($data)) {
            $data = \json_decode($data, true);
        }

        /** @psalm-var T $model */
        $model = $this->serializer->denormalize($data, $modelClass);

        return $model;
    }
}
