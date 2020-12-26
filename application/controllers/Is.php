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
		$this->load->model('purchasing_model');
		$this->load->model('user_model');
  }
	public function get_ps_item(){
		$company = $this->input->post('company');
    $res = $this->purchasing_model->get_item($company);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function get_is_item(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
    $res = $this->is_model->get_item($company, $shop);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function add_is_item(){
		$data = array(
			'company' => $this->input->post('company'),
			'shop' => $this->input->post('shop'),
			'branch_id' => $this->input->post('branch_id'),
			'purchasing_item_id' => $this->input->post('purchasing_item_id'),
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
		$timestamp = $this->input->post('timestamp');
    $res = $this->is_model->add_count(array(
			'counter_id' => $counter_id,
			'branch_id' => $branch_id,
			'period' => $period,
			'timestamp' => $timestamp
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
    $res1 = $this->is_model->remove_count($id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res1
		));
  }
	public function get_c_item(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$res = $this->is_model->get_c_item($company, $shop);
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
			'qty_secondary' => $this->input->post('qty_secondary'),
			'value' => $this->input->post('value')
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
	public function order_status_update(){
		$id = $this->input->post('id');
		$order_status = $this->input->post('order_status');
		$res = $this->is_model->order_status_update($id, $order_status);
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
	public function send_data_to_dashboard(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$branch_id = $this->input->post('branch_id');
		$counter_id = $this->input->post('counter_id');
		$is_count_id = $this->input->post('is_count_id');
		$timestamp = $this->input->post('timestamp');
		$items = json_decode($this->input->post('items'));
		$res = $this->is_model->send_data_to_dashboard($company, $shop, $branch_id, $counter_id, $is_count_id, $timestamp, $items);

		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function get_inventory_history(){
		$company = $this->input->post('company');
		$branch_id = $this->input->post('branch_id');
		$res = $this->is_model->get_inventory_history($company, $branch_id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}

	public function get_inventory_stock(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$branch_id = $this->input->post('branch_id');
		$res = $this->is_model->get_inventory_stock($company, $shop, $branch_id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function get_item_history_data(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$branch_id = $this->input->post('branch_id');
		$purchasing_item_id = $this->input->post('purchasing_item_id');
		$res = $this->is_model->get_item_history_data($company, $shop, $branch_id, $purchasing_item_id);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
}
