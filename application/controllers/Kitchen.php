<?php
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

defined('BASEPATH') OR exit('No direct script access allowed');

class Kitchen extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('kitchen_model');
    }
	public function login(){
		$data = array(
			'name' => $this->input->post('name'),
			'email' => $this->input->post('key')
		);
		$res = $this->kitchen_model->kt_login($data);
		echo json_encode(array(
			'res' => $res
		));
	}
	public function logHistory(){
        // Key check here.
    $data = array(
			'shop_id'   => $this->input->post('shop_id'),
			'type'      => $this->input->post('type'),
			'item_id'   => $this->input->post('item_id'),
			'item_code' => $this->input->post('item_code'),
			'item_name' => $this->input->post('item_name'),
			'amount'    => $this->input->post('amount'),
			'reason'    => $this->input->post('reason'),
			'timestamp' => $this->input->post('timestamp')
		);
		$res = $this->kitchen_model->log_history($data);
		echo json_encode(array(
			'res' => $res
		));
	}

	public function getHistory(){
        // Key check here.
    $data = array(
			'shop_id'   => $this->input->post('kitchen'),
			'from'      => $this->input->post('from'),
      'to'   			=> $this->input->post('to'),
			'd'					=> $this->input->post('d')
		);
		$amount = $this->kitchen_model->get_amount_history($data);
		$item = $this->kitchen_model->get_item_history($data);
		$history = $this->kitchen_model->get_history($data);
		echo json_encode(array(
			'amount_history' => $amount,
			'item_history' => $item,
			'history' => $history
		));
	}

	public function getKitchens(){
		$data = $this->input->post('company');
		$res = $this->kitchen_model->get_kitchens($data);
		echo json_encode(array(
			'res' => $res
		));
	}
}
