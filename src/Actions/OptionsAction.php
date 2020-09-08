<?php

namespace ZnLib\Rest\Actions;

use ZnLib\Rest\Base\BaseAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class OptionsAction extends BaseAction
{

    public function run(): JsonResponse
    {
        $response = new JsonResponse;
        return $response;
    }

}