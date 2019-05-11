<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/10/2018
 * Time: 11:42 PM
 */

use Phalcon\Db\Adapter\Pdo\Mysql as MysqlAdapter;

$mailerConfig   = [
    'driver'     => 'mail',
    'from'       => [
        'email' => 'info@sdchospital.com',
        'name'  => 'St. Dominic Catholic Hospital'
    ]
];

$dependencyInjector = new \Phalcon\Di\FactoryDefault();

$dependencyInjector->setShared('response', function (){
    $response   = new \Phalcon\Http\Response();
    $response->setContentType('application/json','utf-8');
    return $response;
});

$dependencyInjector->setShared('config', $config);
$dependencyInjector->setShared('configKey', $configKey);

$dependencyInjector->set("db", function () use ($config){
    return new MysqlAdapter(
        array(
            "host"      => $config->database->host,
            "username"  => $config->database->username,
            "password"  => $config->database->password,
            "dbname"    => $config->database->dbname
        )
    );
});

$dependencyInjector->set("component", function(){
    $objectClass            = new stdClass();
    $objectClass->helper    = new \App\Components\Helper();
    return $objectClass;
});

$dependencyInjector->set("request", function(){
    return new \Phalcon\Http\Request();
});

$dependencyInjector->setShared("mailer", function() use ($mailerConfig){
    $mailer = new \Phalcon\Ext\Mailer\Manager($mailerConfig);
    return $mailer;
});

return $dependencyInjector;