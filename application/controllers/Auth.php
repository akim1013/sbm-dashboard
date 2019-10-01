<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('user_model');
    }

	public function index(){
		$this->load->view('login');
	}
	public function signup(){
		$this->load->view('register');
	}
	public function login(){
		$data = array(
			'name' => $this->input->post('name'),
			'password' => md5($this->input->post('password'))
		);
		$res = $this->user_model->login($data);
		if($res == 1){
			$this->session->set_userdata('user_name', $data['name']);
            if($data['name'] == 'admin'){
				echo json_encode(array(
	                'status' => 'success',
	                'msg'    => 'Admin logged in successfully'
	            ));
			}else{
				echo json_encode(array(
	                'status' => 'success',
	                'msg'    => 'User logged in successfully'
	            ));
			}
        }else{
            echo json_encode(array(
                'status' => 'failed',
                'msg'    => 'User name or Password incorrect'
            ));
        }
	}
    public function logout(){
        $this->session->sess_destroy();
        echo 1;
	}
    public function register(){
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

	public function users(){
		$users = $this->user_model->users();
		if($users){
			$res = array();
			foreach ($users->result() as $row){
				array_push($res, $row);
			}
			echo json_encode(array(
				'status' => 'success',
				'data' => $res,
				'msg' => 'Read user data successfully'
			));
		}else{
			echo json_encode(array(
				'status' => 'failed',
				'msg' => 'Read user data failed'
			));
		}
	}
}
