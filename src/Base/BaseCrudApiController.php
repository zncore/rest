<?php

namespace PhpLab\Rest\Base;

use PhpLab\Rest\Base\BaseAction;
use PhpLab\Rest\Actions\BaseEntityAction;
use PhpLab\Rest\Actions\CreateAction;
use PhpLab\Rest\Actions\DeleteAction;
use PhpLab\Rest\Actions\IndexAction;
use PhpLab\Rest\Actions\OptionsAction;
use PhpLab\Rest\Actions\UpdateAction;
use PhpLab\Rest\Actions\ViewAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseCrudApiController
{

    /** @var $service object */
    public $service;

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
            ],
            'create' => [
                'class' => CreateAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
            ],
            'update' => [
                'class' => UpdateAction::class,
            ],
            'delete' => [
                'class' => DeleteAction::class,
            ],
            'options' => [
                'class' => OptionsAction::class,
            ],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $actions = $this->actions();
        $actionClass = $actions['index']['class'];
        /** @var BaseAction $action */
        $action = new $actionClass($this->service, $request);
        return $action->run();
    }

    public function create(Request $request): JsonResponse
    {
        $actions = $this->actions();
        $actionClass = $actions['create']['class'];
        /** @var BaseAction $action */
        $action = new $actionClass($this->service, $request);
        return $action->run();
    }

    public function view($id, Request $request): JsonResponse
    {
        $actions = $this->actions();
        $actionClass = $actions['view']['class'];
        /** @var BaseEntityAction $action */
        $action = new $actionClass($this->service, $request, $id);
        return $action->run();
    }

    public function update($id, Request $request): JsonResponse
    {
        $actions = $this->actions();
        $actionClass = $actions['update']['class'];
        /** @var BaseEntityAction $action */
        $action = new $actionClass($this->service, $request, $id);
        return $action->run();
    }

    public function delete($id, Request $request): JsonResponse
    {
        $actions = $this->actions();
        $actionClass = $actions['delete']['class'];
        /** @var BaseEntityAction $action */
        $action = new $actionClass($this->service, $request, $id);
        return $action->run();
    }

    public function options(Request $request): JsonResponse
    {
        $actions = $this->actions();
        $actionClass = $actions['options']['class'];
        /** @var BaseEntityAction $action */
        $action = new $actionClass($this->service, $request);
        return $action->run();
    }

}