<?php
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

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
			'password' => $this->input->post('password')
		);
		$res = $this->user_model->login($data);
		if($res != '0'){
			$this->session->set_userdata('user_name', $data['name']);
			if($this->input->post('language') == 'en'){
				$this->session->set_userdata('site_lang', 'english');
			}else{
				$this->session->set_userdata('site_lang', 'chinese');
			}
            if($data['name'] == 'admin'){
				echo json_encode(array(
	                'status' => 'success',
	                'msg'    => lang('admin_login_success'),
					'res' => $res
	            ));
			}else{
				echo json_encode(array(
	                'status' => 'success',
	                'msg'    => lang('user_login_success'),
					'res'=> $res,
					'user_name'=> $this->input->post('name')
	            ));
			}
        }else{
            echo json_encode(array(
                'status' => 'failed',
                'msg'    => lang('login_error'),
				'res'	=> $res
            ));
        }
	}
	public function ps_login(){
		// Purchasing system login
		$data = array(
			'name' => $this->input->post('name'),
			'password' => $this->input->post('password'),
			'access' => 'purchasing_system'
		);
		$res = $this->user_model->ps_login($data);
		if($res != '0'){
			echo json_encode(array(
				'status' => 'success',
				'msg'    => lang('user_login_success'),
				'res' => $res
			));
    }else{
			echo json_encode(array(
				'status' => 'failed',
				'msg'    => lang('login_error'),
				'res'	=> $res
			));
		}
	}
	public function is_login(){
		// Inventory system login
		$data = array(
			'name' => $this->input->post('name'),
			'password' => $this->input->post('password'),
			'access' => 'purchasing_system'
		);
		$res = $this->user_model->is_login($data);
		if($res != '0'){
			echo json_encode(array(
				'status' => 'success',
				'msg'    => lang('user_login_success'),
				'res' => $res
			));
    }else{
			echo json_encode(array(
				'status' => 'failed',
				'msg'    => lang('login_error'),
				'res'	=> $res
			));
		}
	}
	public function logHistory(){
    $data = array(
			'user_id' => $this->input->post('user_id'),
			'event_description' => $this->input->post('event_description'),
			'event_detail' => $this->input->post('event_detail')
		);
		$res = $this->user_model->logHistory($data);
		echo json_encode(array(
			'res' => $res
		));
	}
	public function getHistory(){
        $data = array(
			'user' => $this->input->post('user_name'),
			'start' => $this->input->post('from'),
			"end"	=> $this->input->post('to')
		);
		$res = array(
			'events' => $this->user_model->getEvents($data),
			'logs' => $this->user_model->getLogs($data),
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
  public function logout(){
    $this->session->unset_userdata('user_name');
    echo 1;
	}
  public function register(){
      $data = array(
          'name'            => $this->input->post('name'),
          'email'           => $this->input->post('email'),
          'password'        => $this->input->post('password'),
					'database'				=> $this->input->post('database'),
					'shop_name'				=> $this->input->post('shop'),
					'access'					=> $this->input->post('access'),
					'role'						=> $this->input->post('role'),
					'company'					=> $this->input->post('company'),
					'branch_id'				=> $this->input->post('branch_id'),
					'member_since'		=> date("Y-m-d")
      );

      $res = $this->user_model->create($data);

      if($res == 1){
          echo json_encode(array(
              'status' => 'success',
              'msg'    => lang('user_created_success')
          ));
      }else if($res == 0){
          echo json_encode(array(
              'status' => 'failed',
              'msg'    => lang('user_name_exist')
          ));
      }else{
          echo json_encode(array(
              'status' => 'failed',
              'msg'    => lang('user_name_exist')
          ));
      }
  }

	public function update(){
		$data = array(
			'id'				=> $this->input->post('id'),
      'name'      => $this->input->post('name'),
			'email'     => $this->input->post('email'),
			'password'	=> $this->input->post('password'),
			'shop_name'	=> $this->input->post('shop'),
			'role'			=> $this->input->post('role'),
			'database' 	=> $this->input->post('database'),
			'access'		=> $this->input->post('access'),
			'company'		=> $this->input->post('company'),
			'branch_id'	=> $this->input->post('branch_id')
    );

    $res = $this->user_model->update($data);
    if($res == 1){
        echo json_encode(array(
            'status' => 'success',
            'msg'    => 'User updated'
        ));
    }else{
        echo json_encode(array(
            'status' => 'failed',
            'msg'    => 'Unknown error'
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
				'msg' => lang('user_read_success')
			));
		}else{
			echo json_encode(array(
				'status' => 'failed',
				'msg' => lang('user_read_failed')
			));
		}
	}

	public function get_all_users(){
		$auth = $this->input->post('role');
		if($auth == 'super_admin'){
			$users = $this->user_model->get_all_users();
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
					'msg' => lang('user_read_success')
				));
			}else{
				echo json_encode(array(
					'status' => 'failed',
					'msg' => lang('user_read_failed')
				));
			}
		}else{
			echo json_encode(array(
				'status' => 'failed',
				'msg' => 'Unauthorized',
				'auth' => $auth
			));
		}
	}

	public function delete(){
		$res = $this->user_model->delete($this->input->post('id'));
		echo $res;
	}
	public function db(){
		// $serverName = "84.242.182.150,9433";
		// $connectionInfo = array( "Database"=>"master", "UID"=>"sa", "PWD"=>"Tcpos2020*!", "CharacterSet" => "UTF-8");
		$serverName = "198.11.172.117";
		$connectionInfo = array( "Database"=>'master', "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
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
				"msg" => lang('db_error')
			 ));
		}
	}
	public function db_secondary(){
		$serverName = "84.242.182.150,9433";
		$connectionInfo = array( "Database"=>"master", "UID"=>"sa", "PWD"=>"Tcpos2020*!", "CharacterSet" => "UTF-8");
		// $serverName = "198.11.172.117";
		// $connectionInfo = array( "Database"=>'master', "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
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
				"msg" => lang('db_error')
			 ));
		}
	}
	public function shop(){
		$db = $this->input->post('db');
		// $serverName = "84.242.182.150,9433";
		// $connectionInfo = array( "Database"=>$db, "UID"=>"sa", "PWD"=>"Tcpos2020*!", "CharacterSet" => "UTF-8");
		$serverName = "198.11.172.117";
		$connectionInfo = array( "Database"=>$db, "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);

		if( $conn ) {
			$sql = "SELECT id, description FROM shops";
			$query = sqlsrv_query( $conn, $sql );
			if(!$query){
				echo json_encode(array(
				   "status" => 'failed',
				   "msg" => lang('not_found_shop')
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
				"msg" => lang('db_error')
			 ));
		}
	}
	public function shop_secondary(){
		$db = $this->input->post('db');
		$serverName = "84.242.182.150,9433";
		$connectionInfo = array( "Database"=>$db, "UID"=>"sa", "PWD"=>"Tcpos2020*!", "CharacterSet" => "UTF-8");
		// $serverName = "198.11.172.117";
		// $connectionInfo = array( "Database"=>$db, "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);

		if( $conn ) {
			$sql = "SELECT id, description FROM shops";
			$query = sqlsrv_query( $conn, $sql );
			if(!$query){
				echo json_encode(array(
				   "status" => 'failed',
				   "msg" => lang('not_found_shop')
				));
			}else{
				$ret = array();
		        while( $row = sqlsrv_fetch_array( $query, SQLSRV_FETCH_ASSOC) ) {
					  array_push($ret, $row);
				}
				echo json_encode(array(
				   "status" => 'success',
				   "data" => $ret,
				   "test" => bin2hex($ret[3]['description'])
				));
			}
		}else{
		     echo json_encode(array(
				"status" => 'failed',
				"msg" => lang('db_error')
			 ));
		}
	}
	public function kt_register(){
        $data = array(
            'username'              => $this->input->post('username'),
            'usercode'             => $this->input->post('usercode'),
            'db'          => $this->input->post('db'),
			'shop'			=> $this->input->post('shop'),
			'ktkey'			=> $this->input->post('ktkey'),
			'created_at'	=> date("Y-m-d H:i:s")
        );

        $res = $this->user_model->kt_create($data);

        if($res == 1){
            echo json_encode(array(
                'status' => 'success',
                'msg'    => lang('user_created_success')
            ));
        }else if($res == 0){
            echo json_encode(array(
                'status' => 'failed',
                'msg'    => lang('user_code_exist')
            ));
        }else{
            echo json_encode(array(
                'status' => 'failed',
                'msg'    => lang('user_code_exist')
            ));
        }
    }
	public function kt_update(){
        $data = array(
            'id'              => $this->input->post('id'),
			'username'              => $this->input->post('username'),
            'usercode'             => $this->input->post('usercode'),
            'db'          => $this->input->post('db'),
			'shop'			=> $this->input->post('shop'),
			'ktkey'			=> $this->input->post('ktkey')
        );

        $res = $this->user_model->kt_update($data);

        if($res == 1){
            echo json_encode(array(
                'status' => 'success',
                'msg'    => 'User updated'
            ));
        }else{
            echo json_encode(array(
                'status' => 'failed',
                'msg'    => 'Unknown error'
            ));
        }
    }
	public function kt_users(){
		$users = $this->user_model->kt_users();
		if($users){
			$res = array();
			foreach ($users->result() as $row){
				array_push($res, $row);
			}
			echo json_encode(array(
				'status' => 'success',
				'data' => $res,
				'msg' => lang('user_read_success')
			));
		}else{
			echo json_encode(array(
				'status' => 'failed',
				'msg' => lang('user_read_failed')
			));
		}
	}
	public function kt_delete(){
		$res = $this->user_model->kt_delete($this->input->post('id'));
		echo $res;
	}
}
