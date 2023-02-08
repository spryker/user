<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\User\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\User\Communication\Form\Constraints\CurrentPassword;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

/**
 * @method \Spryker\Zed\User\Business\UserFacadeInterface getFacade()
 * @method \Spryker\Zed\User\Communication\UserCommunicationFactory getFactory()
 * @method \Spryker\Zed\User\Persistence\UserQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\User\UserConfig getConfig()
 * @method \Spryker\Zed\User\Persistence\UserRepositoryInterface getRepository()
 */
class ResetPasswordForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_CURRENT_PASSWORD = 'current_password';

    /**
     * @var string
     */
    public const FIELD_PASSWORD = 'password';

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'reset_password';
    }

    /**
     * @deprecated Use {@link getBlockPrefix()} instead.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addCurrentPasswordField($builder)
            ->addPasswordField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCurrentPasswordField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CURRENT_PASSWORD, PasswordType::class, [
            'label' => 'Current password',
            'constraints' => [
                new NotBlank(),
                new CurrentPassword([
                    'userFacade' => $this->getFacade(),
                ]),
            ],
            'attr' => ['autocomplete' => 'off'],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPasswordField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_PASSWORD, RepeatedType::class, [
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => $this->getConfig()->getUserPasswordMinLength(),
                    'max' => $this->getConfig()->getUserPasswordMaxLength(),
                ]),
                new NotCompromisedPassword(),
            ],
            'invalid_message' => 'The password fields must match.',
            'first_options' => ['label' => 'Password', 'attr' => ['autocomplete' => 'off']],
            'second_options' => ['label' => 'Repeat Password', 'attr' => ['autocomplete' => 'off']],
            'required' => true,
            'type' => PasswordType::class,
        ]);

        return $this;
    }
}
