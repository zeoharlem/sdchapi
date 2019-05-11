<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/23/2018
 * Time: 6:14 PM
 */

namespace App\Controllers;


use App\Models\Register;
use App\Models\Wallet;
use App\Models\WalletActivities;
use Phalcon\Mvc\Model\Transaction\Failed as FailedTransaction;
use Phalcon\Mvc\Model\Transaction\Failed;
use Phalcon\Mvc\Model\Transaction\Manager;
use PHPMailer\PHPMailer\Exception;

class WalletactivityController extends BaseInjectable {

    const LIMIT = 20;

    public function __construct() {
        parent::__construct();
    }

    public function topupAction(){
        $message            = "";
        $topUpAmount        = $this->request->getPost("amountToUp");
        $register_id        = $this->request->getPost("register_id");
        $wallet_id          = $this->request->getPost("wallet_id");
        $walletStackFlow    = Wallet::findFirst("register_id='".$register_id."' AND wallet_id='".$wallet_id."'");

        if($walletStackFlow != false){
            try{
                $manager        = new Manager();
                $transaction    = $manager->get();
                $walletStackFlow->setTransaction($transaction);
                $newBalance     = $walletStackFlow->balance + $topUpAmount;

                if(!$walletStackFlow->update(["balance" => $newBalance])){
                    $transaction->rollback("Unable to update Balance");
                }
                $walletStackRow = array(
                    "balance"       => $newBalance,
                    "wallet_id"     => $walletStackFlow->wallet_id,
                    "register_id"   => $walletStackFlow->register_id,
                    "walletcode"    => $walletStackFlow->walletcode,
                    "fullname"      => ucwords($walletStackFlow->getRegister()->firstname." ".$walletStackFlow->getRegister()->lastname),
                    "phone"         => $walletStackFlow->getRegister()->phone,
                    "email"         => $walletStackFlow->getRegister()->email,
                    "created_by"    => $this->request->getPost("createdBy"),
                    "purpose"       => $this->request->getPost("purpose"),
                    "amount"        => $topUpAmount,
                    "transactiontype"=> "top up",
                );

                $walletTransact = new WalletActivities();
                $walletTransact->setTransaction($transaction);
                if(!$walletTransact->create($walletStackRow)){
                    $message    = implode(",", $walletTransact->getMessages());
                    $transaction->rollback("Activity Error: ".$message);
                }

                $walletTransact->setEmail($walletStackRow['email']);
                $walletTransact->setFirstname($walletStackRow['fullname']);
                $walletTransact->setAmount($walletStackRow['amount']);
                $transaction->commit();

                $extArray   = [
                    "codename"          => $walletTransact->codename,
                    "transaction_date"  => date("F d,Y h:i:s A")
                ];

                $this->response->setJsonContent(array(
                    "status"    => "OK",
                    "data"      => $walletStackRow + $extArray,
                    "message"   => "successful | Mail: ".$message
                ));
                $this->response->send();
            }
            catch(FailedTransaction $failed){
                $this->response->setJsonContent(array(
                    "status"    => "ERROR",
                    "data"      => [],
                    "message"   => $failed->getMessage()
                ))->send();
            }
        }
    }

    public function getActivityAction($regId){
        $stackBuilder   = [];
        $offset = $this->request->isGet() ? $this->request->getQuery("offset") : $this->request->getPost("offset");
        //$regId  = empty($regid) ? $this->request->getQuery("register_id") : $this->request->getPost("register_id");
        $builderRow = $this->modelsManager->createBuilder()
            ->from(["r" => 'App\Models\WalletActivities'])
            ->where("r.register_id='".$regId."'")->limit(self::LIMIT, $offset)
            ->orderBy("r.date_added DESC")->getQuery()->execute();

        if($builderRow != false){
            foreach($builderRow as $keyBuilderRow => $valueBuilderRow){
                $stackBuilder[] = array(
                    "amount"                => $valueBuilderRow->amount,
                    "wallet_id"             => $valueBuilderRow->wallet_id,
                    "register_id"           => $valueBuilderRow->register_id,
                    "wallettransactions_id" => $valueBuilderRow->wallettransactions_id,
                    "date_added"            => date("F d, Y h:i:s A",strtotime($valueBuilderRow->date_added)),
                    "purpose"               => ucwords($valueBuilderRow->purpose),
                    "transactiontype"       => $valueBuilderRow->transactiontype,
                    "status"                => $valueBuilderRow->status,
                    "created_by"            => $valueBuilderRow->created_by,
                );
            }
            $this->response->setJsonContent([
                "status"    => "OK",
                "data"      => [
                    "data"  => $stackBuilder
                ],
                "message" => "Succesfull",
                "end"       => false
            ])->send();
        }
        else{
            $this->response->setJsonContent([
                "status"    => "ERROR",
                "data"      => [],
                "message"   => "Failure",
                "end"       => true
            ])->send();
        }

    }

    public function getAllActivityAction(){
        $offset     = $this->request->isGet() ? $this->request->getQuery("offset") : $this->request->getPost("offset");
        $builderRow = $this->modelsManager->createBuilder()->from(["r" => 'App\Models\WalletActivities'])
            ->limit(self::LIMIT, $offset)->orderBy("r.date_added DESC")->getQuery()->execute();
        if($builderRow != false){
            $this->response->setJsonContent([
                "status"    => "OK",
                "data"      => [
                    "data"  => $builderRow
                ],
                "message" => "Succesfull",
            ])->send();
        }
        else{
            $this->response->setJsonContent([
                "status"    => "ERROR",
                "data"      => [],
                "message"   => "Failure"
            ])->send();
        }

    }

    public function findWalletAction($walletcode){
        $userWalletRow  = [];
        $getWalletCode  = Wallet::findFirstByWalletcode($walletcode);
        $getStatusRow   = $this->expiryStateRow($getWalletCode->expiry_date);
        if($getWalletCode){
            if($getStatusRow == "available") {
                $userWalletRow  = [
                    "wallet_id"     => $getWalletCode->wallet_id,
                    "codename"      => $getWalletCode->codename,
                    "register_id"   => $getWalletCode->register_id,
                    "balance"       => $getWalletCode->balance,
                    "walletcode"    => $getWalletCode->walletcode,
                    "firstname"     => $getWalletCode->getRegister()->firstname,
                    "lastname"      => $getWalletCode->getRegister()->lastname,
                    "status"        => $getWalletCode->getRegister()->expiry_date,
                    "email"         => $getWalletCode->getRegister()->email,
                    "phone"         => $getWalletCode->getRegister()->phone,
                ];
                $this->response->setJsonContent(array(
                    "status"    => "OK",
                    "data"      => $userWalletRow,
                    "message"   => "Successful"
                ));
                $this->response->send();
            }
            else{
                $this->response->setJsonContent(
                    [
                        "status"    => "ERROR",
                        "data"      => [],
                        "message"   => "Expired | Needs Renewal"
                    ]
                )->send();
            }
        }
        else{
            $this->response->setJsonContent(array(
                "status"    => "ERROR",
                "data"      => [],
                "message"   => "Wallet Account Not Found"
            ));
            $this->response->send();
        }
    }

    public function setTaskAction(){
        $amt        = $this->request->getPost("amount");
        $username   = $this->request->getPost("username");
        $password   = $this->request->getPost("password");
        $getUserRow = Register::findFirstByEmail($username);
        if($getUserRow != false){
            if($this->security->checkHash($password, $getUserRow->password)){
                if($amt < $getUserRow->getRegisterWallet()->balance){
                    $getWalletReg   = $getUserRow->getRegisterWallet();
                    $walletAvr      = $getWalletReg->balance - $amt;
                    try{
                        $manager        = new Manager();
                        $transaction    = $manager->get();
                        $getWalletReg->setTransaction($transaction);
                        if(!$getWalletReg->update(["balance" => $walletAvr])){
                            $transaction->rollback("Unable to Update Wallet Now");
                        }
                        $transactArray      = [
                            "wallet_id"         => $getWalletReg->wallet_id,
                            "register_id"       => $getUserRow->register_id,
                            "amount"            => $this->request->getPost("amount"),
                            "created_by"        => $this->request->getPost("createdBy"),
                            "transactiontype"   => $this->request->getPost("type"),
                            "purpose"           => $this->request->getPost("purpose"),
                            "transaction_date"  => date("F d, Y h:i:s A"),
                            "balance"           => $walletAvr,
                            "status"            => 1,
                        ];

                        $walletTransaction  = new WalletActivities();
                        $walletTransaction->setTransaction($transaction);

                        if(!$walletTransaction->create($transactArray)){
                            $messages   = $walletTransaction->getMessages();
                            $transaction->rollback("Error: ".implode(",",$messages));
                        }

                        $transaction->commit();

                        $walletTransaction->setAmount($transactArray['amount']);
                        $walletTransaction->setFirstname($getUserRow->firstname);
                        $walletTransaction->setEmail($getUserRow->email);

                        $this->response->setJsonContent(array(
                            "status"    => "OK",
                            "data"      => $transactArray + ["codename" => $walletTransaction->codename],
                            "message"   => "Successful"
                        ));
                        $this->response->send();
                    }
                    catch (Failed $exception){
                        $this->response->setJsonContent(array(
                            "status"    => "ERROR",
                            "data"      => [],
                            "message"   => "Failure:".$exception->getMessage()
                        ));
                        $this->response->send();
                    }
                }
                else{
                    $this->response->setJsonContent(array(
                        "status"    => "ERROR",
                        "data"      => [],
                        "message"   => "Insufficient Funds"
                    ));
                    $this->response->send();
                }
            }
        }
        else{
            exit("checker");
        }
    }
}