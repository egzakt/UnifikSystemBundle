<?php

namespace Egzakt\SystemBundle\Lib;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

/**
 * This generator handle the cleaning of the automatics route parameters
 */
class RouterUrlGenerator extends BaseUrlGenerator
{
    /**
     * @var array
     */
    private $automaticParameters = array('sectionId');

    /**
     * @inheritdoc
     */
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens)
    {
        $parameters = $this->removeUnusedAutoParameters($this->automaticParameters, $parameters, $variables);

        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens);
    }

    /**
     * This method remove keys (parameters names) that are not used in the variable array.
     * Such keys are superflous as they match no variables of the route.
     *
     * @param $keys
     * @param $parameters
     * @param $variables
     *
     * @return array The filtered parameters
     */
    private function removeUnusedAutoParameters($keys, $parameters, $variables)
    {
        foreach ($keys as $key) {
            if (isset($parameters[$key]) && false === in_array($key, $variables)) {
                unset($parameters[$key]);
            }
        }

        return $parameters;
    }
}
