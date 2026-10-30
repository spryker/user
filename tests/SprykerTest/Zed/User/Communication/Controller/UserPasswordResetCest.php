<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\User\Communication\Controller;

use Generated\Shared\Transfer\UserTransfer;
use Spryker\Client\SecurityBlocker\SecurityBlockerDependencyProvider;
use Spryker\Client\SecurityBlocker\SecurityBlockerFactory;
use Spryker\Zed\Messenger\Communication\Plugin\Application\MessengerApplicationPlugin;
use Spryker\Zed\User\UserConfig;
use SprykerTest\Client\SecurityBlocker\Helper\InMemorySecurityBlockerRedisClient;
use SprykerTest\Zed\User\UserCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group User
 * @group Communication
 * @group Controller
 * @group UserPasswordResetCest
 * Add your own group annotations below this line
 */
class UserPasswordResetCest
{
    protected const string URL_PASSWORD_RESET = '/user/edit/password-reset';

    protected const string FORM_NAME = 'reset_password';

    protected const string PASSWORD = 'Change!23456';

    protected const string NEW_PASSWORD = 'NewPass!23456';

    protected const string MESSAGE_PASSWORD_CHANGE_LIMIT_EXCEEDED = 'You reached the limit of password change attempts. Please try again later.';

    protected const string WRONG_CURRENT_PASSWORD = 'Wrong!23456';

    protected const string MESSAGE_INCORRECT_CURRENT_PASSWORD = 'Incorrect current password provided.';

    public function passwordResetChangesPasswordForEveryAttemptWithinAttemptLimit(UserCommunicationTester $i): void
    {
        $this->setUpPasswordResetDependencies($i);
        $userTransfer = $this->haveCurrentUser($i);

        $currentPassword = $this->changePasswordUntilAttemptLimitIsReached($i);

        $i->assertTrue($this->isCurrentPassword($i, $userTransfer, $currentPassword));
        $i->assertNotContains(static::MESSAGE_PASSWORD_CHANGE_LIMIT_EXCEEDED, $this->getErrorMessages($i));
    }

    public function passwordResetKeepsPasswordAndShowsErrorWhenAttemptLimitIsExceeded(UserCommunicationTester $i): void
    {
        $this->setUpPasswordResetDependencies($i);
        $userTransfer = $this->haveCurrentUser($i);
        $currentPassword = $this->changePasswordUntilAttemptLimitIsReached($i);

        $this->submitPasswordResetForm($i, $currentPassword, $this->getOtherPassword($currentPassword));

        $i->assertTrue($this->isCurrentPassword($i, $userTransfer, $currentPassword));
        $i->assertContains(static::MESSAGE_PASSWORD_CHANGE_LIMIT_EXCEEDED, $this->getErrorMessages($i));
    }

    public function passwordResetCountsAttemptWithWrongCurrentPassword(UserCommunicationTester $i): void
    {
        $this->setUpPasswordResetDependencies($i);
        $userTransfer = $this->haveCurrentUser($i);
        $this->submitWrongCurrentPasswordUntilAttemptLimitIsReached($i);

        $this->submitPasswordResetForm($i, static::PASSWORD, static::NEW_PASSWORD);

        $i->assertTrue($this->isCurrentPassword($i, $userTransfer, static::PASSWORD));
        $i->assertContains(static::MESSAGE_PASSWORD_CHANGE_LIMIT_EXCEEDED, $this->getErrorMessages($i));
    }

    public function passwordResetShowsNoCurrentPasswordErrorWhenAttemptLimitIsExceeded(UserCommunicationTester $i): void
    {
        $this->setUpPasswordResetDependencies($i);
        $this->haveCurrentUser($i);
        $this->submitWrongCurrentPasswordUntilAttemptLimitIsReached($i);

        $this->submitPasswordResetForm($i, static::WRONG_CURRENT_PASSWORD, static::NEW_PASSWORD);

        $i->dontSee(static::MESSAGE_INCORRECT_CURRENT_PASSWORD);
        $i->assertContains(static::MESSAGE_PASSWORD_CHANGE_LIMIT_EXCEEDED, $this->getErrorMessages($i));
    }

    protected function setUpPasswordResetDependencies(UserCommunicationTester $i): void
    {
        $i->setDependency(SecurityBlockerDependencyProvider::CLIENT_REDIS, new InMemorySecurityBlockerRedisClient(), SecurityBlockerFactory::class);
        $i->addApplicationPlugin(new MessengerApplicationPlugin());
    }

    protected function changePasswordUntilAttemptLimitIsReached(UserCommunicationTester $i): string
    {
        $passwordChangeAttemptLimit = (new UserConfig())->getPasswordChangeAttemptLimit();
        $currentPassword = static::PASSWORD;

        for ($attempt = 0; $attempt < $passwordChangeAttemptLimit; $attempt++) {
            $newPassword = $this->getOtherPassword($currentPassword);
            $this->submitPasswordResetForm($i, $currentPassword, $newPassword);
            $currentPassword = $newPassword;
        }

        return $currentPassword;
    }

    protected function submitWrongCurrentPasswordUntilAttemptLimitIsReached(UserCommunicationTester $i): void
    {
        $passwordChangeAttemptLimit = (new UserConfig())->getPasswordChangeAttemptLimit();

        for ($attempt = 0; $attempt < $passwordChangeAttemptLimit; $attempt++) {
            $this->submitPasswordResetForm($i, static::WRONG_CURRENT_PASSWORD, static::NEW_PASSWORD);
        }
    }

    protected function getOtherPassword(string $password): string
    {
        return $password === static::PASSWORD ? static::NEW_PASSWORD : static::PASSWORD;
    }

    protected function haveCurrentUser(UserCommunicationTester $i): UserTransfer
    {
        $userTransfer = $i->haveUser([UserTransfer::PASSWORD => static::PASSWORD]);
        $i->getLocator()->user()->facade()->setCurrentUser($userTransfer);

        return $userTransfer;
    }

    protected function submitPasswordResetForm(UserCommunicationTester $i, string $currentPassword, string $newPassword): void
    {
        $i->amOnPage(static::URL_PASSWORD_RESET);
        $i->submitForm(['name' => static::FORM_NAME], [
            'reset_password[current_password]' => $currentPassword,
            'reset_password[password][first]' => $newPassword,
            'reset_password[password][second]' => $newPassword,
        ]);
    }

    protected function isCurrentPassword(UserCommunicationTester $i, UserTransfer $userTransfer, string $password): bool
    {
        $userFacade = $i->getLocator()->user()->facade();
        $passwordHash = $userFacade->getUserById($userTransfer->getIdUserOrFail())->getPasswordOrFail();

        return $userFacade->isValidPassword($password, $passwordHash);
    }

    /**
     * @return array<string>
     */
    protected function getErrorMessages(UserCommunicationTester $i): array
    {
        return $i->getLocator()->messenger()->facade()->getStoredMessages()->getErrorMessages();
    }
}
