<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require('fpdf/fpdf.php');

class Myfpdf extends FPDF  {
    public function __construct()
    {
        parent::__construct();
        $CI = &get_instance();
    }


}


?>