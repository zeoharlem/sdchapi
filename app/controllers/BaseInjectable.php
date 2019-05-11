<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 8/12/2018
 * Time: 12:46 AM
 */

namespace App\Controllers;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

abstract class BaseInjectable extends \Phalcon\Di\Injectable {
    const ERROR_NOT_FOUND       = 1;
    const ERROR_INVALID_REQUEST = 2;
    private $messages           = [];
    private $_apiKey, $_apiId;
    public $_phpMailer;

    public function __construct() {
        $this->_phpMailer   = new PHPMailer(true);
    }

    public function getApiKey(){
        return $this->_apiKey;
    }

    public function getApiId(){
        return $this->_apiId;
    }

    public function setApiKey($keyString){
        $this->_apiKey  = $keyString;
    }

    public function setApiId($keyIdString){
        $this->_apiId   = $keyIdString;
    }

    public function displayJsonRow($content){
        return json_encode($content);
    }

    /**
     * @param string $type
     * @return array|string
     */
    public function getMessages($type = 'string'){
        return $type == 'array' ? $this->messages : implode(',', $this->messages);
    }

    public function expiryStateRow($userDetail) {
        $expiryDate     = strtotime($userDetail->expiry_date);
        $currentDate    = strtotime(date("Y-m-d h:i:s"));
        return $currentDate > $expiryDate ? "expired" : "available";
    }
}