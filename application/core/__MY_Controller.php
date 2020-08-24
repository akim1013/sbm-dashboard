<?php
defined('BASEPATH') OR exit('No direct script access allowed');
mb_internal_encoding("UTF-8");
class MY_Controller extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
    }

    protected function dbconnect(){
		$serverName = "84.242.182.150,9433";
        $db = $this->user_model->get_DB($this->session->userdata('user_name'));
		$connectionInfo = array( "Database"=>$db, "UID"=>"sa", "PWD"=>"Tcpos2020*!", "CharacterSet" => "UTF-8");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( $conn ) {
		     return $conn;
		}else{
		     return -1;
		}
	}

    protected function custom_dbconnect($db){
        $serverName = "84.242.182.150,9433";
		$connectionInfo = array( "Database"=>$db, "UID"=>"sa", "PWD"=>"Tcpos2020*!", "CharacterSet" => "UTF-8");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( $conn ) {
		     return $conn;
		}else{
		     return -1;
		}
    }
}
