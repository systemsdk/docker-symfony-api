<?php

declare(strict_types=1);

namespace App\Tests\Unit\DateDimension\Domain\Entity;

use App\DateDimension\Domain\Entity\DateDimension;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Throwable;

use function floor;

/**
 * @package App\Tests
 */
class DateDimensionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    #[TestDox('Test that DateDimension entity constructor calls expected methods.')]
    public function testThatConstructorCallsExpectedMethods(): void
    {
        $dateTime = DateTimeImmutable::createFromMutable(
            (new DateTime('now', new DateTimeZone('UTC')))->setTime(9, 59, 59)
        );

        $entity = new DateDimension($dateTime);

        self::assertSame($dateTime, $entity->getDate());
        self::assertSame($dateTime->format('U'), $entity->getCreatedAt()->format('U'));
        self::assertSame((int)$dateTime->format('Y'), $entity->getYear());
        self::assertSame((int)$dateTime->format('n'), $entity->getMonth());
        self::assertSame((int)$dateTime->format('j'), $entity->getDay());
        self::assertSame((int)floor(((int)$dateTime->format('n') - 1) / 3) + 1, $entity->getQuarter());
        self::assertSame((int)$dateTime->format('W'), $entity->getWeekNumber());
        self::assertSame((int)$dateTime->format('N'), $entity->getDayNumberOfWeek());
        self::assertSame((int)$dateTime->format('z'), $entity->getDayNumberOfYear());
        self::assertSame((bool)$dateTime->format('L'), $entity->isLeapYear());
        self::assertSame((int)$dateTime->format('o'), $entity->getWeekNumberingYear());
        self::assertSame($dateTime->format('U'), $entity->getUnixTime());
    }
}
