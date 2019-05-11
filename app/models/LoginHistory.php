<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/18/2018
 * Time: 9:31 AM
 */

namespace App\Models;


use Phalcon\Db\RawValue;

class LoginHistory extends BaseModel {

    public $date_login;
    public $login_history_id;
    public $admin_id;

    public function initialize(){
        $this->setSource("login_history");
    }

    public function beforeValidationOnCreate(){
        $this->date_login   = new RawValue("NOW()");
    }
}