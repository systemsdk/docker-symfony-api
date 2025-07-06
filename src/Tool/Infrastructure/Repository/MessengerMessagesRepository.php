<?php

declare(strict_types=1);

namespace App\Tool\Infrastructure\Repository;

use App\Tool\Domain\Repository\Interfaces\MessengerMessagesRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @package App\Tool
 */
class MessengerMessagesRepository implements MessengerMessagesRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly int $messengerMessagesHistoryDays,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function cleanUp(): int
    {
        $connection = $this->em->getConnection();
        $condition = 'DATE_SUB(NOW(), INTERVAL ' . $this->messengerMessagesHistoryDays . ' DAY)';
        $statement = $connection->prepare('DELETE FROM messenger_messages WHERE created_at < ' . $condition);

        return (int)$statement->executeStatement();
    }
}
