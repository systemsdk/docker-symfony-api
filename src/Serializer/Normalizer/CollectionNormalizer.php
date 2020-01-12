<?php
declare(strict_types = 1);
/**
 * /src/Serializer/CollectionNormalizer.php
 */

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class CollectionNormalizer
 *
 * @package App\Serializer
 */
class CollectionNormalizer implements NormalizerInterface
{
    private ObjectNormalizer $normalizer;

    /**
     * Constructor
     *
     * @param ObjectNormalizer $normalizer
     */
    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param Collection|ArrayCollection|mixed $collection
     * @param string|null                      $format
     * @param array<array-key, mixed>          $context
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     *
     * @return array
     */
    public function normalize($collection, $format = null, array $context = []): array
    {
        $output = [];

        foreach ($collection as $value) {
            $output[] = $this->normalizer->normalize($value, $format, $context);
        }

        return $output;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $format === 'json' && is_object($data) && $data instanceof Collection && is_object($data->first());
    }
}
