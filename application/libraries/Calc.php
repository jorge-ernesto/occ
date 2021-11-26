<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/PHPExcel/Classes/PHPExcel.php";
//sftp://emily.local/usr/local/apache/htdocs/ocsmanager/application/third_party/PHPExcel/Classes

class Calc extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }
}