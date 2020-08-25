<?php

namespace PhpLab\Rest\Actions;

use PhpLab\Core\Domain\Interfaces\Service\CrudServiceInterface;
use PhpLab\Rest\Base\BaseAction;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseEntityAction
 * @package PhpLab\Rest\Actions
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