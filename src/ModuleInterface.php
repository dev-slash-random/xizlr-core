<?php

namespace Mooti\Xizlr\Core;

interface ModuleInterface
{
	/**
     * Gets the service provider for this module
     *
     * @return ServiceProviderInterface The Service Provider
     */
    public function getServiceProvider();
}