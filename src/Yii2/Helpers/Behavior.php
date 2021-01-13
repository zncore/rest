<?php

namespace ZnLib\Rest\Yii2\Helpers;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii2bundle\account\domain\v3\filters\auth\ConsoleAuth;
use ZnBundle\User\Yii2\Api\Filters\Auth\HttpTokenAuth;
use ZnCore\Base\Helpers\EnvHelper;
use ZnLib\Rest\Helpers\CorsHelper;

class Behavior {
	
	static function modifyActions() {
		return ['create', 'update', 'delete'];
	}
	
	static function auth($only = null) {
		if(EnvHelper::isConsole()) {
			return self::consoleAuth($only);
		}
		if(APP == FRONTEND || APP == BACKEND) {
            return self::webAuth($only);
        }
		if(APP == API) {
			return self::apiAuth($only);
		}
	}
	
	private static function consoleAuth($only = null) {
		$config = [
			'class' => ConsoleAuth::class,
		];
		if(!empty($only)) {
			$config['only'] = ArrayHelper::toArray($only);
		}
		return $config;
	}
	
	private static function apiAuth($only = null) {
		$config = [
			'class' => HttpTokenAuth::class,
		];
		if(!empty($only)) {
			$config['only'] = ArrayHelper::toArray($only);
		}
        $config['except'] = ['options'];
		return $config;
	}

    private static function webAuth($only = null) {
        $config = [
            'class' => AccessControl::class,
        ];
        if(!empty($only)) {
            $config['only'] = ArrayHelper::toArray($only);
            $config['rules'] = [
                [
                    'allow' => true,
                    'actions' => ArrayHelper::toArray($only),
                    'roles' => ['@'],
                ],
            ];
        } else {
            $config['rules'] = [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ];
        }
        return $config;
    }

    static function verb($actions) {
		foreach($actions as $actionName => &$actionMethods) {
			$actionMethods = ArrayHelper::toArray($actionMethods);
		}
		$config = [
			'class' => VerbFilter::class,
			'actions' => $actions,
		];
		return $config;
	}
	
	static function access($roles, $only = null, $allow = true) {
		$roles = is_array($roles) ? $roles : [$roles];
		$config = [
			'class' => AccessControl::class,
			'rules' => [
				[
					'allow' => $allow,
					'roles' => $roles,
				],
			],
		];
		if(!empty($only)) {
			$config['only'] = ArrayHelper::toArray($only);
		}
		return $config;
	}
	
	static function cors() {
        return CorsHelper::generate();

		// todo: guide
		/*$cors = param('cors.default', false);
		if(!$cors) {
			$cors = CorsHelper::generate();
		}
		return $cors;*/
	}
	
}
