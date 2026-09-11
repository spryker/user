<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\User;

use Spryker\Glue\Kernel\AbstractBundleConfig;

class UserConfig extends AbstractBundleConfig
{
    /**
     * @api
     */
    public const string RESPONSE_CODE_USER_NOT_RESOLVED = '003';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_USER_NOT_RESOLVED = 'The access token does not belong to an active user.';
}
