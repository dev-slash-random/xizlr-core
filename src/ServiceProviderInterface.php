<?php

namespace Mooti\Framework;

interface ServiceProviderInterface
{
	/**
     * Get the details of the services we are providing     
     *
     * @return array Services in the format ['serviceId' => initFunction():Object ]
     */
    public function getServices();
}