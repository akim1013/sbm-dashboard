<?php
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Is extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('is_model');
    $this->load->model('ps_model');
		$this->load->model('user_model');
  }
	public function get_ps_item(){
		$company = $this->input->post('company');
    $res = $this->ps_model->get_item($company);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function get_is_item(){
		$branch_id = $this->input->post('branch_id');
    $res = $this->is_model->get_item($branch_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function add_is_item(){
		$data = array(
			'branch_id' => $this->input->post('branch_id'),
			'inventory_id' => $this->input->post('inventory_id'),
			'safety_qty' => $this->input->post('safety_qty'),
			'primary_unit' => $this->input->post('primary_unit'),
			'secondary_unit' => $this->input->post('secondary_unit'),
			'sp_qty' => $this->input->post('sp_qty')
		);
		$res = $this->is_model->add_is_item($data);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
}
