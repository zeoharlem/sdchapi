<?php
if(!ini_get('date.timezone')){
    date_default_timezone_set('GMT');
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of index
 *
 * @author Theophilus
 */
use Phalcon\Mvc\Micro as Micro;
//use \Phalcon\Loader as Loader;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Http\Request\Exception as RequestException;

require_once ('vendor/autoload.php');
require_once('extensions/PHPMailer/src/Exception.php');
require_once('extensions/PHPMailer/src/PHPMailer.php');

try{
    // Setup loader
    require_once __DIR__.'/config/Loader.php';

    // Read the configuration
    $config     = new ConfigIni(__DIR__ . '/config/config.ini');
    $configKey  = require_once __DIR__ . '/config/Config.php';

    $di     = require_once __DIR__ . '/config/DependencyInjection.php';

    // Start Micro
    $app = new Micro($di);

    // Setup the database service

    $app->setDI($di);

    $apiKeyToken    = $configKey['apiKeyToken'];
    require_once __DIR__ . '/config/Routes.php';

    $app->handle();
}
catch (RequestException $ex){
    $app->response->setStatusCode(400, 'Bad Request')
        ->setJsonContent([$ex->getMessage()])->send();
}
catch (\Exception $ex) {
    $app->response->setStatusCode(500, "Server Error");
    $app->response->setJsonContent($ex->getMessage());
    $app->response->send();
}

