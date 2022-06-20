<?php

namespace ZnLib\Rest\Symfony4\Base;

use ZnCore\Base\Libs\Query\Helpers\QueryHelper;
use ZnCore\Base\Libs\Query\Entities\Query;
use ZnCore\Base\Libs\Service\Interfaces\CrudServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ZnLib\Web\Helpers\WebQueryHelper;

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
        return WebQueryHelper::getAllParams($request->query->all());
    }

}