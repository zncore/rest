<?php

namespace ZnLib\Rest\Contract\Client;

use App\Bus\Domain\Entities\RpcRequestEntity;
use App\Bus\Domain\Entities\RpcResponseEntity;
use App\Bus\Domain\Entities\RpcResponseErrorEntity;
use App\Bus\Domain\Entities\RpcResponseResultEntity;
use App\Bus\Domain\Enums\RpcVersionEnum;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use ZnCore\Base\Enums\Http\HttpHeaderEnum;
use ZnCore\Base\Enums\Http\HttpMethodEnum;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Base\Exceptions\UnauthorizedException;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rest\Contract\Authorization\AuthorizationInterface;
use Psr\Http\Message\ResponseInterface;
use ZnLib\Rest\Helpers\RestResponseHelper;

class RpcClient
{

    private $guzzleClient;
    private $isStrictMode = true;
    private $accept = 'application/json';

    /** @var AuthorizationInterface */
    private $authAgent;

    public function __construct(Client $guzzleClient, AuthorizationInterface $authAgent = null)
    {
        $this->guzzleClient = $guzzleClient;
        $this->setAuthAgent($authAgent);
    }

    public function getAuthAgent(): ?AuthorizationInterface
    {
        return $this->authAgent;
    }

    public function setAuthAgent(AuthorizationInterface $authAgent = null)
    {
        $this->authAgent = $authAgent;
    }

    public function responseToRpcResponse(ResponseInterface $response): RpcResponseEntity
    {
        $data = RestResponseHelper::getBody($response);

        if (isset($data['error'])) {
            $rpcResponse = new RpcResponseErrorEntity();
        } else {
            $rpcResponse = new RpcResponseResultEntity();
        }

        EntityHelper::setAttributes($rpcResponse, $data);
        return $rpcResponse;
    }

    public function sendRequestByEntity(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $requestEntity->setJsonrpc(RpcVersionEnum::V2_0);

        $headers = [];
        $authToken = is_object($this->authAgent) ? $this->authAgent->getAuthToken() : null;
        if ($authToken) {
            $headers[HttpHeaderEnum::AUTHORIZATION] = $authToken;
        }
        $body = [
            'data' => json_encode(EntityHelper::toArray($requestEntity)),
        ];
        $response = $this->sendRequest($body, $headers);
        if ($response instanceof RpcResponseErrorEntity && $response->getError()['code'] == HttpStatusCodeEnum::UNAUTHORIZED) {
            //dd(1234);
        }
        return $response;
    }

    private function validateResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() != HttpStatusCodeEnum::OK) {
            throw new \Exception('Status code is not 200');
        }
        $data = RestResponseHelper::getBody($response);
        if (is_string($data)) {
            throw new \Exception($data);
        }
        if (is_array($data) && empty($data['jsonrpc'])) {
            throw new \Exception(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        if (version_compare($data['jsonrpc'], RpcVersionEnum::V2_0, '<')) {
            throw new \Exception('Unsupported RPC version');
        }
    }

    public function sendRequest(array $body = [], array $headers = []): RpcResponseEntity
    {
        $options = [
            RequestOptions::FORM_PARAMS => $body,
            RequestOptions::HEADERS => $headers,
        ];
        $options[RequestOptions::HEADERS]['Accept'] = $this->accept;
        try {
            $response = $this->guzzleClient->request(HttpMethodEnum::POST, '', $options);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if ($response == null) {
                throw new \Exception('Url not found!');
            }
        }
        if ($this->isStrictMode) {
            $this->validateResponse($response);
        }
        return $this->responseToRpcResponse($response);
    }
}
