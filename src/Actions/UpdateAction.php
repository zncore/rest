<?php

namespace PhpLab\Rest\Actions;

use PhpLab\Core\Domain\Exceptions\UnprocessibleEntityException;
use PhpLab\Rest\Libs\Serializer\JsonRestSerializer;
use PhpLab\Core\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UpdateAction extends BaseEntityAction
{

    public function run(): JsonResponse
    {
        $response = new JsonResponse;
        $body = $this->request->request->all();
        try {
            $this->service->updateById($this->id, $body);
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
        } catch (UnprocessibleEntityException $e) {
            $errorCollection = $e->getErrorCollection();
            $serializer = new JsonRestSerializer($response);
            $serializer->serialize($errorCollection);
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (NotFoundException $e) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }
        return $response;
    }

}