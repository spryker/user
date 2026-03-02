<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\User\Communication\Table\PluginExecutor;

use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

interface UserTablePluginExecutorInterface
{
    /**
     * @param array $user
     *
     * @return array<\Generated\Shared\Transfer\ButtonTransfer>
     */
    public function executeActionButtonExpanderPlugins(array $user): array;

    public function executeConfigExpanderPlugins(TableConfiguration $tableConfiguration): TableConfiguration;

    public function executeDataExpanderPlugins(array $item): array;
}
