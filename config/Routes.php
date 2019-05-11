<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/10/2018
 * Time: 11:43 PM
 */

use Phalcon\Mvc\Micro\Collection as MicroCollection;

$getApiId   = $apiKeyToken['appKeyId'];
$getApiKey  = $apiKeyToken['appKeySecret'];
$queryApiId = '';
$queryApiKey= '';

if (($app->request->hasQuery('apiKey') && $app->request->hasQuery('apiId')) ||
    ($app->request->hasPost('apiKey') && $app->request->hasPost('apiId'))) {
    try {
        if ($app->request->isGet()) {
            $queryApiId     = $app->request->getQuery('apiId');
            $queryApiKey    = $app->request->getQuery('apiKey');
        }
        elseif ($app->request->isPost()) {
            $queryApiId     = $app->request->getPost('apiId');
            $queryApiKey    = $app->request->getPost('apiKey');
        }
        if (($getApiId !== $queryApiId) || ($getApiKey !== $queryApiKey)) {
            throw new Exception("Api Key and Api Id not set");
        }

        /**
         * UserCollection
         */
        $userCollections = new MicroCollection();
        $userCollections->setHandler('App\Controllers\UsersController', true);
        $userCollections->setPrefix('/users');
        $userCollections->get('/list', 'getUserListAction');
        $userCollections->get('/details/{id:[0-9]+}', 'getUserDetailAction');
        $userCollections->put('update/{id:[0-9]*}', 'updateUserAction');
        $userCollections->delete('/delete/{id:[0-9]*}', 'deleteUserAction');
        $userCollections->post('/create', 'createUserAction');
        $app->mount($userCollections);

        /**
         * Login Collections
         */
        $loginCollection    = new MicroCollection();
        $loginCollection->setHandler('App\Controllers\LoginController', true);
        $loginCollection->setPrefix('/login');
        $loginCollection->get('/{username}/{password}', 'loginAction');
        $app->mount($loginCollection);

        /**
         * Wallet Activities Collections
         */
        $walletActivityCollection   = new MicroCollection();
        $walletActivityCollection->setHandler('App\Controllers\WalletactivityController', true);
        $walletActivityCollection->setPrefix('/wallet');
        $walletActivityCollection->get('/find/{walletcode}','findWalletAction');
        $walletActivityCollection->get('/getlist/{id:[0-9]*}/{walletid}','getActivityAction');
        $walletActivityCollection->get('/getalllist', 'getAllActivityAction');
        $walletActivityCollection->post('/set','setTaskAction');
        $walletActivityCollection->post('/topup', 'topupAction');
        $app->mount($walletActivityCollection);

        /**
         * History Activities Collections
         */
        $history    = new MicroCollection();
        $history->setHandler('App\Controllers\HistoryController', true);
        $history->setPrefix('/history');
        $history->get('/{offset:[0-9]*}','loadAction');
        $app->mount($history);

        /**
         * Password ResetAction Collections
         */
        $password   = new MicroCollection();
        $password->setHandler('App\Controllers\PasswordController', true);
        $password->setPrefix('/password');
        $password->post('/reset','resetAction');
        $app->mount($password);

        /**
         * Balance CheckAction Collections
         */
        $balance   = new MicroCollection();
        $balance->setHandler('App\Controllers\BalanceController', true);
        $balance->setPrefix('/balance');
        $balance->post('/','checkBalanceAction');
        $app->mount($balance);

        $app->notFound(function()use ($app){
           $app->response->setJsonContent([
               $app->request->getMethod().' '.$app->request->getURI(),
               "URI not found in the module"
           ])->send();
        });
    }
    catch (Exception $ex) {
        $app->notFound(function() use ($app){
            exit('sdfsdfdsf');
        });
        $app->response->setJsonContent([$ex->getMessage()]);
    }
}
else{
    $app->notFound(function() use ($app){
        $app->response->setStatusCode(401, "Not found")
        ->setJsonContent("Not Available")->send();
    });
}

//var_dump($apiKeyToken); exit;