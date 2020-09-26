<?php

namespace ZnLib\Rest\Symfony4\Actions;

use ZnCore\Domain\Interfaces\Service\CrudServiceInterface;
use ZnLib\Rest\Symfony4\Base\BaseAction;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseEntityAction
 * @package ZnLib\Rest\Symfony4\Actions
 *
 * @property CrudServiceInterface $service
 */
abstract class BaseEntityAction extends BaseAction
{

    public $id;

    public function __construct(object $service, Request $request, $id)
    {
        parent::__construct($service, $request);
        $this->id = $id;
    }

}