<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\User;

use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;

class UserDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @return array<\Spryker\Shared\UserExtension\Dependency\Plugin\UserIdentityCriteriaExpanderPluginInterface>
     */
    protected function getUserIdentityCriteriaExpanderPlugins(): array
    {
        return [];
    }
}
