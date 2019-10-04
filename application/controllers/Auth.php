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
			$this->session->set_userdata('shop_name', $data['shop_name']);
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
			'database'			=> $this->input->post('database'),
			'shop_name'			=> $this->input->post('shop')
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
				if($row->name != 'admin'){
					array_push($res, $row);
				}
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
	public function delete(){
		$res = $this->user_model->delete($this->input->post('id'));
		echo $res;
	}
	public function db(){
		$serverName = "47.88.53.35";
		$connectionInfo = array( "Database"=>"master", "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( $conn ) {
			$sql = "SELECT name FROM master.sys.databases";
			$query = sqlsrv_query( $conn, $sql );
	        $ret = array();
	        while( $row = sqlsrv_fetch_array( $query, SQLSRV_FETCH_ASSOC) ) {
				  array_push($ret, $row);
			}
			echo json_encode(array(
			   "status" => 'success',
			   "data" => $ret
			));
		}else{
		     echo json_encode(array(
				"status" => 'failed',
				"msg" => "DB connection error"
			 ));
		}
	}
	public function shop(){
		$db = $this->input->post('db');
		$serverName = "47.88.53.35";
		$connectionInfo = array( "Database"=>$db, "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( $conn ) {
			$sql = "SELECT id, description FROM shops";
			$query = sqlsrv_query( $conn, $sql );
			if(!$query){
				echo json_encode(array(
				   "status" => 'failed',
				   "msg" => "No shop detected!"
				));
			}else{
				$ret = array();
		        while( $row = sqlsrv_fetch_array( $query, SQLSRV_FETCH_ASSOC) ) {
					  array_push($ret, $row);
				}
				echo json_encode(array(
				   "status" => 'success',
				   "data" => $ret
				));
			}
		}else{
		     echo json_encode(array(
				"status" => 'failed',
				"msg" => "DB connection error"
			 ));
		}
	}
}
