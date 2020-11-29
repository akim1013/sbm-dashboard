<?php

header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
defined('BASEPATH') OR exit('No direct script access allowed');

class Crm extends MY_Controller {
  public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('crm_model');
  }

  public function get_customer_info(){
    $conn = parent::custom_dbconnect('meetfresh');
    $limit = $this->input->post('limit');
    $offset = $this->input->post('offset');
    $ret = array(
			"customer_info" => $this->crm_model->get_customer_info($conn, $limit, $offset)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
  }
}
