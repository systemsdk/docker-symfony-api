<?php
declare(strict_types = 1);
/**
 * /src/Form/Type/Console/ApiKeyType.php
 */

namespace App\Form\Type\Console;

use Symfony\Component\Form\AbstractType;
use App\Form\Type\Traits\AddBasicFieldToForm;
use App\Form\Type\Traits\UserGroupChoices;
use App\DTO\ApiKey\ApiKey;
use Symfony\Component\Form\Extension\Core\Type;
use App\Form\Type\Interfaces\FormTypeLabelInterface;
use App\Form\DataTransformer\UserGroupTransformer;
use App\Resource\UserGroupResource;
use Throwable;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ApiKeyType
 *
 * @package App\Form\Type\Console
 */
class ApiKeyType extends AbstractType
{
    // Traits
    use AddBasicFieldToForm;
    use UserGroupChoices;

    /**
     * Base form fields
     *
     * @var array<int, array<int, mixed>>
     */
    private static array $formFields = [
        [
            'description',
            Type\TextType::class,
            [
                FormTypeLabelInterface::LABEL => 'Description',
                FormTypeLabelInterface::REQUIRED => true,
                FormTypeLabelInterface::EMPTY_DATA => '',
            ],
        ],
    ];
    private UserGroupTransformer $userGroupTransformer;

    /**
     * Constructor
     *
     * @param UserGroupResource    $userGroupResource
     * @param UserGroupTransformer $userGroupTransformer
     */
    public function __construct(UserGroupResource $userGroupResource, UserGroupTransformer $userGroupTransformer)
    {
        $this->userGroupResource = $userGroupResource;
        $this->userGroupTransformer = $userGroupTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array              $options
     *
     * @throws Throwable
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $this->addBasicFieldToForm($builder, self::$formFields);

        $builder
            ->add(
                'userGroups',
                Type\ChoiceType::class,
                [
                    'choices' => $this->getUserGroupChoices(),
                    'multiple' => true,
                    'required' => true,
                    'empty_data' => '',
                ]
            );

        $builder->get('userGroups')->addModelTransformer($this->userGroupTransformer);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     *
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ApiKey::class,
        ]);
    }
}
