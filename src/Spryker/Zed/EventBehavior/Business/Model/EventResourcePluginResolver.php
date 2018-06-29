<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\EventBehavior\Business\Model;

use Spryker\Zed\EventBehavior\Dependency\Plugin\EventResourceQueryContainerPluginInterface;
use Spryker\Zed\EventBehavior\Dependency\Plugin\EventResourceRepositoryPluginInterface;

class EventResourcePluginResolver
{
    protected const REPOSITORY_EVENT_RESOURCE_PLUGINS = 'repository';
    protected const QUERY_CONTAINER_EVENT_RESOURCE_PLUGINS = 'query_container';

    /**
     * @var \Spryker\Zed\EventBehavior\Dependency\Plugin\EventResourcePluginInterface[]
     */
    protected $eventResourcePlugins;

    /**
     * @var \Spryker\Zed\EventBehavior\Business\Model\EventResourceRepositoryManager
     */
    protected $eventResourceRepositoryManager;

    /**
     * @var \Spryker\Zed\EventBehavior\Business\Model\EventResourceQueryContainerManager
     */
    protected $eventResourceQueryContainerManager;

    /**
     * @param \Spryker\Zed\EventBehavior\Business\Model\EventResourceRepositoryManager $eventResourceRepositoryManager
     * @param \Spryker\Zed\EventBehavior\Business\Model\EventResourceQueryContainerManager $eventResourceQueryContainerManager
     * @param array $eventResourcePlugins
     */
    public function __construct(
        EventResourceRepositoryManager $eventResourceRepositoryManager,
        EventResourceQueryContainerManager $eventResourceQueryContainerManager,
        array $eventResourcePlugins
    ) {
        $this->eventResourceRepositoryManager = $eventResourceRepositoryManager;
        $this->eventResourceQueryContainerManager = $eventResourceQueryContainerManager;
        $this->eventResourcePlugins = $eventResourcePlugins;
    }

    /**
     * @param string[] $resources
     *
     * @return void
     */
    public function executeResolvedPluginsBySources(array $resources)
    {
        $pluginsPerExporter = $this->getResolvedPluginsByResources($resources);
        $this->eventResourceQueryContainerManager->triggerResourceEvents($pluginsPerExporter[static::QUERY_CONTAINER_EVENT_RESOURCE_PLUGINS]);
        $this->eventResourceRepositoryManager->triggerResourceEvents($pluginsPerExporter[static::REPOSITORY_EVENT_RESOURCE_PLUGINS]);
    }

    /**
     * @param string[] $resources
     *
     * @return \Spryker\Zed\SynchronizationExtension\Dependency\Plugin\SynchronizationDataPluginInterface[]
     */
    protected function getResolvedPluginsByResources(array $resources)
    {
        $this->mapPluginsByResourceName();
        $effectivePlugins = $this->getEffectivePlugins($resources);
        $pluginsPerExporter = [
            static::REPOSITORY_EVENT_RESOURCE_PLUGINS => [],
            static::QUERY_CONTAINER_EVENT_RESOURCE_PLUGINS => [],
        ];

        foreach ($effectivePlugins as $effectivePlugin) {
            if ($effectivePlugin instanceof EventResourceRepositoryPluginInterface) {
                $pluginsPerExporter[static::REPOSITORY_EVENT_RESOURCE_PLUGINS][] = $effectivePlugin;
            }
            if ($effectivePlugin instanceof EventResourceQueryContainerPluginInterface) {
                $pluginsPerExporter[static::QUERY_CONTAINER_EVENT_RESOURCE_PLUGINS][] = $effectivePlugin;
            }
        }

        return $pluginsPerExporter;
    }

    /**
     * @return void
     */
    protected function mapPluginsByResourceName(): void
    {
        $mappedDataPlugins = [];
        foreach ($this->eventResourcePlugins as $plugin) {
            $mappedDataPlugins[$plugin->getResourceName()] = $plugin;
        }

        $this->eventResourcePlugins = $mappedDataPlugins;
    }

    /**
     * @param array $resources
     *
     * @return \Spryker\Zed\EventBehavior\Dependency\Plugin\EventResourcePluginInterface[]
     */
    protected function getEffectivePlugins(array $resources): array
    {
        $effectivePlugins = [];
        if (empty($resources)) {
            return $this->eventResourcePlugins;
        }

        foreach ($resources as $resource) {
            if (isset($this->eventResourcePlugins[$resource])) {
                $effectivePlugins[$resource] = $this->eventResourcePlugins[$resource];
            }
        }

        return $effectivePlugins;
    }
}
