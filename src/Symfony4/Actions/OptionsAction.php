<?php

namespace ZnLib\Rest\Symfony4\Actions;

use ZnLib\Rest\Symfony4\Base\BaseAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class OptionsAction extends BaseAction
{

    public function run(): JsonResponse
    {
        $response = new JsonResponse;
        return $response;
    }

}