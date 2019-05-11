<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/18/2018
 * Time: 2:42 PM
 */

namespace App\Models;


use Phalcon\Db\RawValue;

class WalletActivities extends BaseModel {

    public $email, $firstname, $amount;

    public function initialize(){
        $this->hasOne(
            'register_id',
            'App\Models\Register',
            'register_id',
                array(
                    "alias" => "Register"
                )
            );

        $this->hasOne(
            'created_by',
            'App\Models\Admin',
            'admin_id',
            array(
                "alias" => "Admin"
            )
        );
        $this->setSource("wallettransactions");
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email    = $email;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname) {
        $this->firstname    = $firstname;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount) {
        $this->amount   = $amount;
    }

    /**
     * @return mixed
     */
    public function getAmount() {
        return $this->amount;
    }

    public function beforeValidationOnCreate(){
        $this->status       = 1;
        $this->codename     = time();
        $this->date_added   = new RawValue("NOW()");
    }

    public function afterCreate(){
        //Perform the mail function here
        $email      = $this->getRegister()->email;
        $firstname  = ucwords($this->getRegister()->firstname);

        $content    = $firstname.' | '.$email." Just had a <b>&#8358;".
            number_format($this->amount, 2)."</b> Transaction Now";

        $body       = $this->setHeaderRow().$this->setBodyRow().
            $this->mainBodyAction($this->firstname, $content).$this->setFooterRow();
        $messageRow = $this->getDI()->getMailer()->createMessage()->to($email, $firstname)
            ->subject('Notification')->content($body);

        //Set the CC addresses for this message
        $messageRow->cc(self::_ADMIN_LEVEL_2);
        $messageRow->bcc(self::_ADMIN_LEVEL_1);
        $messageRow->send();
    }

    public function getRegister(){
        return $this->getRelated("Register");
    }

    public function getAdmin(){
        return $this->getRelated("Admin");
    }
}