<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 1/6/2019
 * Time: 2:15 AM
 */

namespace App\Controllers;


use App\Models\Register;
use App\Models\Wallet;

class BalanceController extends BaseInjectable {

    public function __construct() {
        parent::__construct();
    }

    public function checkBalanceAction(){
        $walletcode     = $this->request->getPost("wallet");
        $getWallet      = Wallet::findFirstByWalletcode($walletcode);
        if($getWallet != false){
            $overflowStack  = [
                "register_id"   => $getWallet->register_id,
                "cashbalance"   => number_format($getWallet->balance,2),
                "fullname"      => $getWallet->getRegister()->firstname." ".$getWallet->getRegister()->lastname,
                "todays_date"   => date("F d, Y h:i:s A")
            ];
            $this->response->setJsonContent([
                "status"    => "OK",
                "data"      => $overflowStack,
                "message"   => "Available"
            ]);
            $this->response->send();
        }
        else{
            $this->response->setJsonContent([
                "status"    => "ERROR",
                "data"      => [],
                "message"   => "Account Not Found"
            ])->send();
        }
    }

}