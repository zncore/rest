<?php

namespace ZnLib\Rest\Symfony4\Base;

use ZnCore\Domain\Helpers\QueryHelper;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Interfaces\Service\CrudServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseAction
 * @package ZnLib\Rest\Symfony4\Actions
 *
 * @property CrudServiceInterface $service
 */
abstract class BaseAction
{

    /** @var $service */
    public $service;

    /** @var Request */
    public $request;

    /** @var Query */
    public $query;

    public function __construct(object $service, Request $request)
    {
        $this->service = $service;
        $this->request = $request;
        $this->query = $this->forgeQueryFromRequest($request);
    }

    abstract public function run(): JsonResponse;

    private function forgeQueryFromRequest(Request $request)
    {
        return QueryHelper::getAllParams($request->query->all());
    }

}