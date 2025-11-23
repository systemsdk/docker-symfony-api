<?php

declare(strict_types=1);

namespace App\Tests\Unit\General\Application\Validator\Constraints\Src;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityNotFoundException;
use Override;

/**
 * @package App\Tests
 */
class TestEntityReference implements EntityInterface
{
    public function __construct(
        private ?bool $throwException = null
    ) {
        $this->throwException ??= false;
    }

    #[Override]
    public function getId(): string
    {
        return 'xxx';
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Override]
    public function getCreatedAt(): ?DateTimeImmutable
    {
        if ($this->throwException) {
            throw new EntityNotFoundException('Entity not found');
        }

        return null;
    }
}
