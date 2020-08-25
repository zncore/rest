<?php

namespace PhpLab\Rest\Actions;

use PhpLab\Rest\Base\BaseAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class OptionsAction extends BaseAction
{

    public function run(): JsonResponse
    {
        $response = new JsonResponse;
        return $response;
    }

}