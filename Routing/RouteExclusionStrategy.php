<?php

namespace Egzakt\SystemBundle\Routing;

use Symfony\Component\Routing\Route;

use JMS\I18nRoutingBundle\Router\DefaultRouteExclusionStrategy;

/**
 * The egzakt route exclusion strategy.
 *
 * This strategy add one condition to the default JMSi18nRouting excludes list:
 *
 *     - the route must not be from a egzakt backend application
 */
class RouteExclusionStrategy extends DefaultRouteExclusionStrategy
{
    public function shouldExcludeRoute($routeName, Route $route)
    {
        $shouldExclude = parent::shouldExcludeRoute($routeName, $route);

        if ($shouldExclude) {
            return true;
        }

        // automatically exclude if the route is a egzakt backend one
        if (preg_match('/egzakt_[a-zA-Z0-9-_]*backend_/', $routeName)) {
            return true;
        }

        return false;
    }
}