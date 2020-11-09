<?php
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Ps extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('ps_model');
		$this->load->model('user_model');
  }
	public function old_email_setting(){
		$config = array();
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'a2plcpnl0005.prod.iad2.secureserver.net';
		$config['smtp_user'] = 'purchasing-system@sbmtec.com';
		$config['smtp_pass'] = '#%uLExt[!HTX';
		$config['smtp_port'] = 587;
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		return $config;
	}

	public function new_email_setting(){
		$config = array();
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'ssl://sbmtec.com';
		$config['smtp_user'] = 'purchasing-system@sbmtec.com';
		$config['smtp_pass'] = 'O1lCT,gVV%Pp';
		$config['smtp_port'] = 465;
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';
		return $config;
	}
  public function add_item(){
    $item = array(
      'inventory_id' => $this->input->post('inventory_id'),
      'created_user_id' => $this->input->post('created_user_id'),
      'user_access' => $this->input->post('user_access'),
      'gross_weight' => $this->input->post('gross_weight'),
      'category' => $this->input->post('category'),
      'description' => $this->input->post('description'),
      'vendor_description' => $this->input->post('vendor_description'),
      'packing_info' => $this->input->post('packing_info'),
      'uom' => $this->input->post('uom'),
      'price' => $this->input->post('price'),
      'cbm' => $this->input->post('cbm'),
      'qty' => $this->input->post('qty'),
      'moq' => $this->input->post('moq'),
      'status' => $this->input->post('status'),
      'qty_display' => $this->input->post('qty_display'),
      'created_at' => date('Y-m-d h:i:s'),
      'updated_at' => date('Y-m-d h:i:s')
		);
    $res = $this->ps_model->add_item($item);
    $file_uploaded = 0;
    if($res == 1){
      $file = $this->input->post('image');
      if(isset($file) && (!empty($file))){
        $file_uploaded = $this->upload_file($file, $this->input->post('inventory_id'));
        if($file_uploaded != 1){
          $res = -2;
        }
      }
    }
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
  public function update_item(){
    $item = array(
      'created_user_id' => $this->input->post('created_user_id'),
      'user_access' => $this->input->post('user_access'),
      'gross_weight' => $this->input->post('gross_weight'),
      'category' => $this->input->post('category'),
      'description' => $this->input->post('description'),
      'vendor_description' => $this->input->post('vendor_description'),
      'packing_info' => $this->input->post('packing_info'),
      'uom' => $this->input->post('uom'),
      'price' => $this->input->post('price'),
      'cbm' => $this->input->post('cbm'),
      'qty' => $this->input->post('qty'),
      'moq' => $this->input->post('moq'),
      'status' => $this->input->post('status'),
      'qty_display' => $this->input->post('qty_display'),
      'updated_at' => date('Y-m-d h:i:s')
		);
    $res = $this->ps_model->update_item($item, $this->input->post('inventory_id'));

    $file_uploaded = 0;
    if($res == 1){
      $file = $this->input->post('image');
      if(isset($file) && (!empty($file)) && (!strpos($file, 'uploads'))){
        $file_uploaded = $this->upload_file($file, $this->input->post('inventory_id'));
        if($file_uploaded != 1){
          $res = -2;
        }
      }else{
        $res = 1;
      }
    }
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
  public function update_item_status(){
    $res = $this->ps_model->update_item_status($this->input->post('status'), $this->input->post('inventory_id'));
    echo json_encode(array(
      'status' => 'success',
      'status_code' => 200,
      'data' => $res
    ));
  }
	public function update_order(){
		$ordered_item = json_decode($this->input->post('item'));
		$item = json_decode($this->input->post('g_item'));
		$res = $this->ps_model->update_order($ordered_item, $item);
		echo json_encode(array(
      'status' => 'success',
      'status_code' => 200,
      'data' => $res
    ));
	}
	public function delete_ordered_item(){
		$ordered_item = json_decode($this->input->post('item'));
		$res = $this->ps_model->delete_ordered_item($ordered_item);
		echo json_encode(array(
      'status' => 'success',
      'status_code' => 200,
      'data' => $res
    ));
	}
	public function add_additional_items_to_order(){
		$items = json_decode($this->input->post('items'));
		$res = $this->ps_model->add_order_items($this->input->post('order_id'), $items, 1);
		echo json_encode(array(
      'status' => 'success',
      'status_code' => 200,
      'data' => $res
    ));
	}
	private function get_extension($file) {
		$ret = explode('/', explode(';', $file)[0])[1];
		return $ret;
	}
  private function upload_file($file, $inventory_id){
    $target_dir = 'C:/inetpub/wwwroot/sbm-dashboard/uploads/'; // add the specific path to save the file
    $data = explode(',', $file);
    $decoded_file = base64_decode($data[1]); // decode the file
    $extension = $this->get_extension($file); // extract extension from mime type
    $file_name = uniqid() .'.'. $extension; // rename file as a unique name
    $file_dir = $target_dir . $file_name;
    try {
      file_put_contents($file_dir, $decoded_file); // save
      return $this->ps_model->update_item_image(base_url(). 'uploads/' . $file_name, $inventory_id);
    } catch (Exception $e) {
      return -1;
    }
  }
  public function get_item(){
		$company = $this->input->post('company');
    $res = $this->ps_model->get_item($company);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
  public function get_item_by_id(){
    $res = $this->ps_model->get_item_by_id($this->input->post('inventory_id'));
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
  public function remove_item(){
    $res = $this->ps_model->remove_item($this->input->post('inventory_id'));
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function filter_items($items){
		$valid = array();
		$invalid = array();
		$invalid_ids = array();
		foreach($items as $item){
			$flag = $this->ps_model->validate($item->inventory_id);
			if($flag == 0){
				array_push($valid, $item);
			}else{
				array_push($invalid, $item);
				array_push($invalid_ids, $item->inventory_id);
			}
		}
		return array('items' => $valid, 'invalid_ids' => $invalid_ids, 'invalid_items' => $invalid);
	}
	public function add_batch_item(){
		$items = json_decode($this->input->post('items'));
		$filtered = $this->filter_items($items);
		$res1 = $this->ps_model->add_batch_item($filtered['items']);
		$res2 = $this->ps_model->update_batch_item($filtered['invalid_ids'], $filtered['invalid_items']);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res1 && $res2,
			'invalid_ids' => $filtered['invalid_ids']
		));
	}
	public function add_order(){
		$order = array(
			'order_id' => $this->input->post('order_id'),
			'customer_id' => $this->input->post('customer_id'),
			'order_time' => $this->input->post('order_time'),
			'status' => $this->input->post('status')
		);
		$items = json_decode($this->input->post('items'));
		$res = $this->ps_model->add_order($order);
		if($res == 1){
			$res = $this->ps_model->add_order_items($this->input->post('order_id'), $items, 0);
		}else{
			echo json_encode(array(
				'status' => 'failed'
			));
		}
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function get_orders(){
		$limit = $this->input->post('limit');
		$customer_id = $this->input->post('customer_id');
    $res = $this->ps_model->get_orders($customer_id, $limit);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function get_all_orders(){
		$company = $this->input->post('company');
    $res = $this->ps_model->get_all_orders($company);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function get_order_details(){
		$order_id = $this->input->post('order_id');
    $res = $this->ps_model->get_order_details($order_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function get_ps_users(){
		$res = $this->user_model->get_ps_users();
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function get_ps_admin(){
		$company = $this->input->post('company');
		$res = $this->user_model->get_ps_admin($company);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function update_order_status(){
		$order_id = $this->input->post('order_id');
		$status = $this->input->post('status');
		$shipment_date = $this->input->post('shipment_date');
		$shipment_ref_number = $this->input->post('shipment_ref_number');
		$updated_date = date("Y-m-d H:i:s");
		$res = $this->ps_model->update_order_status($order_id, $status, $shipment_date, $shipment_ref_number, $updated_date);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function delete_order(){
		$order_id = $this->input->post('order_id');
		$res = $this->ps_model->delete_order($order_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function send_mail_to_user(){
		$order_info = array();
		$order_info['order_id'] = $this->input->post('order_id');
		$order_info['items'] = json_decode($this->input->post('item_details'));
		$order_info['user_name'] = $this->input->post('user_name');
		$order_info['company'] = $this->input->post('company');

		$order_info['total_price'] = $this->input->post('total_price');
		$order_info['info_dry'] = json_decode($this->input->post('info_dry'));
		$order_info['info_frozen'] = json_decode($this->input->post('info_frozen'));
		$order_info['message'] = $this->input->post('message');

		$this->load->library('email');
		$this->email->initialize($this->old_email_setting());

		$from = 'purchasing-system@sbmtec.com';
    $to = $this->input->post('to');
    $subject = $this->input->post('subject');
    $message = $this->load->view('email/po_mail', $order_info, true);

		$this->email->set_newline("\r\n");
    $this->email->from($from, 'Purchasing System');
    $this->email->to($to);
    $this->email->subject($subject);
    $this->email->message($message);

		if ($this->email->send()) {
			echo json_encode(array(
				'status' => 'success',
				'status_code' => 200,
				'data' => "Mail sent successfully!"
			));
    } else {
			echo json_encode(array(
				'status' => 'failed',
				'status_code' => 400,
				'data' => show_error($this->email->print_debugger())
			));
    }
	}
	public function send_mail_to_admin(){
		$order_info = array();
		$order_info['order_id'] = $this->input->post('order_id');
		$order_info['items'] = json_decode($this->input->post('item_details'));
		$order_info['user_name'] = $this->input->post('user_name');
		$order_info['admin_name'] = $this->input->post('admin_name');
		$order_info['company'] = $this->input->post('company');

		$order_info['total_price'] = $this->input->post('total_price');
		$order_info['info_dry'] = json_decode($this->input->post('info_dry'));
		$order_info['info_frozen'] = json_decode($this->input->post('info_frozen'));
		$order_info['message'] = $this->input->post('message');

		$this->load->library('email');
		$this->email->initialize($this->old_email_setting());

		$from = 'purchasing-system@sbmtec.com';
    $to = $this->input->post('to');
    $subject = $this->input->post('subject');
    $message = $this->load->view('email/po_admin_mail', $order_info, true);

		$this->email->set_newline("\r\n");
    $this->email->from($from, 'Purchasing System');
    $this->email->to($to);
    $this->email->subject($subject);
    $this->email->message($message);

		if ($this->email->send()) {
			echo json_encode(array(
				'status' => 'success',
				'status_code' => 200,
				'data' => "Mail sent successfully!"
			));
    } else {
			echo json_encode(array(
				'status' => 'failed',
				'status_code' => 400,
				'data' => show_error($this->email->print_debugger())
			));
    }
	}
	public function send_status_update_mail(){
		$user = json_decode($this->input->post('user'));

		$this->load->library('email');
		$this->email->initialize($this->old_email_setting());

		$from = 'purchasing-system@sbmtec.com';
    $to = $user->email;
    $subject = $this->input->post('subject');
    $message = $this->input->post('message');

		$this->email->set_newline("\r\n");
    $this->email->from($from, 'Purchasing System');
    $this->email->to($to);
    $this->email->subject($subject);
    $this->email->message($message);

		if ($this->email->send()) {
			echo json_encode(array(
				'status' => 'success',
				'status_code' => 200,
				'data' => "Mail sent successfully!"
			));
    } else {
			echo json_encode(array(
				'status' => 'failed',
				'status_code' => 400,
				'data' => show_error($this->email->print_debugger())
			));
    }
	}
}
