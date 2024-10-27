<?php

declare(strict_types=1);

namespace App\Tool\Transport\Serializer;

use App\Tool\Domain\Message\ExternalMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface as MessengerSerializerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

use function sprintf;

/**
 * @package App\Tool
 */
class ExternalMessageSerializer implements MessengerSerializerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @param array<string, mixed> $encodedEnvelope
     *
     * @throws MessageDecodingFailedException
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        try {
            /** @var ExternalMessage $message */
            $message = $this->serializer->deserialize($body, ExternalMessage::class, 'json');
        } catch (Throwable $exception) {
            $this->logger->error(
                $exception,
                [
                    'encodedEnvelope' => $encodedEnvelope,
                ]
            );

            throw new MessageDecodingFailedException('Failed to decode messages_external queue message');
        }

        // in case of redelivery, unserialize any stamps
        $stamps = [];
        if (isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        return new Envelope($message, $stamps);
    }

    /**
     * This is called if a message is redelivered for "retry"
     *
     * @throws Throwable
     *
     * @return array<string, string|array<string, string>>
     */
    public function encode(Envelope $envelope): array
    {
        // more info here: https://github.com/symfony/symfony/pull/31471
        $envelope = $envelope->withoutStampsOfType(NonSendableStampInterface::class);
        /** @var ExternalMessage|object $message */
        $message = $envelope->getMessage();

        if (!$message instanceof ExternalMessage) {
            $this->logger->error(sprintf('Expected class %s but got %s', ExternalMessage::class, $message::class));

            throw new UnrecoverableMessageHandlingException('Unsupported message class');
        }

        $allStamps = [];
        foreach ($envelope->all() as $stampKey => $stamps) {
            if ($stampKey === ErrorDetailsStamp::class) {
                // this header could be huge and drasticaly increase a size of a message
                continue;
            }

            $allStamps = array_merge($allStamps, $stamps);
        }

        return [
            'body' => $this->serializer->serialize($message, 'json'),
            'headers' => [
                // store stamps as a header - to be read in decode()
                'stamps' => serialize($allStamps),
            ],
        ];
    }
}
