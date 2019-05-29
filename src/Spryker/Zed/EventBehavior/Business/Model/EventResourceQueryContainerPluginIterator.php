<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\EventBehavior\Business\Model;

use Iterator;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\EventBehavior\Dependency\Plugin\EventResourceQueryContainerPluginInterface;

class EventResourceQueryContainerPluginIterator implements Iterator
{
    /**
     * @var \Spryker\Zed\EventBehavior\Dependency\Plugin\EventResourceQueryContainerPluginInterface
     */
    protected $plugin;

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * @var array
     */
    protected $current = [];

    /**
     * @var int
     */
    protected $chunkSize;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @param \Spryker\Zed\EventBehavior\Dependency\Plugin\EventResourceQueryContainerPluginInterface $plugin
     * @param int $chunkSize
     */
    public function __construct(EventResourceQueryContainerPluginInterface $plugin, int $chunkSize)
    {
        $this->plugin = $plugin;
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return void
     */
    protected function executeQuery(): void
    {
        $this->current = $this->plugin->queryData()
            ->offset($this->offset)
            ->limit($this->chunkSize)
            ->where($this->plugin->getIdColumnName() . ModelCriteria::ISNOTNULL)
            ->select([$this->plugin->getIdColumnName()])
            ->orderBy($this->plugin->getIdColumnName())
            ->find()
            ->getData();
    }

    /**
     * @return array
     */
    public function current(): array
    {
        return $this->current;
    }

    /**
     * @return void
     */
    public function next(): void
    {
        $this->offset += $this->chunkSize;
        $this->index += 1;
        $this->executeQuery();
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return is_array($this->current) && $this->current !== [];
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        $this->offset = 0;
        $this->index = 0;
        $this->executeQuery();
    }
}
