<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->model('dashboard_model');
    }

	public function index(){
		if($this->session->has_userdata('user_name')){

            $this->load->view('home');
        }else{
            $this->load->view('login');
        }
	}
	public function dbtest(){
		$serverName = "47.88.53.35";

		$connectionInfo = array( "Database"=>"meetfresh", "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);

		if( $conn ) {
	     	echo "Connection established.<br />";
		}else{
		     echo "Connection could not be established.<br />";
	     	die( print_r( sqlsrv_errors(), true));
		}
	}
	public function dashboard(){

		$conn = parent::dbconnect();

		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);
		$ret = array(
			"shops" 		=> $this->dashboard_model->get_shop_list($conn),
			"sale" 			=> $this->dashboard_model->get_sale($conn, $date),
			"transaction" 	=> $this->dashboard_model->get_transaction($conn, $date),
			"promotion"		=> $this->dashboard_model->get_promotion($conn, $date),
			"tip"			=> $this->dashboard_model->get_tip($conn, $date)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function daily(){
		$conn = parent::dbconnect();

		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);
		$ret = array(
			"daily_sale" 		=> $this->dashboard_model->get_daily_sale($conn, $date),
			"daily_transaction" => $this->dashboard_model->get_daily_transaction($conn, $date)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}

}
