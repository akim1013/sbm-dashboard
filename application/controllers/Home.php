<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	public function __construct(){
		parent::__construct();
    }

	public function index(){
		if($this->session->has_userdata('user_name')){

            $this->load->view('home');
        }else{
            $this->load->view('login');
        }
	}

	public function dashboard(){

		$conn = parent::dbconnect();

		$start = $this->input->post('start');
		$end = $this->input->post('end');

		// Get shop lists
		$sql_shop_list = "
			SELECT id, description
			FROM shops
		";
		$query_shop_list = sqlsrv_query( $conn, $sql_shop_list );
		$ret_shop_list = array();
		while( $row = sqlsrv_fetch_array( $query_shop_list, SQLSRV_FETCH_ASSOC) ) {
			  array_push($ret_shop_list, $row);
		}

		// Get total turnover
		$sql_turn_over = "
			SELECT
				t.shop_id as ShopId,
				SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as TurnOver
			FROM shops s
			LEFT JOIN transactions t on t.shop_id = s.id
			LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
			LEFT JOIN trans_articles ta ON (ta.transaction_id = t.id)
			LEFT JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
			LEFT JOIN measure_units mu ON (mu.id = a.measure_unit_id)

			WHERE t.delete_operator_id IS NULL
					AND t.bookkeeping_date BETWEEN '" . $start . "' AND '" . $end . "'
			GROUP BY t.shop_id
		";
		$query_turn_over = sqlsrv_query( $conn, $sql_turn_over );

		$ret_turn_over = array();
		while( $row = sqlsrv_fetch_array( $query_turn_over, SQLSRV_FETCH_ASSOC) ) {
			  array_push($ret_turn_over, $row);
		}
		// Num of transactions
		$sql_num_transactions = "
			SELECT COUNT(*) Transactions ,
				t.shop_id as ShopId
			FROM transactions t
			LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
			LEFT JOIN shops s ON s.id = t.shop_id AND tk.in_statistics=1
			WHERE t.delete_operator_id IS NULL
				AND t.bookkeeping_date BETWEEN '" . $start . "' AND '" . $end . "'
			GROUP BY t.shop_id
		";
		$query_num_transactions = sqlsrv_query( $conn, $sql_num_transactions );

		$ret_num_transactions = array();
		while( $row = sqlsrv_fetch_array( $query_num_transactions, SQLSRV_FETCH_ASSOC) ) {
			  array_push($ret_num_transactions, $row);
		}
		// Average bill
		$sql_average_bill = "
			SELECT SUM(t.total_amount-COALESCE(t.tax_amount,0)) / COUNT(*) average_bill
			FROM transactions t
			LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
			WHERE t.bookkeeping_date BETWEEN '" . $start . "' AND '" . $end . "'
			 	AND t.delete_operator_id IS NULL
		";
		$query_average_bill = sqlsrv_query( $conn, $sql_average_bill );

		$ret_average_bill = array();
		while( $row = sqlsrv_fetch_array( $query_average_bill, SQLSRV_FETCH_ASSOC) ) {
			  array_push($ret_average_bill, $row);
		}

		// Discounts
		$sql_discount = "
			SELECT COALESCE(SUM(ta.discount),0) Discount, t.shop_id as ShopId
			FROM transactions t INNER JOIN trans_articles ta ON ta.transaction_id = t.id
			WHERE  t.delete_operator_id IS NULL AND t.bookkeeping_date BETWEEN '" . $start . "' AND '" . $end . "'
			Group by t.shop_id
		";
		$query_discount = sqlsrv_query( $conn, $sql_discount );

		$ret_discount = array();
		while( $row = sqlsrv_fetch_array( $query_discount, SQLSRV_FETCH_ASSOC) ) {
			  array_push($ret_discount, $row);
		}

		// Promotions
		$sql_promotion = "
			SELECT
			sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(coalesce(tp.offered_amount,0)) + sum(tp.articles_amount) + sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(CASE WHEN tp.offered_amount <> 0 then tp.articles_amount ELSE 0 END) Promotion
			FROM transactions t LEFT JOIN trans_promotions tp on tp.transaction_id = t.id
			WHERE t.delete_operator_id IS NULL
			 	AND t.delete_operator_id IS NULL AND t.bookkeeping_date BETWEEN '" . $start . "' AND '" . $end . "'
		";
		$query_promotion = sqlsrv_query( $conn, $sql_promotion );

		$ret_promotion = array();
		while( $row = sqlsrv_fetch_array( $query_promotion, SQLSRV_FETCH_ASSOC) ) {
			  array_push($ret_promotion, $row);
		}
		$ret = array(
			"shops" => $ret_shop_list,
			"turnover" => $ret_turn_over,
			"transactions" => $ret_num_transactions,
			"discount"	=> $ret_discount,
			"promotion"	=> $ret_promotion,
			"average_bill" => $ret_average_bill
		);

		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $ret
		));
	}


}
