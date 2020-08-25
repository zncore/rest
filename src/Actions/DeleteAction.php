<?php

namespace PhpLab\Rest\Actions;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteAction extends BaseEntityAction
{

    public function run(): JsonResponse
    {
        $response = new JsonResponse;
        try {
            $this->service->deleteById($this->id);
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
        } catch (\PhpLab\Core\Exceptions\NotFoundException $e) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }
        return $response;
    }

}