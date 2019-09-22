<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->library('session');
    }

    protected function dbconnect(){

		$serverName = "47.88.53.35";

		$connectionInfo = array( "Database"=>"CHA2O", "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);

		if( $conn ) {
		     return $conn;
		}else{
		     return -1;
		}
	}
}
