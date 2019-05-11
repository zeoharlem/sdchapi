<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/10/2018
 * Time: 11:42 PM
 */

$Loader = new \Phalcon\Loader();
$Loader->registerNamespaces([
    'App\Services'      => realpath(__DIR__.'../app/services'),
    'App\Controllers'   => realpath(__DIR__.'../../app/controllers'),
    'App\Components'    => realpath(__DIR__.'../../app/components'),
    'App\Models'        => realpath(__DIR__.'../../app/models'),
    'Phalcon\Ext\Mailer'=> realpath(__DIR__ .'../../vendor/phalcon-ext/mailer/src'),
]);

$Loader->registerDirs(array(
    __DIR__ . '../app/models/',
    __DIR__ . '../app/controllers/',
    __DIR__ . '../app/components/',
));

$Loader->register();
