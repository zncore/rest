<?php

namespace PhpLab\Rest\Actions;

use PhpLab\Rest\Libs\Serializer\JsonRestSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ViewAction extends BaseEntityAction
{

    public function run(): JsonResponse
    {
        $response = new JsonResponse;
        try {
            $entity = $this->service->oneById($this->id, $this->query);
            $serializer = new JsonRestSerializer($response);
            $serializer->serialize($entity);
        } catch (\PhpLab\Core\Exceptions\NotFoundException $e) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }
        return $response;
    }

}