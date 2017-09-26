<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require('tcpdf/tcpdf.php');

class Mytcpdf extends TCPDF  {
    function __construct()
    {
        parent::__construct();
        $CI =& get_instance();
    }


}
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/22/17
 * Time: 2:31 PM
 */

?>