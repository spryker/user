<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\User\Business;

use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\UserCollectionTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\User\Business\UserBusinessFactory getFactory()
 * @method \Spryker\Zed\User\Persistence\UserRepositoryInterface getRepository()
 */
class UserFacade extends AbstractFacade implements UserFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return void
     */
    public function install()
    {
        $this->getFactory()
            ->createInstallerModel()
            ->install();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $username
     *
     * @return bool
     */
    public function hasUserByUsername($username)
    {
        return $this->getFactory()
            ->createUserModel()
            ->hasUserByUsername($username);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $username
     *
     * @return bool
     */
    public function hasActiveUserByUsername($username)
    {
        return $this->getFactory()
            ->createUserModel()
            ->hasActiveUserByUsername($username);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\User\Business\UserFacade::getUserCollection()} instead.
     *
     * @param string $username
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getUserByUsername($username)
    {
        return $this->getFactory()
            ->createUserModel()
            ->getUserByUsername($username);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\User\Business\UserFacade::getUserCollection()} instead.
     *
     * @param int $idUser
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getUserById($idUser)
    {
        return $this->getFactory()
            ->createUserModel()
            ->getUserById($idUser);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\User\Business\UserFacade::getUserCollection()} instead.
     *
     * @param int $idUser
     *
     * @return \Generated\Shared\Transfer\UserTransfer|null
     */
    public function findUserById(int $idUser): ?UserTransfer
    {
        return $this->getFactory()
            ->createUserModel()
            ->findUserById($idUser);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\User\Business\UserFacade::getUserCollection()} instead.
     *
     * @param \Generated\Shared\Transfer\UserCriteriaTransfer $userCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UserTransfer|null
     */
    public function findUser(UserCriteriaTransfer $userCriteriaTransfer): ?UserTransfer
    {
        return $this->getFactory()
            ->createUserModel()
            ->findUser($userCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\User\Business\UserFacade::getUserCollection()} instead.
     *
     * @param int $idUser
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getActiveUserById($idUser)
    {
        return $this->getFactory()
            ->createUserModel()
            ->getActiveUserById($idUser);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string $password
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function addUser($firstName, $lastName, $username, $password)
    {
        return $this->getFactory()
            ->createUserModel()
            ->addUser($firstName, $lastName, $username, $password);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function createUser(UserTransfer $userTransfer): UserTransfer
    {
        return $this->getFactory()
            ->createUserModel()
            ->createUser($userTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserTransfer $user
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function updateUser(UserTransfer $user)
    {
        return $this->getFactory()
            ->createUserModel()
            ->save($user);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserTransfer $user
     *
     * @return mixed
     */
    public function setCurrentUser(UserTransfer $user)
    {
        return $this->getFactory()
            ->createUserModel()
            ->setCurrentUser($user);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getCurrentUser()
    {
        return $this->getFactory()
            ->createUserModel()
            ->getCurrentUser();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return bool
     */
    public function hasCurrentUser()
    {
        return $this->getFactory()
            ->createUserModel()
            ->hasCurrentUser();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function isValidPassword($password, $hash)
    {
        return $this->getFactory()
            ->createUserModel()
            ->validatePassword($password, $hash);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserTransfer $user
     *
     * @return bool
     */
    public function isSystemUser(UserTransfer $user)
    {
        return $this->getFactory()
            ->createUserModel()
            ->isSystemUser($user);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\CollectionTransfer
     */
    public function getSystemUsers()
    {
        return $this->getFactory()
            ->createUserModel()
            ->getSystemUsers();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idUser
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function removeUser($idUser)
    {
        return $this->getFactory()
            ->createUserModel()
            ->removeUser($idUser);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idUser
     *
     * @return bool
     */
    public function activateUser($idUser)
    {
        return $this->getFactory()->createUserModel()->activateUser($idUser);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idUser
     *
     * @return bool
     */
    public function deactivateUser($idUser)
    {
        return $this->getFactory()->createUserModel()->deactivateUser($idUser);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Will be removed without replacement. Handling of user password reset mail implemented in UserPasswordReset module.
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     *
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    public function expandMailWithUserData(MailTransfer $mailTransfer): MailTransfer
    {
        return $this->getFactory()
            ->createMailExpander()
            ->expandMailWithUserData($mailTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCriteriaTransfer $userCriteriaTransfer
     *
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return \Generated\Shared\Transfer\UserCollectionTransfer
     */
    public function getUserCollection(UserCriteriaTransfer $userCriteriaTransfer): UserCollectionTransfer
    {
        return $this->getFactory()
            ->createUserReader()
            ->getUserCollection($userCriteriaTransfer);
    }
}
