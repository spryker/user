<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\User\BackendApi;

use ArrayObject;
use Codeception\Stub;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\UserCollectionTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\ApiPlatform\EventSubscriber\IdentityRequestSubscriber;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\User\Api\Backend\EventSubscriber\UserIdentityRequestSubscriber;
use Spryker\Glue\User\UserConfig;
use Spryker\Shared\UserExtension\Dependency\Plugin\UserIdentityCriteriaExpanderPluginInterface;
use Spryker\Zed\User\Business\UserFacadeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group User
 * @group BackendApi
 * @group UserIdentityRequestSubscriberTest
 * Add your own group annotations below this line
 */
class UserIdentityRequestSubscriberTest extends Unit
{
    protected const int ID_USER = 42;

    protected const string USER_UUID = 'd4c3b2a1-0000-4000-8000-000000000001';

    protected const string USERNAME = 'harald@spryker.com';

    protected const string STATUS_ACTIVE = 'active';

    protected const string THIRD_PARTY_SUBJECT_CLAIM = 'sub';

    /**
     * @var array<\Generated\Shared\Transfer\UserTransfer>
     */
    protected array $currentUserTransfers = [];

    protected int $resetCurrentUserCalls = 0;

    /**
     * @var array<\Generated\Shared\Transfer\UserCriteriaTransfer>
     */
    protected array $userCriteriaTransfers = [];

    public function testGivenUserClaimsWhenHandlingRequestThenPutsLoadedUserTransferOnRequest(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims([
            'id_user' => static::ID_USER,
            'uuid' => static::USER_UUID,
        ]);

        // Act
        $this->createSubscriber([$this->createLoadedUserTransfer()])->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $userTransfer = $request->attributes->get(UserIdentityRequestSubscriber::ATTRIBUTE_USER_TRANSFER);
        $this->assertInstanceOf(UserTransfer::class, $userTransfer);
        $this->assertSame(static::ID_USER, $userTransfer->getIdUser());
        $this->assertSame(static::USERNAME, $userTransfer->getUsername());
    }

    public function testGivenUserClaimsWhenHandlingRequestThenPublishesLoadedUserAsZedCurrentUser(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims(['id_user' => static::ID_USER]);

        // Act
        $this->createSubscriber([$this->createLoadedUserTransfer()])->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertCount(1, $this->currentUserTransfers);
        $this->assertSame(static::ID_USER, $this->currentUserTransfers[0]->getIdUser());
        $this->assertSame(static::USERNAME, $this->currentUserTransfers[0]->getUsername());
    }

    public function testGivenUserClaimsWhenHandlingRequestThenLooksTheUserUpAmongActiveUsersOnly(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims(['id_user' => static::ID_USER]);

        // Act
        $this->createSubscriber([$this->createLoadedUserTransfer()])->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertCount(1, $this->userCriteriaTransfers);
        $userConditionsTransfer = $this->userCriteriaTransfers[0]->getUserConditionsOrFail();
        $this->assertSame([static::ID_USER], $userConditionsTransfer->getUserIds());
        $this->assertSame([static::STATUS_ACTIVE], $userConditionsTransfer->getStatuses());
    }

    public function testGivenStringIdUserClaimWhenHandlingRequestThenCastsItToIntegerForTheLookup(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims(['id_user' => (string)static::ID_USER]);

        // Act
        $this->createSubscriber([$this->createLoadedUserTransfer()])->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertSame([static::ID_USER], $this->userCriteriaTransfers[0]->getUserConditionsOrFail()->getUserIds());
    }

    public function testGivenClaimsOfUserThatIsNotActiveWhenHandlingRequestThenRejectsTheRequest(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims(['id_user' => static::ID_USER]);
        $subscriber = $this->createSubscriber([]);

        // Act
        try {
            $subscriber->onKernelRequest($this->createRequestEvent($request));
            $this->fail('Expected the request to be rejected for a user that is not active.');
        } catch (GlueApiException $glueApiException) {
            // Assert
            $this->assertSame(Response::HTTP_UNAUTHORIZED, $glueApiException->getStatusCode());
            $this->assertSame(UserConfig::RESPONSE_CODE_USER_NOT_RESOLVED, $glueApiException->getErrorCode());
        }

        $this->assertNull($request->attributes->get(UserIdentityRequestSubscriber::ATTRIBUTE_USER_TRANSFER));
        $this->assertCount(0, $this->currentUserTransfers);
    }

    /**
     * A mapping that matches two users is a misconfiguration, not an identity; loading the first one
     * would hand a request to whichever user happens to sort first.
     */
    public function testGivenClaimsMatchingSeveralUsersWhenHandlingRequestThenRejectsTheRequest(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims(['id_user' => static::ID_USER]);
        $subscriber = $this->createSubscriber([
            $this->createLoadedUserTransfer(),
            (new UserTransfer())->setIdUser(static::ID_USER + 1)->setUsername('second@spryker.com'),
        ]);

        // Act
        try {
            $subscriber->onKernelRequest($this->createRequestEvent($request));
            $this->fail('Expected the request to be rejected when the claims identify more than one user.');
        } catch (GlueApiException $glueApiException) {
            // Assert
            $this->assertSame(Response::HTTP_UNAUTHORIZED, $glueApiException->getStatusCode());
        }

        $this->assertCount(0, $this->currentUserTransfers);
    }

    public function testGivenCustomerClaimsWhenHandlingRequestThenLeavesRequestUntouched(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims(['customer_reference' => 'DE--1']);

        // Act
        $this->createSubscriber([$this->createLoadedUserTransfer()])->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertNull($request->attributes->get(UserIdentityRequestSubscriber::ATTRIBUTE_USER_TRANSFER));
        $this->assertCount(0, $this->userCriteriaTransfers, 'A token that identifies no user must not trigger a lookup.');
        $this->assertCount(0, $this->currentUserTransfers);
    }

    public function testGivenNoClaimsWhenHandlingRequestThenPublishesNoCurrentUser(): void
    {
        // Arrange
        $request = new Request();

        // Act
        $this->createSubscriber([$this->createLoadedUserTransfer()])->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertNull($request->attributes->get(UserIdentityRequestSubscriber::ATTRIBUTE_USER_TRANSFER));
        $this->assertCount(0, $this->currentUserTransfers);
    }

    public function testGivenNoClaimsWhenHandlingRequestThenStillDiscardsTheCurrentUserOfTheWorker(): void
    {
        // Arrange
        $request = new Request();

        // Act
        $this->createSubscriber([$this->createLoadedUserTransfer()])->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertSame(1, $this->resetCurrentUserCalls);
        $this->assertCount(0, $this->currentUserTransfers);
    }

    public function testGivenAPluginMappingAThirdPartyClaimWhenHandlingRequestThenTheUserIsLookedUpByThatMapping(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims([static::THIRD_PARTY_SUBJECT_CLAIM => static::USERNAME]);
        $subscriber = $this->createSubscriber(
            [$this->createLoadedUserTransfer()],
            [$this->createUsernameMappingPlugin()],
        );

        // Act
        $subscriber->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $userConditionsTransfer = $this->userCriteriaTransfers[0]->getUserConditionsOrFail();
        $this->assertSame([static::USERNAME], $userConditionsTransfer->getUsernames());
        $this->assertSame([], $userConditionsTransfer->getUserIds());
        $this->assertSame([static::STATUS_ACTIVE], $userConditionsTransfer->getStatuses(), 'The status filter is applied after the plugins.');
        $this->assertCount(1, $this->currentUserTransfers);
    }

    public function testGivenAPluginMappingAReferenceWhenHandlingRequestThenItReplacesTheIdUserDefault(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims(['id_user' => static::ID_USER, 'uuid' => static::USER_UUID]);
        $subscriber = $this->createSubscriber(
            [$this->createLoadedUserTransfer()],
            [$this->createUuidMappingPlugin()],
        );

        // Act
        $subscriber->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $userConditionsTransfer = $this->userCriteriaTransfers[0]->getUserConditionsOrFail();
        $this->assertSame([static::USER_UUID], $userConditionsTransfer->getUuids());
        $this->assertSame([], $userConditionsTransfer->getUserIds(), 'A plugin that identified the user takes precedence over the id_user default.');
    }

    public function testGivenAPluginThatMapsNothingWhenHandlingRequestThenTheIdUserDefaultApplies(): void
    {
        // Arrange
        $request = $this->createRequestWithClaims(['id_user' => static::ID_USER]);
        $subscriber = $this->createSubscriber(
            [$this->createLoadedUserTransfer()],
            [$this->createUsernameMappingPlugin()],
        );

        // Act
        $subscriber->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertSame([static::ID_USER], $this->userCriteriaTransfers[0]->getUserConditionsOrFail()->getUserIds());
    }

    /**
     * @param array<\Generated\Shared\Transfer\UserTransfer> $loadedUserTransfers
     * @param array<\Spryker\Shared\UserExtension\Dependency\Plugin\UserIdentityCriteriaExpanderPluginInterface> $plugins
     */
    protected function createSubscriber(array $loadedUserTransfers, array $plugins = []): UserIdentityRequestSubscriber
    {
        return new UserIdentityRequestSubscriber($this->createUserFacadeStub($loadedUserTransfers), $plugins);
    }

    /**
     * @param array<\Generated\Shared\Transfer\UserTransfer> $loadedUserTransfers
     */
    protected function createUserFacadeStub(array $loadedUserTransfers): UserFacadeInterface
    {
        return Stub::makeEmpty(UserFacadeInterface::class, [
            'getUserCollection' => function (UserCriteriaTransfer $userCriteriaTransfer) use ($loadedUserTransfers): UserCollectionTransfer {
                $this->userCriteriaTransfers[] = $userCriteriaTransfer;

                return (new UserCollectionTransfer())->setUsers(new ArrayObject($loadedUserTransfers));
            },
            'setCurrentUser' => function (UserTransfer $userTransfer): void {
                $this->currentUserTransfers[] = $userTransfer;
            },
            'resetCurrentUser' => function (): void {
                $this->resetCurrentUserCalls++;
            },
        ]);
    }

    protected function createUsernameMappingPlugin(): UserIdentityCriteriaExpanderPluginInterface
    {
        return Stub::makeEmpty(UserIdentityCriteriaExpanderPluginInterface::class, [
            'expand' => function (array $identityClaims, UserCriteriaTransfer $userCriteriaTransfer): UserCriteriaTransfer {
                if (isset($identityClaims[static::THIRD_PARTY_SUBJECT_CLAIM])) {
                    $userCriteriaTransfer->getUserConditionsOrFail()->addUsername($identityClaims[static::THIRD_PARTY_SUBJECT_CLAIM]);
                }

                return $userCriteriaTransfer;
            },
        ]);
    }

    protected function createUuidMappingPlugin(): UserIdentityCriteriaExpanderPluginInterface
    {
        return Stub::makeEmpty(UserIdentityCriteriaExpanderPluginInterface::class, [
            'expand' => function (array $identityClaims, UserCriteriaTransfer $userCriteriaTransfer): UserCriteriaTransfer {
                $userCriteriaTransfer->getUserConditionsOrFail()->addUuid($identityClaims['uuid']);

                return $userCriteriaTransfer;
            },
        ]);
    }

    protected function createLoadedUserTransfer(): UserTransfer
    {
        return (new UserTransfer())
            ->setIdUser(static::ID_USER)
            ->setUsername(static::USERNAME)
            ->setUuid(static::USER_UUID);
    }

    /**
     * @param array<string, mixed> $claims
     */
    protected function createRequestWithClaims(array $claims): Request
    {
        $request = new Request();
        $request->attributes->set(IdentityRequestSubscriber::ATTRIBUTE_OAUTH_IDENTITY_CLAIMS, $claims);

        return $request;
    }

    protected function createRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent(
            Stub::makeEmpty(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
    }
}
