<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\User\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\User\Communication\Form\ActivateUserForm;
use Spryker\Zed\User\Communication\Form\DataProvider\UserFormDataProvider;
use Spryker\Zed\User\Communication\Form\DataProvider\UserUpdateFormDataProvider;
use Spryker\Zed\User\Communication\Form\DeactivateUserForm;
use Spryker\Zed\User\Communication\Form\ResetPasswordForm;
use Spryker\Zed\User\Communication\Form\UserDeleteConfirmForm;
use Spryker\Zed\User\Communication\Form\UserForm;
use Spryker\Zed\User\Communication\Form\UserUpdateForm;
use Spryker\Zed\User\Communication\Table\PluginExecutor\UserTablePluginExecutor;
use Spryker\Zed\User\Communication\Table\PluginExecutor\UserTablePluginExecutorInterface;
use Spryker\Zed\User\Communication\Table\UsersTable;
use Spryker\Zed\User\UserDependencyProvider;
use Symfony\Component\Form\FormInterface;

/**
 * @method \Spryker\Zed\User\Persistence\UserQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\User\UserConfig getConfig()
 * @method \Spryker\Zed\User\Business\UserFacadeInterface getFacade()
 * @method \Spryker\Zed\User\Persistence\UserRepositoryInterface getRepository()
 */
class UserCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createResetPasswordForm()
    {
        return $this->getFormFactory()->create(ResetPasswordForm::class);
    }

    /**
     * @return \Spryker\Zed\User\Communication\Table\UsersTable
     */
    public function createUserTable()
    {
        return new UsersTable(
            $this->getQueryContainer(),
            $this->getProvidedDependency(UserDependencyProvider::SERVICE_DATE_FORMATTER),
            $this->createUserTablePluginExecutor(),
        );
    }

    /**
     * @return \Spryker\Zed\User\Communication\Table\PluginExecutor\UserTablePluginExecutorInterface
     */
    public function createUserTablePluginExecutor(): UserTablePluginExecutorInterface
    {
        return new UserTablePluginExecutor(
            $this->getUserTableActionExpanderPlugins(),
            $this->getUserTableConfigExpanderPlugins(),
            $this->getUserTableDataExpanderPlugins(),
        );
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createUserForm(array $data = [], array $options = [])
    {
        return $this->getFormFactory()->create(UserForm::class, $data, $options);
    }

    /**
     * @return array<\Spryker\Zed\UserExtension\Dependency\Plugin\UserFormExpanderPluginInterface>
     */
    public function getFormExpanderPlugins(): array
    {
        return $this->getProvidedDependency(UserDependencyProvider::PLUGINS_USER_FORM_EXPANDER);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createUpdateUserForm(array $data = [], array $options = [])
    {
        return $this->getFormFactory()->create(UserUpdateForm::class, $data, $options);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getUserDeleteConfirmForm(): FormInterface
    {
        return $this->getFormFactory()->create(UserDeleteConfirmForm::class);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createActivateUserForm(): FormInterface
    {
        return $this->getFormFactory()->create(ActivateUserForm::class);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createDeactivateUserForm(): FormInterface
    {
        return $this->getFormFactory()->create(DeactivateUserForm::class);
    }

    /**
     * @return \Spryker\Zed\User\Communication\Form\DataProvider\UserFormDataProvider
     */
    public function createUserFormDataProvider()
    {
        /** @var \Spryker\Zed\User\Business\UserFacade $userFacade */
        $userFacade = $this->getFacade();

        return new UserFormDataProvider($this->getGroupPlugin(), $userFacade);
    }

    /**
     * @return \Spryker\Zed\User\Communication\Form\DataProvider\UserUpdateFormDataProvider
     */
    public function createUserUpdateFormDataProvider()
    {
        /** @var \Spryker\Zed\User\Business\UserFacade $userFacade */
        $userFacade = $this->getFacade();

        return new UserUpdateFormDataProvider($this->getGroupPlugin(), $userFacade);
    }

    /**
     * @return \Spryker\Zed\User\Dependency\Plugin\GroupPluginInterface
     */
    public function getGroupPlugin()
    {
        return $this->getProvidedDependency(UserDependencyProvider::PLUGIN_GROUP);
    }

    /**
     * @return array<\Spryker\Zed\UserExtension\Dependency\Plugin\UserTableActionExpanderPluginInterface>
     */
    protected function getUserTableActionExpanderPlugins(): array
    {
        return $this->getProvidedDependency(UserDependencyProvider::PLUGINS_USER_TABLE_ACTION_EXPANDER);
    }

    /**
     * @return array<\Spryker\Zed\UserExtension\Dependency\Plugin\UserTableConfigExpanderPluginInterface>
     */
    protected function getUserTableConfigExpanderPlugins(): array
    {
        return $this->getProvidedDependency(UserDependencyProvider::PLUGINS_USER_TABLE_CONFIG_EXPANDER);
    }

    /**
     * @return array<\Spryker\Zed\UserExtension\Dependency\Plugin\UserTableDataExpanderPluginInterface>
     */
    protected function getUserTableDataExpanderPlugins(): array
    {
        return $this->getProvidedDependency(UserDependencyProvider::PLUGINS_USER_TABLE_DATA_EXPANDER);
    }
}
