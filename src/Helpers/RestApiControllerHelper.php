<?php

namespace PhpLab\Rest\Helpers;

use Illuminate\Container\Container;
use PhpLab\Core\Enums\Http\HttpStatusCodeEnum;
use PhpLab\Rest\Entities\RouteEntity;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Throwable;

class RestApiControllerHelper
{

    public static function send(RouteCollection $routeCollection, ContainerInterface $container, $context = '/')
    {
        //$request = $request ?? Request::createFromGlobals();
        $response = self::run($routeCollection, $container, $context);
        $response->send();
    }

    public static function run(RouteCollection $routeCollection, ContainerInterface $container, $context = '/'): Response
    {
        $response = RestApiControllerHelper::runAll($routeCollection, $container, $context);
        return $response;
    }

    private static function runAll(RouteCollection $routeCollection, ContainerInterface $container, $context = '/'): Response
    {
        $request = $container->get(Request::class);
        try {
            $routeEntity = self::match($request, $routeCollection, $context);
            $controllerInstance = $container->get($routeEntity->controllerClassName);
            $response = self::runController($controllerInstance, $request, $routeCollection);
        } catch (ResourceNotFoundException $e) {
            $response = self::getResponseByStatusCode(HttpStatusCodeEnum::NOT_FOUND);
        }
        return $response;
    }

    public static function prepareContent(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            //dd($request->request->all());
        }
    }

    private static function runController(object $controllerInstance, Request $request, RouteCollection $routeCollection, $context = '/'): Response
    {
        $routeEntity = self::match($request, $routeCollection, $context);
        $callback = [$controllerInstance, $routeEntity->actionName];
        try {
            $container = Container::getInstance();
            $response = $container->call([$controllerInstance, $routeEntity->actionName], $routeEntity->actionParameters);

            //$response = call_user_func_array($callback, $routeEntity->actionParameters);
        } catch (Throwable $e) {
            $response = self::handleException($e);
        }
        return $response;
    }

    private static function match(Request $request, RouteCollection $routeCollection, $context = '/'): RouteEntity
    {
        $requestContext = new RequestContext($context);
        $matcher = new UrlMatcher($routeCollection, $requestContext);
        $parameters = $matcher->match($request->getPathInfo());
        $routeEntity = new RouteEntity;
        $routeEntity->controllerClassName = $parameters['_controller'];
        $routeEntity->actionName = $parameters['_action'];
        if (in_array($routeEntity->actionName, ['view', 'update', 'delete'])) {
            $id = $parameters['id'];
            $routeEntity->actionParameters = [$id, $request];
        } else {
            $routeEntity->actionParameters = [$request];
        }
        return $routeEntity;
    }

    private static function getResponseByStatusCode(int $statusCode): JsonResponse
    {
        $message = Response::$statusTexts[$statusCode];
        $data = ['message' => $message];
        $response = new JsonResponse($data, $statusCode);
        return $response;
    }

    private static function handleException(Throwable $exception): Response
    {
        if ($exception instanceof ResourceNotFoundException) {
            $response = self::getResponseByStatusCode(HttpStatusCodeEnum::NOT_FOUND);
        } elseif ($exception instanceof MethodNotAllowedException) {
            $response = self::getResponseByStatusCode(HttpStatusCodeEnum::METHOD_NOT_ALLOWED);
        } else {
            throw $exception;
        }
        return $response;
    }

}
