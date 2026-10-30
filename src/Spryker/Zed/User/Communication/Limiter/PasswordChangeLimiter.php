<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\User\Communication\Limiter;

use Generated\Shared\Transfer\SecurityCheckAttemptContextTransfer;
use Spryker\Client\SecurityBlocker\SecurityBlockerClientInterface;
use Spryker\Zed\User\UserConfig;

class PasswordChangeLimiter implements PasswordChangeLimiterInterface
{
    protected const string SECURITY_CHECK_TYPE = 'back-office-user:password_change';

    protected const string IDENTIFIER_PATTERN_USER = 'user:%d';

    public function __construct(
        protected SecurityBlockerClientInterface $securityBlockerClient,
        protected UserConfig $userConfig
    ) {
    }

    public function reserveAttempt(int $idUser): bool
    {
        $limit = $this->userConfig->getPasswordChangeAttemptLimit();

        if ($limit <= 0) {
            return true;
        }

        $securityCheckAttemptContextTransfer = (new SecurityCheckAttemptContextTransfer())
            ->setType(static::SECURITY_CHECK_TYPE)
            ->setIdentifier(sprintf(static::IDENTIFIER_PATTERN_USER, $idUser))
            ->setTtl($this->userConfig->getPasswordChangeAttemptLimitTtl());

        return $this->securityBlockerClient
            ->incrementAttemptCount($securityCheckAttemptContextTransfer)
            ->getNumberOfAttemptsOrFail() <= $limit;
    }
}
