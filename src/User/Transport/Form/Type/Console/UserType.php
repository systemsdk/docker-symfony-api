<?php

declare(strict_types=1);

namespace App\User\Transport\Form\Type\Console;

use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\General\Transport\Form\Type\Interfaces\FormTypeLabelInterface;
use App\General\Transport\Form\Type\Traits\AddBasicFieldToForm;
use App\Tool\Application\Service\LocalizationService;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use App\User\Application\DTO\User\User as UserDto;
use App\User\Application\Resource\UserGroupResource;
use App\User\Transport\Form\DataTransformer\UserGroupTransformer;
use App\User\Transport\Form\Type\Traits\UserGroupChoices;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Throwable;

use function array_map;

/**
 * @package App\User
 */
class UserType extends AbstractType
{
    use AddBasicFieldToForm;
    use UserGroupChoices;

    /**
     * Base form fields
     *
     * @var array<int, array<int, mixed>>
     */
    private static array $formFields = [
        [
            'username',
            Type\TextType::class,
            [
                FormTypeLabelInterface::LABEL => 'Username',
                FormTypeLabelInterface::REQUIRED => true,
                FormTypeLabelInterface::EMPTY_DATA => '',
            ],
        ],
        [
            'firstName',
            Type\TextType::class,
            [
                FormTypeLabelInterface::LABEL => 'First name',
                FormTypeLabelInterface::REQUIRED => true,
                FormTypeLabelInterface::EMPTY_DATA => '',
            ],
        ],
        [
            'lastName',
            Type\TextType::class,
            [
                FormTypeLabelInterface::LABEL => 'Last name',
                FormTypeLabelInterface::REQUIRED => true,
                FormTypeLabelInterface::EMPTY_DATA => '',
            ],
        ],
        [
            'email',
            Type\EmailType::class,
            [
                FormTypeLabelInterface::LABEL => 'Email address',
                FormTypeLabelInterface::REQUIRED => true,
                FormTypeLabelInterface::EMPTY_DATA => '',
            ],
        ],
        [
            'password',
            Type\RepeatedType::class,
            [
                FormTypeLabelInterface::TYPE => Type\PasswordType::class,
                FormTypeLabelInterface::REQUIRED => true,
                FormTypeLabelInterface::FIRST_NAME => 'password1',
                FormTypeLabelInterface::FIRST_OPTIONS => [
                    FormTypeLabelInterface::LABEL => 'Password',
                ],
                FormTypeLabelInterface::SECOND_NAME => 'password2',
                FormTypeLabelInterface::SECOND_OPTIONS => [
                    FormTypeLabelInterface::LABEL => 'Repeat password',
                ],
            ],
        ],
    ];

    public function __construct(
        private readonly UserGroupResource $userGroupResource,
        private readonly UserGroupTransformer $userGroupTransformer,
        private readonly LocalizationService $localization,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $this->addBasicFieldToForm($builder, self::$formFields);
        $this->addLocalizationFieldsToForm($builder);

        $builder
            ->add(
                'userGroups',
                Type\ChoiceType::class,
                [
                    FormTypeLabelInterface::CHOICES => $this->getUserGroupChoices(),
                    FormTypeLabelInterface::REQUIRED => true,
                    FormTypeLabelInterface::EMPTY_DATA => '',
                    'multiple' => true,
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
    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => UserDto::class,
        ]);
    }

    /**
     * @throws Throwable
     */
    private function addLocalizationFieldsToForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add(
                'language',
                Type\EnumType::class,
                [
                    FormTypeLabelInterface::CLASS_NAME => Language::class,
                    FormTypeLabelInterface::LABEL => 'Language',
                    FormTypeLabelInterface::REQUIRED => true,
                    FormTypeLabelInterface::EMPTY_DATA => Language::getDefault(),
                ],
            );
        $builder
            ->add(
                'locale',
                Type\EnumType::class,
                [
                    FormTypeLabelInterface::CLASS_NAME => Locale::class,
                    FormTypeLabelInterface::LABEL => 'Locale',
                    FormTypeLabelInterface::REQUIRED => true,
                    FormTypeLabelInterface::EMPTY_DATA => Locale::getDefault(),
                ],
            );
        $builder
            ->add(
                'timezone',
                Type\ChoiceType::class,
                [
                    FormTypeLabelInterface::LABEL => 'Timezone',
                    FormTypeLabelInterface::REQUIRED => true,
                    FormTypeLabelInterface::EMPTY_DATA => LocalizationServiceInterface::DEFAULT_TIMEZONE,
                    FormTypeLabelInterface::CHOICES => $this->getTimeZoneChoices(),
                ],
            );
    }

    /**
     * Method to get choices array for time zones.
     *
     * @throws Throwable
     *
     * @return array<string, string>
     */
    private function getTimeZoneChoices(): array
    {
        // Initialize output
        $choices = [];
        $iterator = static function (array $timezone) use (&$choices): void {
            $choices[$timezone['value'] . ' (' . $timezone['offset'] . ')'] = $timezone['identifier'];
        };
        array_map($iterator, $this->localization->getFormattedTimezones());

        return $choices;
    }
}
