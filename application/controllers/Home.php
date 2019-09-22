<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	public function __construct(){
		parent::__construct();
    }

	public function index(){
		if($this->session->has_userdata('user_name')){

            $this->load->view('home');
        }else{
            $this->load->view('login');
        }
	}

	public function dashboard(){

		$conn = parent::dbconnect();

		$start = $this->input->post('start');
		$end = $this->input->post('end');

		// Get shop lists
		$sql_shop_list = "
			SELECT id, description
			FROM shops
		";
		$query_shop_list = sqlsrv_query( $conn, $sql_shop_list );
		$ret_shop_list = array();
		while( $row = sqlsrv_fetch_array( $query_shop_list, SQLSRV_FETCH_ASSOC) ) {
			  array_push($ret_shop_list, $row);
		}
		$ret = array(
			"shops" => $ret_shop_list
		);

		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
	}


}
