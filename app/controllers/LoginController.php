<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/16/2018
 * Time: 11:19 PM
 */

namespace App\Controllers;


use App\Models\Admin;
use App\Models\LoginHistory;
use Phalcon\Exception;

class LoginController extends BaseInjectable {

    public function __construct() {
        parent::__construct();
        //echo $this->security->checkHash("twenty");
    }

    public function loginAction($username, $password){
        $register   = Admin::findFirstByUsername($username);
        if($register != false){
            if($this->security->checkHash($password, $register->password)){
                try{
                    $loginHistory   = new LoginHistory();
                    if(!$loginHistory->create(['admin_id'=>$register->admin_id])){
                        throw new Exception("Error:".implode(",", $loginHistory->getMessages()));
                    }
                }
                catch (Exception $exception){
                    $this->response->setJsonContent([
                        "status"    => "ERROR",
                        "data"      => "",
                        "message"   => $exception->getMessage()
                    ])->send();
                }
                $this->response->setJsonContent([
                    "status"    => "OK",
                    "data"      => $this->setRegisterAction($register),
                    "message"   => "success"
                ])->send();
            }
            else{
                $this->response->setJsonContent([
                    "status"    => "ERROR",
                    "data"      => "",
                    "message"   => "Password Incorrect"
                ])->send();
            }
        }
        else{
            $this->response->setJsonContent([
                "status"    => "ERROR",
                "data"      => "",
                "message"   => "Username|Email not found"
            ])->send();
        }
    }

    private function setRegisterAction(Admin $register){
        return array(
            "register_id"   => $register->admin_id,
            "fullname"      => $register->fullname,
            "username"      => $register->username,
            "codename"      => $register->codename,
            "privy"         => $register->privy,
            "role"          => $register->role,
            "apiTokenKey"       => $this->configKey['apiKeyToken'],
            "accessKeyToken"    => $this->security->hash($register->codename)."_".time(),
        );
    }
}