<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 1/4/2019
 * Time: 12:09 PM
 */

namespace App\Controllers;


use App\Models\Admin;
use App\Models\Register;
use Phalcon\Exception;

class PasswordController extends BaseInjectable {

    public function __construct() {
        parent::__construct();
    }

    public function resetAction(){
        $register   = Admin::findFirstByAdmin_id($this->request->getPost("admin_id"));
        if($register != false){
            $checkOldPswdAction = $this->security->checkHash($this->request->getPost("oldpswd"), $register->password);
            if($checkOldPswdAction != false){
                try{
                    $newPasswd  = $this->security->hash($this->request->getPost("newpswd"));
                    $updateRow  = $register->update(["password" => $newPasswd]);
                    if(!$updateRow){
                        throw new Exception("Unable to Update Password".$newPasswd);
                    }
                    $this->response->setJsonContent([
                        "status"    => "OK",
                        "data"      => [],
                        "message"   => "Update was successful"
                    ])->send();
                }
                catch (Exception $exception){
                    $this->response->setJsonContent([
                        "status"    => "ERROR",
                        "data"      => [],
                        "message"   => $exception->getMessage()
                    ])->send();
                }
            }
            else{
                $this->response->setJsonContent([
                    "status"    => "ERROR",
                    "data"      => [],
                    "message"   => "Old Password Incorrect"
                ])->send();
            }
        }
        else{
            $this->response->setJsonContent([
                "status"    => "ERROR",
                "data"      => [],
                "message"   => "User Not Found".$this->request->getPost("register_id")
            ])->send();
        }
    }

}