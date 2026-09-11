<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\User\Business\Facade;

use Codeception\Test\Unit;
use ReflectionProperty;
use Spryker\Client\Session\SessionClient;
use SprykerTest\Zed\User\UserBusinessTester;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group User
 * @group Business
 * @group Facade
 * @group CurrentUserWithoutSessionTest
 * Add your own group annotations below this line
 */
class CurrentUserWithoutSessionTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\User\UserBusinessTester
     */
    public UserBusinessTester $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->removeSessionContainer();
        $this->tester->getFacade()->resetCurrentUser();
    }

    protected function tearDown(): void
    {
        $this->removeSessionContainer();
        $this->tester->getFacade()->resetCurrentUser();

        parent::tearDown();
    }

    public function testGivenNoSessionWhenSettingTheCurrentUserThenItCanBeReadBack(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();

        // Act
        $this->tester->getFacade()->setCurrentUser($userTransfer);

        // Assert
        $this->assertTrue($this->tester->getFacade()->hasCurrentUser());
        $this->assertSame(
            $userTransfer->getIdUserOrFail(),
            $this->tester->getFacade()->getCurrentUser()->getIdUser(),
        );
    }

    public function testGivenNoSessionWhenResettingTheCurrentUserThenNoUserIsActing(): void
    {
        // Arrange
        $this->tester->getFacade()->setCurrentUser($this->tester->haveUser());

        // Act
        $this->tester->getFacade()->resetCurrentUser();

        // Assert
        $this->assertFalse($this->tester->getFacade()->hasCurrentUser());
    }

    public function testGivenASessionWhenResettingTheCurrentUserThenTheSessionValueIsRemovedToo(): void
    {
        // Arrange
        (new SessionClient())->setContainer(new Session(new MockArraySessionStorage()));
        $this->tester->getFacade()->setCurrentUser($this->tester->haveUser());

        // Act
        $this->tester->getFacade()->resetCurrentUser();

        // Assert
        $this->assertFalse($this->tester->getFacade()->hasCurrentUser());
    }

    /**
     * The fallback must stay empty wherever a session accepted the user, or dropping the session -
     * a Back Office logout - would leave the process still reporting that user as the current one.
     */
    public function testGivenASessionWhenItIsDroppedThenNoUserIsActing(): void
    {
        // Arrange
        (new SessionClient())->setContainer(new Session(new MockArraySessionStorage()));
        $this->tester->getFacade()->setCurrentUser($this->tester->haveUser());

        // Act
        (new SessionClient())->setContainer(new Session(new MockArraySessionStorage()));

        // Assert
        $this->assertFalse($this->tester->getFacade()->hasCurrentUser());
    }

    protected function removeSessionContainer(): void
    {
        (new ReflectionProperty(SessionClient::class, 'container'))->setValue(null, null);
    }
}
