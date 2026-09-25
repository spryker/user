<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\User;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface UserConstants
{
    /**
     * @var string
     */
    public const USER_SYSTEM_USERS = 'USER_SYSTEM_USERS';

    /**
     * Specification:
     * - Bcrypt cost factor used to hash Zed user passwords.
     * - Keep the production-grade default; lower it only in test environments where hashing dominates runtime.
     *
     * @api
     */
    public const string PASSWORD_HASH_COST = 'USER:PASSWORD_HASH_COST';
}
