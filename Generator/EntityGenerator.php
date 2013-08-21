<?php

namespace Egzakt\SystemBundle\Generator;

use \Doctrine\ORM\Mapping\ClassMetadataInfo;
use \Doctrine\Common\Util\Inflector;

/**
 * Entity Generator
 */
class EntityGenerator extends \Doctrine\ORM\Tools\EntityGenerator
{
    protected static $_getToString =
'
    public function __toString()
    {
        if (false == $this->id) {
            return \'New <entityName>\';
        }

        if ($name = <nameFunction>) {
            return $name;
        }

        return \'\';
    }
';

    protected static $_getRouteBackendTemplate =
'
    /**
     * Get the backend route
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getRouteBackend($suffix = \'edit\')
    {
        return \'<routeName>_\' . $suffix;
    }
';

    protected static $_getRouteBackendParamsTemplate =
'
    /**
     * Get params for the backend route
     *
     * @param array $params Additional parameters
     *
     * @return array
     */
    public function getRouteBackendParams($params = array())
    {
        $defaults = array(
            \'id\' => $this->id ? $this->id : 0,
        );

        $params = array_merge($defaults, $params);

        return $params;
    }
';

    protected static $_endClassBracket =
'}';

    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate Entity Class
     *
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata Metadata Info
     * @param array $fields Fields Info
     *
     * @return string
     */
    public function generateEntityClass(ClassMetadataInfo $metadata, array $fields = array())
    {
        parent::setFieldVisibility('protected');

        $code = parent::generateEntityClass($metadata);

        $shortClassName = explode('\\', $metadata->name);
        $shortClassName = 'class ' . end($shortClassName);

        // Adding custom extends
        $code = str_replace($shortClassName, $shortClassName . ' extends BaseEntity', $code);

        // Adding custom use statement
        $useStatements = 'use Egzakt\SystemBundle\Lib\BaseEntity;';
        $startString = 'use Doctrine\ORM\Mapping as ORM;';
        $code = str_replace($startString, $startString . "\n\n" . $useStatements, $code);

        // Add custom methods
        if (!strstr($metadata->name, 'Translation')) {

            $code = substr($code, 0, -2); // remove the end bracket

            $bundleName = explode('\Entity', $metadata->name);
            $entityName = str_replace('\\', '', $bundleName[1]);

            $routeNamePrefix = strtolower(str_replace('\\', '_', str_replace('Bundle', '', $bundleName[0]))) . '_backend';
            $routeEntityName = $routeNamePrefix . strtolower(str_replace('\\', '_', $bundleName[1]));

            // Track all the translation fields and check if it contains the fieldName 'name'
            $containNameField = false;
            foreach ($fields as $field) {
                if (substr(strtolower($field['i18n']), 0, 1) == 'y') {
                    $field['fieldName'] == 'name' ? $containNameField = true : null;
                }
            }

            if ($containNameField) {
                $code .= str_replace(array('<entityName>', '<nameFunction>'), array($entityName, '$this->translate()->getName()'), static::$_getToString);
            } else {
                $code .= str_replace(array('<entityName>', '<nameFunction>'), array($entityName, '$this->getEntityName()'), static::$_getToString);
            }

            $code .= str_replace('<routeName>', $routeEntityName, static::$_getRouteBackendTemplate);

            $code .= static::$_getRouteBackendParamsTemplate;

            $code .= static::$_endClassBracket;
        }

        return $code;
    }

}