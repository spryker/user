<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\User\Api\Backend\EventSubscriber;

use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\ApiPlatform\Attribute\ApiType;
use Spryker\ApiPlatform\EventSubscriber\IdentityRequestSubscriber;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\User\UserConfig;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Zed\User\Business\UserFacadeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Loads the active user the access token identifies and publishes it as Zed's current user.
 * `UserIdentityCriteriaExpanderPluginInterface` plugins map the token claims onto the lookup criteria;
 * the `id_user` claim is the default when no plugin produced an identifying condition.
 */
#[ApiType(types: ['backend'])]
class UserIdentityRequestSubscriber implements EventSubscriberInterface
{
    public const string ATTRIBUTE_USER_TRANSFER = 'UserTransfer';

    protected const string KEY_ID_USER = 'id_user';

    protected const string STATUS_ACTIVE = 'active';

    protected const int PRIORITY_AFTER_IDENTITY = 6;

    /**
     * @param array<\Spryker\Shared\UserExtension\Dependency\Plugin\UserIdentityCriteriaExpanderPluginInterface> $userIdentityCriteriaExpanderPlugins
     */
    public function __construct(
        protected UserFacadeInterface $userFacade,
        #[Plugins(dependencyProviderMethod: 'getUserIdentityCriteriaExpanderPlugins')]
        protected array $userIdentityCriteriaExpanderPlugins = [],
    ) {
    }

    /**
     * @return array<string, array<int, string|int>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', static::PRIORITY_AFTER_IDENTITY],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->userFacade->resetCurrentUser();

        $request = $event->getRequest();
        $identityClaims = $request->attributes->get(IdentityRequestSubscriber::ATTRIBUTE_OAUTH_IDENTITY_CLAIMS);

        if (!is_array($identityClaims)) {
            return;
        }

        $userCriteriaTransfer = $this->createUserCriteriaTransfer($identityClaims);

        if (!$this->hasIdentifyingCondition($userCriteriaTransfer)) {
            return;
        }

        $userTransfer = $this->findActiveUser($userCriteriaTransfer);

        if ($userTransfer === null) {
            throw new GlueApiException(
                Response::HTTP_UNAUTHORIZED,
                UserConfig::RESPONSE_CODE_USER_NOT_RESOLVED,
                UserConfig::RESPONSE_DETAIL_USER_NOT_RESOLVED,
            );
        }

        $this->userFacade->setCurrentUser($userTransfer);

        $request->attributes->set(static::ATTRIBUTE_USER_TRANSFER, $userTransfer);
    }

    /**
     * @param array<string, mixed> $identityClaims
     */
    protected function createUserCriteriaTransfer(array $identityClaims): UserCriteriaTransfer
    {
        $userCriteriaTransfer = (new UserCriteriaTransfer())->setUserConditions(new UserConditionsTransfer());

        foreach ($this->userIdentityCriteriaExpanderPlugins as $userIdentityCriteriaExpanderPlugin) {
            $userCriteriaTransfer = $userIdentityCriteriaExpanderPlugin->expand($identityClaims, $userCriteriaTransfer);
        }

        if ($this->hasIdentifyingCondition($userCriteriaTransfer) || !isset($identityClaims[static::KEY_ID_USER])) {
            return $userCriteriaTransfer;
        }

        $userCriteriaTransfer->getUserConditionsOrFail()->addIdUser((int)$identityClaims[static::KEY_ID_USER]);

        return $userCriteriaTransfer;
    }

    protected function hasIdentifyingCondition(UserCriteriaTransfer $userCriteriaTransfer): bool
    {
        $userConditionsTransfer = $userCriteriaTransfer->getUserConditionsOrFail();

        return $userConditionsTransfer->getUserIds() !== []
            || $userConditionsTransfer->getUuids() !== []
            || $userConditionsTransfer->getUsernames() !== [];
    }

    protected function findActiveUser(UserCriteriaTransfer $userCriteriaTransfer): ?UserTransfer
    {
        $userCriteriaTransfer->getUserConditionsOrFail()->addStatus(static::STATUS_ACTIVE);

        $userTransfers = $this->userFacade->getUserCollection($userCriteriaTransfer)->getUsers();

        if ($userTransfers->count() !== 1) {
            return null;
        }

        return $userTransfers->offsetGet(0);
    }
}
