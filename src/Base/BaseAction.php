<?php

namespace ZnLib\Rest\Base;

use ZnCore\Base\Domain\Helpers\QueryHelper;
use ZnCore\Base\Domain\Libs\Query;
use ZnCore\Base\Domain\Interfaces\Service\CrudServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseAction
 * @package ZnLib\Rest\Actions
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