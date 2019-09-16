<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('user_model');
    }

	public function index(){
		$this->load->view('login');
	}

	public function auth(){
		$data = array(
			'name' => $this->input->post('name'),
			'password' => md5($this->input->post('password'))
		);
		$res = $this->user_model->login($data);
		if($res == 1){
			$this->session->set_userdata('user_name', $data['name']);
            echo json_encode(array(
                'status' => 'success',
                'msg'    => 'User logged in successfully'
            ));
        }else{
            echo json_encode(array(
                'status' => 'failed',
                'msg'    => 'User name or Password incorrect'
            ));
        }
	}
}
