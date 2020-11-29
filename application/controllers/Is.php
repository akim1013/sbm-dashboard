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
	public function remove_is_item(){
		$id = $this->input->post('id');
		$res = $this->is_model->remove_is_item($id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }

	public function get_is_counts(){
		$branch_id = $this->input->post('branch_id');
    $res = $this->is_model->get_counts($branch_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function add_is_count(){
		$counter_id = $this->input->post('counter_id');
		$period = $this->input->post('period');
		$branch_id = $this->input->post('branch_id');
    $res = $this->is_model->add_count(array(
			'counter_id' => $counter_id,
			'branch_id' => $branch_id,
			'period' => $period
		));
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function draft_is_count(){
		$branch_id = $this->input->post('branch_id');
    $res = $this->is_model->draft_count($branch_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function remove_is_count(){
		$id = $this->input->post('is_count_id');
    $res = $this->is_model->remove_count($id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function get_c_item(){
		$branch_id = $this->input->post('branch_id');
		$res = $this->is_model->get_c_item($branch_id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function get_draft_items(){
		$draft_id = $this->input->post('draft_id');
		$res = $this->is_model->get_draft_items($draft_id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function add_count_detail(){
		$data = array(
			'is_count_id' => $this->input->post('is_count_id'),
			'is_item_id' => $this->input->post('is_item_id'),
			'qty_primary' => $this->input->post('qty_primary'),
			'qty_secondary' => $this->input->post('qty_secondary')
		);
		$res = $this->is_model->add_count_detail($data);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function complete_count(){
		$draft_id = $this->input->post('draft_id');
		$res = $this->is_model->complete_count($draft_id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function can_start_count(){
		$counter_id = $this->input->post('counter_id');
		$res = $this->is_model->can_start_count($counter_id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function is_draft(){
		$id = $this->input->post('id');
		$res = $this->is_model->is_draft($id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function remove_draft_detail_item(){
		$id = $this->input->post('id');
		$res = $this->is_model->remove_draft_detail_item($id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
}
