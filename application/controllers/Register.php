<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
    }

	public function index(){
		$this->load->view('register');
	}

    public function add(){
        $data = array(
            'name'              => $this->input->post('name'),
            'email'             => $this->input->post('email'),
            'verification_key'  => md5(rand()),
            'password'          => md5($this->input->post('password')),
        );
        $res = $this->user_model->create($data);
        if($res == 1){
            echo json_encode(array(
                'status' => 'success',
                'msg'    => 'User created successfully'
            ));
        }else if($res == 0){
            echo json_encode(array(
                'status' => 'failed',
                'msg'    => 'User name exists. Please try with different name'
            ));
        }else{
            echo json_encode(array(
                'status' => 'failed',
                'msg'    => 'User created successfully'
            ));
        }
    }
}
