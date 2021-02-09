<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\EventBehavior\Dependency\Facade;

interface EventBehaviorToPropelFacadeInterface
{
    /**
     * @param string $tableName
     *
     * @return bool
     */
    public function tableExists(string $tableName): bool;
}
