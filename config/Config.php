<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/10/2018
 * Time: 11:51 PM
 */

return new Phalcon\Config(
    [
        'database'  => [

        ],
        'application'   => [
            'controllerDir' => 'app/controllers',
            'modelsDir'     => 'app/models',
            'baseUri'       => '/'
        ],
        'apiKeyToken'       => [
            'appKeyId'        => '106648356162553',
            'appKeySecret'    => '0f276ce1219ef7a45e936dc470a604b6',
        ]
    ]
);