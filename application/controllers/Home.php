<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->model('dashboard_model');
    }

	public function index(){
		if($this->session->has_userdata('user_name')){
            if($this->session->userdata('user_name') == 'admin'){
				$this->load->view('admin');
			}else{
				$this->load->view('home');
			}
        }else{
            $this->load->view('login');
        }
	}
	public function dbtest(){
		$serverName = "198.11.172.117";

		$connectionInfo = array( "Database"=>"meetfresh", "UID"=>"laguna", "PWD"=>"goqkdtks.1234");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);

		if( $conn ) {
	     	echo "Connection established.<br />";
		}else{
		     echo "Connection could not be established.<br />";
	     	die( print_r( sqlsrv_errors(), true));
		}
	}
	public function dbtest2(){
		$servername = "107.180.14.68:3306";
		$username = "admin-present";
		$password = "Tcpos2018";

		// Create connection
		$conn = new mysqli($servername, $username, $password);

		// Check connection
		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		}
		echo "Connected successfully";
	}
	public function dashboard(){

		$conn = parent::dbconnect();

		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);
		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"shops" 		=> $this->dashboard_model->get_shop_list($conn, $shop_name),
			"sale" 			=> $this->dashboard_model->get_sale($conn, $date, $shop_name),
			"discount"		=> $this->dashboard_model->get_discount($conn, $date, $shop_name),
			"transaction" 	=> $this->dashboard_model->get_transaction($conn, $date, $shop_name),
			"promotion"		=> $this->dashboard_model->get_promotion($conn, $date, $shop_name),
			"tip"			=> $this->dashboard_model->get_tip($conn, $date, $shop_name),
			"tax"			=> $this->dashboard_model->get_tax($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function daily(){
		$conn = parent::dbconnect();

		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);

		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));

		$ret = array(
			"daily_sale" 		=> $this->dashboard_model->get_daily_sale($conn, $date, $shop_name),
			"daily_transaction" => $this->dashboard_model->get_daily_transaction($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function sale_detail(){
		$conn = parent::dbconnect();

		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);

		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"sale_detail" => $this->dashboard_model->get_sale_detail($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function transaction_detail(){
		$conn = parent::dbconnect();

		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);

		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));

		$ret = array(
			"transaction_detail" => $this->dashboard_model->get_transaction_detail($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		 sqlsrv_close($conn);
	}
	public function payment(){
		$conn = parent::dbconnect();

		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);

		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"payment_detail" => $this->dashboard_model->get_payment_detail($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}

	public function daily_turnover(){
		$conn = parent::dbconnect();

		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end'),
			"length"=> $this->input->post('length')
		);
		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"daily_turnover" => $this->dashboard_model->get_daily_turnover($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function weekly_turnover(){
		$conn = parent::dbconnect();
		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end'),
			"length"=> $this->input->post('length')
		);
		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));

		$ret = array(
			"weekly_turnover" => $this->dashboard_model->get_weekly_turnover($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function monthly_turnover(){
		$conn = parent::dbconnect();
		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end'),
			"length"=> $this->input->post('length')
		);
		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"monthly_turnover" => $this->dashboard_model->get_monthly_turnover($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function yearly_turnover(){
		$conn = parent::dbconnect();

		$d_date = array(
			"start" => $this->input->post('d_start'),
			"end"	=> $this->input->post('end'),
			"length"=> $this->input->post('length')
		);
		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"yearly_turnover" => $this->dashboard_model->get_yearly_turnover($conn, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}

	public function get_comparison_detailed_data(){
		$conn = parent::dbconnect();
		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end'),
			"last_week_start" => $this->input->post('last_start'),
			"last_week_end"	=> $this->input->post('last_end'),
		);
		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"article_detail" 	=> $this->dashboard_model->detail_comparison_article($conn, $date, $shop_name),
			"discount_detail" 	=> $this->dashboard_model->detail_comparison_discount($conn, $date, $shop_name),
			"payment_detail" 	=> $this->dashboard_model->detail_comparison_payment($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}

	public function article_detail(){
		$conn = parent::dbconnect();
		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);
		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"article_detail" 	=> $this->dashboard_model->shop_article_details($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}

	public function operator_info(){
		$conn = parent::dbconnect();
		$shop_name = str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shop_name')));
		$ret = array(
			"shops" 	=> $this->dashboard_model->get_shops($conn, $shop_name),
			"tills"		=> $this->dashboard_model->get_tills($conn, $shop_name),
			"operators" => $this->dashboard_model->get_operators($conn, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}

	public function operator_presence(){
		$conn = parent::dbconnect();
		$data = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end'),
			"shop_id" => str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('shops'))),
			"till_id" => str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('tills'))),
			"operator_id" => str_replace(array('"'), '\'', str_replace(array('[',']'), '', $this->input->post('operators')))
		);
		$ret = array(
			"presence" 	=> $this->dashboard_model->get_presence($conn, $data)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}

	public function save_present_data(){
		$data = array(
			"operators" => $this->input->post('operators'),
			"manager"	=> $this->input->post('manager'),
			"date"		=> $this->input->post('date')
		);
		$ret = $this->user_model->save_presence($data);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
	}

	public function load_present_data(){
		$ret = $this->user_model->get_presence();
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
	}
	public function delete_present_data(){
		$ret = $this->user_model->delete_presence($this->input->post('id'));
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
	}

	public function get_payment_view(){
		$conn = parent::dbconnect();
		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);
		$shop_name = $this->input->post('shop_name');
		$ret = array(
			"p_netsale" 	=> $this->dashboard_model->get_p_netsale($conn, $date, $shop_name),
			"p_tax" 		=> $this->dashboard_model->get_p_tax($conn, $date, $shop_name),
			"p_detail" 		=> $this->dashboard_model->get_p_detail($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function get_monthly_view(){
		$conn = parent::dbconnect();
		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);
		$shop_name = $this->input->post('shop_name');
		$ret = array(
			"m_sale" 		=> $this->dashboard_model->get_m_sale($conn, $date, $shop_name),
			"m_count" 		=> $this->dashboard_model->get_m_count($conn, $date, $shop_name),
			"m_cup" 		=> $this->dashboard_model->get_m_cups($conn, $date, $shop_name),
			"m_ac" 			=> $this->dashboard_model->get_m_ac($conn, $date, $shop_name),
			"m_drink" 		=> $this->dashboard_model->get_m_drinks($conn, $date, $shop_name)
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
	public function get_yearly_view(){
		$conn = parent::dbconnect();
		$date = array(
			"start" => $this->input->post('start'),
			"end"	=> $this->input->post('end')
		);
		$shop_name = $this->input->post('shop_name');
		$ret = array(
			"y_sale"				=> $this->dashboard_model->get_y_sale($conn, $date, $shop_name),
			"y_dinein_count"		=> $this->dashboard_model->get_y_dinein_count($conn, $date, $shop_name),
			"y_dinein_amount"		=> $this->dashboard_model->get_y_dinein_amount($conn, $date, $shop_name),
			"y_togo_count"			=> $this->dashboard_model->get_y_togo_count($conn, $date, $shop_name),
			"y_togo_amount"			=> $this->dashboard_model->get_y_togo_amount($conn, $date, $shop_name),
			"y_transaction_count"	=> $this->dashboard_model->get_y_transaction_count($conn, $date, $shop_name),
			"y_article_count"		=> $this->dashboard_model->get_y_article_count($conn, $date, $shop_name),
		);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
		sqlsrv_close($conn);
	}
}
