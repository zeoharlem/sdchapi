<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Models;

/**
 * Description of BaseModel
 *
 * @author web
 * Note: Please methods starting with __postRaw has no advantage
 * It's just used to monitor methods written by the theophilus for brevity
 * most times the __ are mostly private assessors
 */

class BaseModel extends \Phalcon\Mvc\Model{
    const MODEL_REPLACE     = 'REPLACE';
    const MODEL_INSERT      = 'INSERT';
    //const _ADMIN_LEVEL_1    = 'zeoharlem@yahoo.co.uk';
    //const _ADMIN_LEVEL_2    = 'theophilus.alamu8@gmail.com';
    const _ADMIN_LEVEL_1    = 'ijeomajesu@yahoo.co.uk';
    const _ADMIN_LEVEL_2    = 'ujuasogwaop@yahoo.com';
    /**
     * @param array $array
     * @param string $table
     * @param string $others
     * @return array
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Post Associative arrays
     */
    public function __postRawArray(array $array, $table, $others=''){
        $sqlState = "INSERT INTO $table SET ";
        foreach($array as $key => $value){
            $_stmtArrayQuery[] = $key."='".$value."'";
        }
        $sqlState .= implode(", ", $_stmtArrayQuery);
        $sqlQueryNow = !empty($others) ? $sqlState.$others : $sqlState;
        $return = $this->getReadConnection()->execute($sqlQueryNow);
        return $return;
    }
    
    /**
     * @param array $arrays
     * @param type $table
     * @param type $others
     * @return array
     * @covers className::__postIntKeyRaw($arrays, $table, $others);
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     */
    public function __postIntKeyRaw(array $arrays, $table, $others=''){
        foreach($arrays as $keys => $values){
            $sqlState = "INSERT INTO $table SET ";
            foreach($values as $index => $element){
                $_stmtArrayQuery[] = $index."='".$element."'";
            }
            $sqlState .= implode(", ", $_stmtArrayQuery);
            $sqlQueryNow = !empty($others) ? $sqlState.$others : $sqlState;
            $result = $this->getReadConnection()->execute($sqlQueryNow);
            $_stmtArrayQuery = array();
            //var_dump($sqlQueryNow);
        }
        return $result;
    }
    
    /**
     * @param array $arrays
     * @param string $table
     * @param string $others
     * @return array
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Post a multi-dimensional array
     */
    public function __postMultiRaw(array $arrays, $table, $others=''){
        foreach($arrays as $keys => $values){
            for($x = 0; $x < count($values); $x++){
                @$stmtArrayQuery[$x][] = $keys . '="' .$values[$x].'"';
            }
        }
        foreach($stmtArrayQuery as $index => $element){
            $sqlArrayQuery = NULL;
            $sqlQuery = 'INSERT INTO '.$table.' SET ';
            foreach($element as $textNotify){
                $sqlArrayQuery[] = $textNotify;
            }
            $sqlQuery .= implode(", ", $sqlArrayQuery) . $others;
            $result = $this->getReadConnection()->execute($sqlQuery);
            //var_dump($sqlQuery);
        }
        return $result;
    }
    
    /**
     * @param array $arrays
     * @param string $table
     * @param string $others
     * @return array
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Post a mixture of multi-dimensional array and 
     * Associative array values
     */
    public function __postRawSQLTask(array $arrays, $table, 
            $others = '', $action = self::MODEL_INSERT){
        foreach($arrays as $keys => $values){
            if(isset($values) && is_array($values)){
                for($x = 0; $x < count($values); $x++){
                    @$stmtArrayQuery[$x][] = $keys . '="' .$values[$x].'"';
                }
            }
        }
        foreach($stmtArrayQuery as $index => $element){
            $sqlArrayQuery = NULL;
            $sqlQuery = $action.' INTO '.$table.' SET ';
            foreach($element as $textNotify){
                $sqlArrayQuery[] = $textNotify;
            }
            $sqlQuery .= implode(", ", $sqlArrayQuery) . $others;
            $result = $this->getReadConnection()->execute($sqlQuery);
            //var_dump($sqlQuery);
        }
        return $result;
    }
    
    /**
     * @param array $arrays
     * @param string $table
     * @param string $others
     * @return array
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Post a mixture of multi-dimensional array and 
     * Associative array values
     */
    public function __postDuplicateRawSQL(array $arrays, $table){
        foreach($arrays as $keys => $values){
            if(isset($values) && is_array($values)){
                for($x = 0; $x < count($values); $x++){
                    @$stmtArrayQuery[$x][] = $keys . '="' .$values[$x].'"';
                }
            }
        }
        foreach($stmtArrayQuery as $index => $element){
            $sqlArrayQuery = NULL;
            $sqlQuery = 'INSERT INTO '.$table.' SET ';
            foreach($element as $textNotify){
                $_otherInfo[] = $textNotify;
                $sqlArrayQuery[] = $textNotify;
            }
            $others = ' ON DUPLICATE KEY UPDATE '.implode(", ", $_otherInfo);
            $sqlQuery .= implode(", ", $sqlArrayQuery) . $others;
            $result = $this->getReadConnection()->execute($sqlQuery);
            //var_dump($sqlQuery);
        }
        return $result;
    }
    
    /**
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Use Raw Replace Structured Query Language
     * Associative Array replace Post
     * @param array $arrays
     * @param type $table
     * @return type
     */
    public function __replaceRawSQL(array $arrays, $table){
        foreach($arrays as $keys => $values){
            $sqlState = "INSERT INTO $table SET ";
            
            //Raw Replace Structured Query Language
            foreach($values as $index => $element){
                $_otherInfo[] = $index."='".strtolower($element)."'";
                $_stmtArrayQuery[] = $index."='".strtolower($element)."'";
            }
            $sqlState .= implode(", ", $_stmtArrayQuery);
            $others = ' ON DUPLICATE KEY UPDATE '.implode(", ", $_otherInfo);
            $sqlQueryNow = !empty($others) ? $sqlState.$others : $sqlState;
            $result = $this->getReadConnection()->execute($sqlQueryNow);
            $_stmtArrayQuery = array();
        }
        return $result;
    }
    
    /**
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Replace Multi Dimensional Array
     * @param array $arrays
     * @param type $table
     * @return type
     */
    public function __replaceMultiRaw(array $arrays, $table){
        foreach($arrays as $keys => $values){
            for($x = 0; $x < count($values); $x++){
                @$stmtArrayQuery[$x][] = $keys . '="' .$values[$x].'"';
            }
        }
        foreach($stmtArrayQuery as $index => $element){
            $sqlArrayQuery = array();
            $sqlQuery = 'INSERT INTO '.$table.' SET ';
            foreach($element as $key => $textNotify){
                $_otherInfos[$key] = $textNotify;
                $sqlArrayQuery[] = $textNotify;
            }
            $others = ' ON DUPLICATE KEY UPDATE '.implode(", ", $_otherInfos);
            $sqlQuery .= implode(", ", $sqlArrayQuery) . $others;
            $result = $this->getReadConnection()->execute($sqlQuery);
            //var_dump($sqlQuery);
        }
        return $result;
    }
    
    //Excel Purpose for uploading
    public function __excelPostArray(array $arrays, $table, $others=''){
        foreach($arrays[0] as $element){
            $columns[] = $element;
        }
        //Remove the first key
        unset($arrays[0]);
        
        //Iterate throught the adjusted array
        foreach($arrays as $keys => $values){
            $sqlState = "INSERT INTO $table SET ";
            foreach($values as $index => $element){
                $_stmtArrayQuery[] = $columns[$index]."='".$element."'";
                $_otherInfo[] = $columns[$index]."='".strtolower($element)."'";
            }
            $sqlState .= implode(", ", $_stmtArrayQuery);
            $others = ' ON DUPLICATE KEY UPDATE '.implode(", ", $_otherInfo);
            $sqlQueryNow = !empty($others) ? $sqlState.$others : $sqlState;
            $result = $this->getReadConnection()->execute($sqlQueryNow);
            $_stmtArrayQuery = array();
            //var_dump($sqlQueryNow);
        }
        return $result;
    }

    //Mail Templates Available for Other Classes
    public function setHeaderRow() {
        $header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--[if !mso]><!-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!--<![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>SDCH Notification</title>
        <style type="text/css">
        * {
            -webkit-font-smoothing: antialiased;
        }
        body {
            Margin: 0;
            padding: 0;
            min-width: 100%;
            font-family: Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            mso-line-height-rule: exactly;
        }
        table {
            border-spacing: 0;
            color: #333333;
            font-family: Arial, sans-serif;
        }
        img {
            border: 0;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        .webkit {
            max-width: 800px;
        }
        .outer {
            Margin: 0 auto;
            width: 100%;
            max-width: 800px;
        }
        .full-width-image img {
            width: 100%;
            max-width: 800px;
            height: auto;
        }
        .inner {
            padding: 10px;
        }
        p {
            Margin: 0;
            padding-bottom: 10px;
        }
        .h1 {
            font-size: 21px;
            font-weight: bold;
            Margin-top: 15px;
            Margin-bottom: 5px;
            font-family: Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .h2 {
            font-size: 18px;
            font-weight: bold;
            Margin-top: 10px;
            Margin-bottom: 5px;
            font-family: Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .one-column .contents {
            text-align: left;
            font-family: Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .one-column p {
            font-size: 14px;
            Margin-bottom: 10px;
            font-family: Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .two-column {
            text-align: center;
            font-size: 0;
        }
        .two-column .column {
            width: 100%;
            max-width: 300px;
            display: inline-block;
            vertical-align: top;
        }
        .contents {
            width: 100%;
        }
        .two-column .contents {
            font-size: 14px;
            text-align: left;
        }
        .two-column img {
            width: 100%;
            max-width: 280px;
            height: auto;
        }
        .two-column .text {
            padding-top: 10px;
        }
        .three-column {
            text-align: center;
            font-size: 0;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .three-column .column {
            width: 100%;
            max-width: 200px;
            display: inline-block;
            vertical-align: top;
        }
        .three-column .contents {
            font-size: 14px;
            text-align: center;
        }
        .three-column img {
            width: 100%;
            max-width: 180px;
            height: auto;
        }
        .three-column .text {
            padding-top: 10px;
        }
        .img-align-vertical img {
            display: inline-block;
            vertical-align: middle;
        }
        @media only screen and (max-device-width: 480px) {
        table[class=hide], img[class=hide], td[class=hide] {
            display: none !important;
        }
        .contents1 {
            width: 100%;
        }
        .contents1 {
            width: 100%;
        }
        </style>
        <!--[if (gte mso 9)|(IE)]>
            <style type="text/css">
                table {border-collapse: collapse !important;}
            </style>
            <![endif]-->
        </head>
        <body style="Margin:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;min-width:100%;background-color:#f3f2f0;">
<center class="wrapper" style="width:100%;table-layout:fixed;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#f3f2f0;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f2f0;" bgcolor="#f3f2f0;">
    <tr>
      <td width="100%"><div class="webkit" style="max-width:800px;Margin:0 auto;"> 
          
          <!--[if (gte mso 9)|(IE)]>

						<table width="800" align="center" cellpadding="0" cellspacing="0" border="0" style="border-spacing:0" >
							<tr>
								<td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
								<![endif]--> ';
        return $header;
    }

    //Mail Templates Available for Other Classes
    public function setBodyRow(){
        $body   = '
         <!-- ======= start main body ======= -->
          <table class="outer" align="center" cellpadding="0" cellspacing="0" border="0" style="border-spacing:0;Margin:0 auto;width:100%;max-width:800px;">
            <tr>
              <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;"><!-- ======= start header ======= -->
                
                <table border="0" width="100%" cellpadding="0" cellspacing="0"  >
                  <tr>
                    <td><table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
                        <tbody>
                          <tr>
                            <td align="center"><center>
                                <table border="0" align="center" width="100%" cellpadding="0" cellspacing="0" style="Margin: 0 auto;">
                                  <tbody>
                                    <tr>
                                      <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-spacing:0">
                                          <tr>
                                            <td>&nbsp;</td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </center></td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                </table>
                <table border="0" width="100%" cellpadding="0" cellspacing="0"  >
                  <tr>
                    <td><table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
                        <tbody>
                          <tr>
                            <td align="center"><center>
                                <table border="0" align="center" width="100%" cellpadding="0" cellspacing="0" style="Margin: 0 auto;">
                                  <tbody>
                                    <tr>
                                      <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" bgcolor="#FFFFFF"><!-- ======= start header ======= -->
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-left:1px solid #e8e7e5; border-right:1px solid #e8e7e5; border-top:1px solid #e8e7e5">
                                          <tr>
                                            <td>&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td>&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td class="two-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-align:center;font-size:0;" ><!--[if (gte mso 9)|(IE)]>
													<table width="100%" style="border-spacing:0" >
													<tr>
													<td width="20%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:30px;" >
													<![endif]-->
                                              
                                              <div class="column" style="width:100%;max-width:220px;display:inline-block;vertical-align:top;">
                                                <table class="contents" style="border-spacing:0; width:100%"  >
                                                  <tr>
                                                    <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0px;" align="left"><a href="#" target="_blank"><img src="http://sdchospital.com/wp-content/uploads/2018/11/St-Dominic-Hospital-Logo-100x100-e1541068809711.png" alt="" width="60" height="60" style="border-width:0; max-width:60px;height:auto; display:block" align="left"/></a></td>
                                                  </tr>
                                                </table>
                                              </div>
                                              
                                              <!--[if (gte mso 9)|(IE)]>
													</td><td width="80%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
													<![endif]-->
                                              
                                              <div class="column" style="width:100%;max-width:515px;display:inline-block;vertical-align:top;">
                                                <table width="100%" style="border-spacing:0">
                                                  <tr>
                                                    <td class="inner" style="padding-top:0px;padding-bottom:10px; padding-right:10px;padding-left:10px;"><table class="contents" style="border-spacing:0; width:100%" bgcolor="#FFFFFF">
                                                        <tr>
                                                          <td width="57%" align="right" valign="top"><img src="https://gallery.mailchimp.com/fdcaf86ecc5056741eb5cbc18/images/b39b534d-6718-43bb-85fa-1cc212eb91c1.jpg" width="25" height="9" style="border-width:0; max-width:25px;height:auto; display:block; max-height:9px; padding-top:4px; padding-left:10px" alt=""/></td>
                                                          <td width="43%" align="left" valign="top"><font style="font-size:11px; text-decoration:none; color:#474b53; font-family: Verdana, Geneva, sans-serif; text-align:left; line-height:16px; padding-bottom:30px"><a href="#" target="_blank" style="color:#474b53; text-decoration:none">View as a web page</a></font></td>
                                                        </tr>
                                                      </table></td>
                                                  </tr>
                                                </table>
                                              </div>
                                              
                                              <!--[if (gte mso 9)|(IE)]>
													</td>
													</tr>
													</table>
													<![endif]--></td>
                                          </tr>
                                          <tr>
                                            <td height="80">&nbsp;</td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </center></td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                </table>
                
                <!-- ======= end header ======= --> 
                
                <!-- ======= start hero image ======= --><!-- ======= end hero image ======= --> 
        ';
        return $body;
    }

    //Mail Templates Available for Other Classes
    public function mainBodyAction($name, $content){
        $body   = '
        <!-- ======= start hero article ======= -->
                
                <table class="one-column" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-spacing:0; border-left:1px solid #e8e7e5; border-right:1px solid #e8e7e5; border-bottom:1px solid #e8e7e5" bgcolor="#FFFFFF">
                  <tr>
                    <td align="left" style="padding:0px 40px 40px 40px"><p style="color:#262626; font-size:32px; text-align:left; font-family: Verdana, Geneva, sans-serif">Hello! '.$name.'</p>
                      <p style="color:#000000; font-size:16px; text-align:left; font-family: Verdana, Geneva, sans-serif; line-height:22px ">'.$content.'<br />
                        <br />
                        <br />
                        <br />
                        <br />
                        <br />
                        on Best Regards, <br />
                        St. Dominic Catholic Hospital</p></td>
                  </tr>
                </table>
                
                <!-- ======= end hero article ======= --> 
        ';
        return $body;
    }

    //Mail Templates Available for Other Classes
    public function setFooterRow(){
        $footer = '<!-- ======= start footer ======= -->
                    
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td height="30">&nbsp;</td>
                      </tr>
                      <tr>
                        <td class="two-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-align:center;font-size:0;"><!--[if (gte mso 9)|(IE)]>
                                                        <table width="100%" style="border-spacing:0" >
                                                        <tr>
                                                        <td width="50%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
                                                        <![endif]-->
                          
                          <div class="column" style="width:100%;max-width:399px;display:inline-block;vertical-align:top;">
                            <table class="contents" style="border-spacing:0; width:100%">
                              <tr>
                                <td width="39%" align="right" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;"><a href="#" target="_blank"><img src="http://sdchospital.com/wp-content/uploads/2018/11/St-Dominic-Hospital-Logo-100x100-e1541068809711.png" alt="" width="59" height="59" style="border-width:0; max-width:59px;height:auto; display:block; padding-right:20px" /></a></td>
                                <td width="61%" align="left" valign="middle" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;"><p style="color:#787777; font-size:13px; text-align:left; font-family: Verdana, Geneva, sans-serif"> St. Dominic Catholic Hospital &copy; 2019<br />
                                    Creative Mesh Kernel<br />
                                    CMK</p></td>
                              </tr>
                            </table>
                          </div>
                          
                          <!--[if (gte mso 9)|(IE)]>
                                                        </td><td width="50%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
                                                        <![endif]-->
                          
                          <div class="column" style="width:100%;max-width:399px;display:inline-block;vertical-align:top;">
                            <table width="100%" style="border-spacing:0">
                              <tr>
                                <td class="inner" style="padding-top:0px;padding-bottom:10px; padding-right:10px;padding-left:10px;"><table class="contents" style="border-spacing:0; width:100%">
                                    <tr>
                                      <td width="32%" align="center" valign="top" style="padding-top:10px"><table width="150" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td width="33" align="center"><a href="#" target="_blank"><img src="https://gallery.mailchimp.com/fdcaf86ecc5056741eb5cbc18/images/630dff47-d818-4954-b59c-a460ba542fa6.jpg" alt="facebook" width="36" height="36" border="0" style="border-width:0; max-width:36px;height:auto; display:block; max-height:36px"/></a></td>
                                            <td width="34" align="center"><a href="#" target="_blank"><img src="https://gallery.mailchimp.com/fdcaf86ecc5056741eb5cbc18/images/85624967-ff81-441d-9324-8e40068af5a1.jpg" alt="twitter" width="36" height="36" border="0" style="border-width:0; max-width:36px;height:auto; display:block; max-height:36px"/></a></td>
                                            <td width="33" align="center"><a href="#" target="_blank"><img src="https://gallery.mailchimp.com/fdcaf86ecc5056741eb5cbc18/images/3595d450-9ad9-4c65-b60e-922f77287d76.jpg" alt="linkedin" width="36" height="36" border="0" style="border-width:0; max-width:36px;height:auto; display:block; max-height:36px"/></a></td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                  </table></td>
                              </tr>
                            </table>
                          </div>
                          
                          <!--[if (gte mso 9)|(IE)]>
                                                        </td>
                                                        </tr>
                                                        </table>
                                                        <![endif]--></td>
                      </tr>
                      <tr>
                        <td height="30">&nbsp;</td>
                      </tr>
                    </table>
                    
                    <!-- ======= end footer ======= --></td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]>
                        </td>
                    </tr>
                </table>
                <![endif]--> 
            </div></td>
        </tr>
      </table>
    </center>
    </body>
    </html>';
        return $footer;
    }
}
