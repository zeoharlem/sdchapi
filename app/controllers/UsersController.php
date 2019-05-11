<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/11/2018
 * Time: 12:55 AM
 */

namespace App\Controllers;


use App\Models\Qrcodes;
use App\Models\Register;
use App\Models\Wallet;
use Phalcon\Db\Exception;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Transaction\Failed;
use Phalcon\Mvc\Model\Transaction\Manager;

class UsersController extends BaseInjectable {
    const LIMIT = 10;

    public function __construct() {
        parent::__construct();
    }

    public function getUserListAction() {
        $stackflow  = array();
        $offset     = $this->request->getQuery("offset");
        if($this->request->hasQuery("search")){
            $searchQuery    = $this->request->getQuery("search");
            if(!empty($searchQuery)){
                $buildUser  = $this->modelsManager->createBuilder()
                    ->from(['r' => 'App\Models\Qrcodes'])
                    ->where("r.codename LIKE :codename:",array("codename" => "%".$searchQuery."%"))
                    ->getQuery()->execute();

                if($buildUser){
                    foreach($buildUser as $keyRow => $valueRow){
                        $getRegister    = $valueRow->getQrWallets()->getRegister();
                        $firstLetter    = substr($getRegister->firstname, 0, 1);
                        $stackflow[] = array(
                            "firstLetter"   => $firstLetter,
                            "firstname"     => $getRegister->firstname,
                            "lastname"      => $getRegister->lastname,
                            "email"         => $getRegister->email,
                            "phone"         => $getRegister->phone,
                            "address"       => $getRegister->address,
                            "codename"      => $getRegister->codename,
                            "role"          => $getRegister->role,
                            "hospital_number"   => $getRegister->hospital_number,
                            "date_of_register"  => $getRegister->date_of_register,
                            "register_id"   => $getRegister->register_id,
                            "cashBalance"   => $getRegister->getRegisterWallet()->balance,
                            "wallet_id"     => $getRegister->getRegisterWallet()->wallet_id,
                            "walletcode"    => $getRegister->getRegisterWallet()->walletcode,
                            "date_created"  => $getRegister->getRegisterWallet()->date_created
                        );
                    }
                    $this->response->setJsonContent([
                        "status"  => "OK",
                        "data"    => [
                            "data"  => $stackflow
                        ],
                        "end"     => false
                    ])->send();
                }
                else{
                    $this->response->setJsonContent([
                        "status"  => "ERROR",
                        "data"    => [
                            "data"  => []
                        ],
                        "end"     => true
                    ])->send();
                }

            }
            else{
                $buildUser  = $this->modelsManager->createBuilder()
                    ->from(['r' => 'App\Models\Register'])
                    ->limit(self::LIMIT, $offset)
                    ->orderBy("r.register_id DESC")
                    ->getQuery()->execute();

                if($buildUser != false) {
                    foreach ($buildUser as $keyBuild => $valueBuild) {
                        $firstLetter = substr($valueBuild->firstname, 0, 1);
                        $stackflow[] = array(
                            "firstLetter"   => $firstLetter,
                            "firstname"     => $valueBuild->firstname,
                            "lastname"      => $valueBuild->lastname,
                            "email"         => $valueBuild->email,
                            "phone"         => $valueBuild->phone,
                            "address"       => $valueBuild->address,
                            "codename"      => $valueBuild->codename,
                            "role"          => $valueBuild->role,
                            "hospital_number"   => $valueBuild->hospital_number,
                            "date_of_register"  => $valueBuild->date_of_register,
                            "register_id"   => $valueBuild->register_id,
                            "cashBalance"   => $valueBuild->getRegisterWallet()->balance,
                            "wallet_id"     => $valueBuild->getRegisterWallet()->wallet_id,
                            "walletcode"    => $valueBuild->getRegisterWallet()->walletcode,
                            "date_created"  => $valueBuild->getRegisterWallet()->date_created
                        );
                    }
                    $this->response->setJsonContent([
                        "status"  => "OK",
                        "data"    => [
                            "data"  => $stackflow
                        ],
                        "end"     => false
                    ])->send();
                }
                else{
                    $this->response->setJsonContent([
                        "status"  => "ERROR",
                        "data"    => [],
                        "end"     => true
                    ])->send();
                }
            }
        }
    }

    /**
     * @throws \Phalcon\Exception
     */
    public function createUserAction(){
        $code       = $this->request->getPost("walletcode");
        $qrStatus   = Qrcodes::findFirst("walletcode = '".$code."' AND status = 0");
        if($qrStatus != false) {
            $arrays = array(
                "email"             => trim($this->request->getPost("email")),
                "phone"             => trim($this->request->getPost("phone")),
                "address"           => trim($this->request->getPost("address")),
                "lastname"          => trim($this->request->getPost("lastname")),
                "firstname"         => trim($this->request->getPost("firstname")),
                "hospital_number"   => trim($this->request->getPost("hospital_number")),
                "merchants_id"      => trim($this->request->getPost("merchant_id")),
                "walletcode"        => trim($this->request->getPost("walletcode")),
            );
            //var_dump($arrays); exit;
            try {
                $userReg        = new Register();
                $manager        = new Manager();
                $transaction    = $manager->get();

                $userReg->setTransaction($transaction);
                if ($userReg->create($arrays) == false) {
                    $messages   = implode(", ", $userReg->getMessages());
                    $transaction->rollback("Unable to Register: ".$messages);
                }

                $column = array(
                    "balance"       => 0,
                    "type"          => "user",
                    "codename"      => $userReg->codename,
                    "register_id"   => $userReg->register_id,
                    "walletcode"    => $qrStatus->walletcode,
                    "image"         => $qrStatus->image,
                );

                $walletRow  = new Wallet();
                $walletRow->setTransaction($transaction);
                if($walletRow->create($column) == false){
                    $messages   = implode(", ", $walletRow->getMessages());
                    $transaction->rollback("Unalbe to create Wallet: ".$messages);
                }

                $transaction->commit();

                $qrStatus->update(["status" => 1]);
                $this->response->setJsonContent([
                    "status"    => "OK",
                    "data"      => $arrays,
                    "message"   => "Successful",
                    "others"    => $this->getMessages()
                ])->send();
            }
            catch (Failed $exception) {
                $this->response->setJsonContent(array(
                    "status" => "ERROR",
                    "data" => "",
                    "message" => $exception->getMessage()
                ));
                $this->response->send();
            }
        }
        else{
            $this->response->setJsonContent(array(
                "status" => "ERROR",
                "data" => "",
                "message" => "Wallet Used or Not found"
            ));
            $this->response->send();
        }
    }

    public function getUserDetailAction($id) {
        $userDetail = Register::findFirstByRegister_id($id);
        if($userDetail != false){
            $this->response->setJsonContent([
                "status"    => "OK",
                "data"      => $userDetail,
                "message"   => $this->expiryStateRow($userDetail)
            ])->send();
        }
        else{
            $this->response->setJsonContent([
                "status"    => "ERROR",
                "data"      => "",
                "message"   => "User Not Found"
            ])->send();
        }
    }

    public function updateUserAction($id) {
        $user   = Register::findFirstByRegister_id($id);
        if($user){
            if($user->update($this->request->getPut())){
                $this->response->setJsonContent([
                    "status"    => "OK",
                    "data"      => $user,
                    "message"   => ""
                ])->send();
            }
            else{
                $this->response->setJsonContent([
                    "status"    => "ERROR",
                    "data"      => "",
                    "message"   => "Unable to Update: "
                ])->send();
            }
        }
        else{
            $this->response->setJsonContent([
                "status"    => "ERROR",
                "data"      => "",
                "message"   => "User not found: ".implode(",", $user->getMessages())
            ])->send();
        }
    }

    public function deleteUserAction($id) {
        
    }

    public function activationAction(){
        $register_id    = $this->request->getPost("register_id","int");
        $getUserRow     = Register::findFirstByRegister_id($register_id);
        if($getUserRow != false){
            $updateRow  = $getUserRow->update(["role" => "user"]);
            if($updateRow){
                $this->response->setJsonContent(
                    [
                        "status"    => "OK",
                        "data"      => [],
                        "message"   => $getUserRow->firstname."'s Account Successfully Activated"
                    ]
                )->send();
            }
            else{
                $this->response->setJsonContent(
                    [
                        "status"    => "ERROR",
                        "data"      => [],
                        "message"   => "Unable to Activate Account"
                    ]
                )->send();
            }
        }
        else{
            $this->response->setJsonContent(
                [
                    "status"    => "ERROR",
                    "data"      => [],
                    "message"   => "Seems there is a problem, Contact your Administrator"
                ]
            )->send();
        }
    }
}