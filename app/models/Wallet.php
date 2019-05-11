<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/18/2018
 * Time: 2:40 PM
 */

namespace App\Models;


use Phalcon\Db\RawValue;
use Phalcon\Validation;

class Wallet extends BaseModel {

    public $date_created;

    public function initialize(){
        $this->belongsTo(
            "register_id",
            "App\\Models\\Register",
            "register_id",
            array(
                "reusable"  => true,
                "alias"     => "WalletRegister"
            )
        );

        $this->hasMany(
            "register_id",
            "App\\Models\\WalletActivities",
            "register_id",
            array(
                "alias"  => "ManyWalletActs"
            )
        );

        $this->skipAttributesOnUpdate(["date_created"]);
        $this->skipAttributesOnCreate(["date_modified"]);
    }

    public function getRegister(){
        return $this->getRelated("WalletRegister");
    }

    public function getManyWalletActs(){
        return $this->getRelated("ManyWalletActs");
    }

    public function beforeValidationOnUpdate(){
        $this->date_modified    = new RawValue("NOW()");
    }

    public function beforeValidationOnCreate(){
        $this->activated    = 0;
        $this->date_created = new RawValue("NOW()");
    }

    public function validation(){
        $validate   = new Validation();
        $validate->add("register_id", new Validation\Validator\Uniqueness(
            [
                'model'     => $this,
                'message'   => 'User Already Existed'
            ]
        ));

        $validate->add("codename", new Validation\Validator\Uniqueness(
            [
                'model'     => $this,
                'message'   => 'COdename Already Existed'
            ]
        ));
        return $this->validate($validate);
    }
}