<?php

namespace ZnLib\Rest\Symfony4\Actions;

use ZnLib\Rest\Libs\Serializer\JsonRestSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ViewAction extends BaseEntityAction
{

    public function run(): JsonResponse
    {
        $response = new JsonResponse;
        try {
            $entity = $this->service->findOneById($this->id, $this->query);
            $serializer = new JsonRestSerializer($response);
            $serializer->serialize($entity);
        } catch (\ZnCore\Domain\Entity\Exceptions\NotFoundException $e) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }
        return $response;
    }

}