<?php

namespace ZnLib\Rest\Helpers;

use ZnCore\Base\Libs\Http\Enums\HttpMethodEnum;
use ZnCore\Base\Libs\Text\Helpers\Inflector;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RestApiRouteHelper
{

    public static function defineCrudRoutes(string $endpoint, string $controllerClassName, RouteCollection $routeCollection)
    {
        $routeNamePrefix = self::extractRoutePrefix($controllerClassName);

        $endpoint = '/' . trim($endpoint, '/');
        $routeCollection = $routeCollection ?? new RouteCollection;

        $defaults = ['_controller' => $controllerClassName, '_action' => 'view'];
        $routeName = $routeNamePrefix . '_view';
        $route = new Route($endpoint . '/{id}', $defaults, [], [], null, [], [HttpMethodEnum::GET]);
        $routeCollection->add($routeName, $route);

        $defaults = ['_controller' => $controllerClassName, '_action' => 'delete'];
        $routeName = $routeNamePrefix . '_delete';
        $route = new Route($endpoint . '/{id}', $defaults, [], [], null, [], [HttpMethodEnum::DELETE]);
        $routeCollection->add($routeName, $route);

        $defaults = ['_controller' => $controllerClassName, '_action' => 'update'];
        $routeName = $routeNamePrefix . '_update';
        $route = new Route($endpoint . '/{id}', $defaults, [], [], null, [], [HttpMethodEnum::PUT]);
        $routeCollection->add($routeName, $route);

        $defaults = ['_controller' => $controllerClassName, '_action' => 'index'];
        $routeName = $routeNamePrefix . '_index';
        $route = new Route($endpoint, $defaults, [], [], null, [], [HttpMethodEnum::GET]);
        $routeCollection->add($routeName, $route);

        $defaults = ['_controller' => $controllerClassName, '_action' => 'create'];
        $routeName = $routeNamePrefix . '_create';
        $route = new Route($endpoint, $defaults, [], [], null, [], [HttpMethodEnum::POST]);
        $routeCollection->add($routeName, $route);
    }

    public static function extractRoutePrefix(string $controllerClassName): string
    {
        $controllerClass = basename($controllerClassName);
        $controllerClass = str_replace('Controller', '', $controllerClass);
        $routeNamePrefix = Inflector::underscore($controllerClass);
        return $routeNamePrefix;
    }

}
