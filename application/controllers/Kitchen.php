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
			'shop_id' => $this->input->post('name'),
			'key' => md5($this->input->post('password'))
		);
	}
	public function logHistory(){
        // Key check here.
        $data = array(
			'shop_id'   => $this->input->post('shop_id'),
			'type'      => $this->input->post('type'),
            'item_id'   => $this->input->post('item_id'),
            'item_name' => $this->input->post('item_name'),
            'qty'       => $this->input->post('qty'),
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
			'shop_id'   => $this->input->post('shop_id'),
			'from'      => $this->input->post('start'),
            'to'   => $this->input->post('end')
		);
		$res = $this->kitchen_model->get_history($data);
		echo json_encode(array(
			'res' => $res
		));
	}
}
