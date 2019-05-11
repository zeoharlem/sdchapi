<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 1/2/2019
 * Time: 3:47 PM
 */

namespace App\Controllers;


use App\Models\WalletActivities;

class HistoryController extends BaseInjectable {
    const LIMIT = 20;

    public function __construct() {
        parent::__construct();
    }

    public function loadAction($offset){
        $stackActivityFlow  = [];
        $dateQuery  = $this->request->hasQuery("dateTrans") ? $this->request->getQuery("dateTrans") : date("Y-m-d");

        $walletActBuilder = $this->modelsManager->createBuilder()
            ->from(["r" => "App\\Models\\WalletActivities"])
            ->where("r.date_added LIKE '%".$dateQuery."%'")
            ->limit(self::LIMIT, $offset)
            ->orderBy("r.wallettransactions_id DESC")
            ->getQuery()->execute();

        if($walletActBuilder != false) {
            foreach ($walletActBuilder as $keyBuildRow => $valueBuildRow) {
                $stackActivityFlow[] = [
                    "wallettransactions_id" => $valueBuildRow->wallettransactions_id,
                    "wallet_id" => $valueBuildRow->wallet_id,
                    "register_id" => $valueBuildRow->register_id,
                    "amount" => $valueBuildRow->amount,
                    "date_added" => date("F d,Y h:i:s A", strtotime($valueBuildRow->date_added)),
                    "fullname" => ucwords($valueBuildRow->getRegister()->lastname . " " . $valueBuildRow->getRegister()->firstname),
                    "created_by" => $valueBuildRow->getAdmin()->fullname,
                    "transaction_type" => $valueBuildRow->transactiontype,
                    "purpose" => ucwords($valueBuildRow->purpose),
                    "transaction_code" => $valueBuildRow->codename,
                ];
            }

            $amountStack = WalletActivities::sum([
                "column" => "amount",
                "conditions" => "date_added LIKE '%" . $dateQuery . "%'"
            ]);

            $this->response->setJsonContent([
                "status" => "OK",
                "data" => [
                    "data" => $stackActivityFlow
                ],
                "end"   => false,
                "total" => "" . number_format($amountStack)
            ])->send();
        }
        else{
            $this->response->setJsonContent([
                "status"    => "Error",
                "data"      => [],
                "end"       => true,
                "total"     => "0.00"
            ]);
        }
    }
}