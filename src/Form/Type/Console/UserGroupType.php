<?php

declare(strict_types=1);

namespace App\Form\Type\Console;

use App\DTO\UserGroup\UserGroup;
use App\Entity\Role as RoleEntity;
use App\Form\DataTransformer\RoleTransformer;
use App\Form\Type\Interfaces\FormTypeLabelInterface;
use App\Form\Type\Traits\AddBasicFieldToForm;
use App\Resource\RoleResource;
use App\Security\RolesService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Throwable;

/**
 * Class UserGroupType
 *
 * @package App\Form\Type\Console
 */
class UserGroupType extends AbstractType
{
    use AddBasicFieldToForm;

    /**
     * Base form fields
     *
     * @var array<int, array<int, mixed>>
     */
    private static array $formFields = [
        [
            'name',
            Type\TextType::class,
            [
                FormTypeLabelInterface::LABEL => 'Group name',
                FormTypeLabelInterface::REQUIRED => true,
                FormTypeLabelInterface::EMPTY_DATA => '',
            ],
        ],
    ];

    public function __construct(
        private RolesService $rolesService,
        private RoleResource $roleResource,
        private RoleTransformer $roleTransformer,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $this->addBasicFieldToForm($builder, self::$formFields);
        $builder
            ->add(
                'role',
                Type\ChoiceType::class,
                [
                    FormTypeLabelInterface::LABEL => 'Role',
                    FormTypeLabelInterface::CHOICES => $this->getRoleChoices(),
                    FormTypeLabelInterface::REQUIRED => true,
                ],
            );
        $builder->get('role')->addModelTransformer($this->roleTransformer);
    }

    /**
     * Configures the options for this type.
     *
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => UserGroup::class,
        ]);
    }

    /**
     * Method to get choices array for user groups.
     *
     * @throws Throwable
     *
     * @return array<string, string>
     */
    public function getRoleChoices(): array
    {
        // Initialize output
        $choices = [];
        $iterator = function (RoleEntity $role) use (&$choices): void {
            $choices[$this->rolesService->getRoleLabel($role->getId())] = $role->getId();
        };

        array_map($iterator, $this->roleResource->find());

        return $choices;
    }
}
