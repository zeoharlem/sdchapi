<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/18/2018
 * Time: 2:51 PM
 */

namespace App\Models;


class Qrcodes extends BaseModel {

    public function initialize() {
        $this->hasOne(
            "walletcode",
            "App\\Models\\Wallet",
            "walletcode",
            array(
                "reusable"  => true,
                "alias"     => "QrWallets"
            )
        );
    }

    public function getQrWallets(){
        return $this->getRelated("QrWallets");
    }
}