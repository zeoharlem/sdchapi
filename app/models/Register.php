<?php
/**
 * Created by PhpStorm.
 * User: Theophilus
 * Date: 12/16/2018
 * Time: 2:15 PM
 */

namespace App\Models;


use Phalcon\Db\RawValue;
use Phalcon\Validation;

class Register extends BaseModel {

    public $date_of_register;
    public $register_id;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    public $address;
    public $password;
    public $codename;
    public $role;
    public $pwsdlook;
    public $user_last_login;
    public $status;
    public $merchants_id;
    public $hospital_number;
    public $expiry_date;

    public function initialize(){
        $this->skipAttributesOnCreate([
            "user_last_login",
        ]);
        $this->allowEmptyStringValues([
            "status"
        ]);

        $this->belongsTo(
            "register_id",
            "App\\Models\\Wallet",
            "register_id",
            array(
                "reusable"  => true,
                "alias"     => "RegisterWallet"
            )
        );
    }

    public function beforeValidationOnCreate(){
        $this->status           = 0;
        $this->role             = "user";
        $this->password         = "admin";
        $this->pwsdlook         = "admin";
        $this->date_of_register = new RawValue("NOW()");
        $this->codename         = $this->getDI()->get('component')->helper->makeRandomInts();
        $this->expiry_date      = $this->setExpiryDate(date('Y-m-d h:i:s'));
    }

    public function beforeValidationOnUpdate(){
        $this->user_last_login  = new RawValue("NOW()");
    }

    public function getRegisterWallet(){
        return $this->getRelated("RegisterWallet");
    }

    public function afterCreate(){
        //Perform the mail function here
        $content    = "Your Username: ".$this->email." | Password: admin. <a href='http://sdchospital.com/ewallet'>Login</a> to Update your Password.";
        $body       = $this->setHeaderRow().$this->setBodyRow().$this->mainBodyAction($this->firstname, $content).$this->setFooterRow();
        $messageRow = $this->getDI()->getMailer()->createMessage()
            ->to($this->email, $this->firstname)
            ->subject('Notification')
            ->content($body);
        //Set the CC addresses for this message
        $messageRow->cc(self::_ADMIN_LEVEL_1);
        $messageRow->bcc(self::_ADMIN_LEVEL_2);
        $messageRow->send();
    }

    public function afterUpdate(){
        $content    = "Your Account has been Re-Activated. You can now make use of your Wallet Card";
        $body       = $this->setHeaderRow().$this->setBodyRow().$this->mainBodyAction($this->firstname, $content).$this->setFooterRow();
        $messageRow = $this->getDI()->getMailer()->createMessage()
            ->to($this->email, $this->firstname)
            ->subject('Re-Activation Notification')
            ->content($body);
        //Set the CC addresses for this message
        $messageRow->cc(self::_ADMIN_LEVEL_1);
        $messageRow->bcc(self::_ADMIN_LEVEL_2);
        $messageRow->send();
    }

    public function validation(){
        $validation = new Validation();
        $security   = new \Phalcon\Security();
        $validation->add('email', new Validation\Validator\Email(array(
            'model'     => $this,
            'message'   => 'Please enter correct email address'
        )));
        $validation->add("email", new Validation\Validator\Uniqueness([
            'model'     => $this,
            'message'   => 'Email already existed'
        ]));
        $validation->add("phone", new Validation\Validator\Uniqueness([
            'model'     => $this,
            'message'   => 'Phone Number Already Existed'
        ]));
        $validation->add('hospital_number',new Validation\Validator\Uniqueness(array(
            'model'     => $this,
            'message'   => 'Hospital Number address already existed'
        )));
        $this->password = $security->hash($this->password);
        return $this->validate($validation);

    }

    private function setExpiryDate($dateRegi){
        return strtotime(date("Y-m-d h:i:s", strtotime($dateRegi)) . "+1 year");
    }
}