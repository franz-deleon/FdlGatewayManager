<?php
namespace FdlGatewayManager\Utils;

use Zend\Db\Adapter\Adapter;

class GatewayFactoryUtilities
{
    public static function normalizeClassname($className)
    {
        $wordFilter = new \Zend\Filter\Word\UnderscoreToCamelCase();
        return $wordFilter->filter($className);
    }

    public static function normalizeTablename($tableName)
    {
        $wordFilter = new \Zend\Filter\Word\CamelCaseToUnderscore;

        $tableName = preg_replace('~[^a-z0-9]~i', '_', $tableName);
        $tableName = $wordFilter->filter($tableName);
        $tableName = explode('_', $tableName);

        // check if the table name is appended with 'table' or 'entity'
        $lastWord = strtolower($tableName[(count($tableName) - 1)]);
        if ('table' === $lastWord || 'entity' === $lastWord) {
            array_pop($tableName);
        }

        $tableName = array_map(function ($name) {
            return ucfirst($name);
        }, $tableName);

        return implode('', $tableName);
    }

    /**
     * Extract the class name from a fully qualified namespace
     * @param string|object $class
     * @return mixed
     */
    public static function extractClassnameFromFQNS($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (is_string($class)) {
            $class = substr($class, (strrpos($class, '\\') + 1));
        }

        return $class;
    }
}
