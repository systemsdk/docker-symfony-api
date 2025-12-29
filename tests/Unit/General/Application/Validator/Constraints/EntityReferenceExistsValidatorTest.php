<?php

declare(strict_types=1);

namespace App\Tests\Unit\General\Application\Validator\Constraints;

use App\General\Application\Validator\Constraints\EntityReferenceExists;
use App\General\Application\Validator\Constraints\EntityReferenceExistsValidator;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Tests\Unit\General\Application\Validator\Constraints\Src\TestEntityReference;
use App\Tool\Application\Validator\Constraints\Language;
use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Throwable;

use function sprintf;

/**
 * @package App\Tests
 */
class EntityReferenceExistsValidatorTest extends TestCase
{
    /**
     * @throws Throwable
     */
    #[TestDox('Test that `validate` method throws exception if constraint is not `EntityReferenceExists`.')]
    #[AllowMockObjectsWithoutExpectations]
    public function testThatValidateMethodThrowsUnexpectedTypeException(): void
    {
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage(
            'Expected argument of type "' . EntityReferenceExists::class . '", "' . Language::class . '" given'
        );
        $constraint = new Language();

        new EntityReferenceExistsValidator($loggerMock)->validate('', $constraint);
    }

    /**
     * @param string|stdClass|array<mixed> $value
     */
    #[DataProvider('dataProviderTestThatValidateMethodThrowsUnexpectedValueException')]
    #[TestDox('Test that `validate` method throws `$expectedMessage` with `$value` using entity class `$entityClass`.')]
    #[AllowMockObjectsWithoutExpectations]
    public function testThatValidateMethodThrowsUnexpectedValueException(
        string|stdClass|array $value,
        string $entityClass,
        string $expectedMessage
    ): void {
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage($expectedMessage);
        $constraint = new EntityReferenceExists();
        $constraint->entityClass = $entityClass;

        new EntityReferenceExistsValidator($loggerMock)->validate($value, $constraint);
    }

    #[TestDox('Test that `validate` method throws an exception if value is `stdClass`.')]
    #[AllowMockObjectsWithoutExpectations]
    public function testThatValidateMethodThrowsUnexpectedValueExceptionWhenValueIsNotEntityInterface(): void
    {
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage(
            sprintf('Expected argument of type "%s", "stdClass" given', EntityInterface::class)
        );
        $constraint = new EntityReferenceExists();
        $constraint->entityClass = stdClass::class;

        (new EntityReferenceExistsValidator($loggerMock))->validate(new stdClass(), $constraint);
    }

    /**
     * @throws Throwable
     */
    #[TestDox("Test that `validate` method doesn't call `Context` nor `Logger` methods with happy path.")]
    #[AllowMockObjectsWithoutExpectations]
    public function testThatContextAndLoggerMethodsAreNotCalledWithinHappyPath(): void
    {
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $contextMock = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $contextMock
            ->expects($this->never())
            ->method(self::anything());
        $contextMock
            ->expects($this->never())
            ->method(self::anything());
        $constraint = new EntityReferenceExists();
        $constraint->entityClass = TestEntityReference::class;

        $validator = new EntityReferenceExistsValidator($loggerMock);
        $validator->initialize($contextMock);
        $validator->validate(new TestEntityReference(), $constraint);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `validate` method calls expected `Context` and `Logger` service methods with unhappy path.')]
    #[AllowMockObjectsWithoutExpectations]
    public function testThatContextAndLoggerMethodsAreCalledIfEntityReferenceIsNotValidEntity(): void
    {
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $contextMock = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $violation
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturn($violation);
        $violation
            ->expects($this->once())
            ->method('setCode')
            ->with('64888b5e-bded-449b-82ed-0cc1f73df14d')
            ->willReturn($violation);
        $violation
            ->expects($this->once())
            ->method('addViolation');
        $contextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->with('Invalid id value "{{ id }}" given for entity "{{ entity }}".')
            ->willReturn($violation);
        $loggerMock
            ->expects($this->once())
            ->method('error')
            ->with('Entity not found');
        $constraint = new EntityReferenceExists();
        $constraint->entityClass = TestEntityReference::class;

        $validator = new EntityReferenceExistsValidator($loggerMock);
        $validator->initialize($contextMock);
        $validator->validate(new TestEntityReference(true), $constraint);
    }

    /**
     * @return Generator<array{0: string|stdClass|array<mixed>, 1: string, 2: string}>
     */
    public static function dataProviderTestThatValidateMethodThrowsUnexpectedValueException(): Generator
    {
        yield ['', stdClass::class, 'Expected argument of type "stdClass", "string" given'];

        yield [
            new stdClass(),
            EntityInterface::class,
            sprintf('Expected argument of type "%s", "stdClass" given', EntityInterface::class),
        ];

        yield [
            [''],
            EntityInterface::class,
            sprintf('Expected argument of type "%s", "string" given', EntityInterface::class),
        ];

        yield [
            [new stdClass()],
            EntityInterface::class,
            sprintf('Expected argument of type "%s", "stdClass" given', EntityInterface::class),
        ];
    }
}
