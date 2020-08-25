<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
    }

    protected function dbconnect(){
		$serverName = "198.11.172.117";
        $db = $this->user_model->get_DB($this->session->userdata('user_name'));
		$connectionInfo = array( "Database"=>$db, "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( $conn ) {
		     return $conn;
		}else{
		     return -1;
		}
	}

    protected function custom_dbconnect($db){
        $serverName = "198.11.172.117";
		$connectionInfo = array( "Database"=>$db, "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( $conn ) {
		     return $conn;
		}else{
		     return -1;
		}
    }

    protected function __dbconnect(){
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

    protected function __custom_dbconnect($db){
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
