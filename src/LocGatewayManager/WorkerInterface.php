<?php
namespace LocGatewayManager;

interface WorkerInterface
{
    /**
     * Retrieve the adapter name
     * @return string
     */
    public function getAdapterKeyName();

    /**
     * Return the table name
     * @return string
     */
    public function getEntityName();

    /**
     * Retrieve the feature name
     * @return string
     */
    public function getFeatureName();

    /**
     * Retrieve the result ser name
     * @return string
     */
    public function getResultSetName();
}
