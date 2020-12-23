<?php
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasing extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('purchasing_model');
		$this->load->model('user_model');
  }
  private function upload_file($file, $id){
    $target_dir = 'C:/inetpub/wwwroot/sbm-dashboard/uploads/'; // add the specific path to save the file
    $data = explode(',', $file);
    $decoded_file = base64_decode($data[1]); // decode the file
    $extension = $this->get_extension($file); // extract extension from mime type
    $file_name = uniqid() .'.'. $extension; // rename file as a unique name
    $file_dir = $target_dir . $file_name;
    try {
      file_put_contents($file_dir, $decoded_file); // save
      return $this->purchasing_model->update_item_image(base_url(). 'uploads/' . $file_name, $id);
    } catch (Exception $e) {
      return -1;
    }
  }
  private function get_extension($file) {
    $ret = explode('/', explode(';', $file)[0])[1];
    return $ret;
  }

	public function email_setting(){
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

  public function get_item(){
		$company = $this->input->post('company');
    $res = $this->purchasing_model->get_item($company);
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
			$flag = $this->purchasing_model->validate($item->inventory_id, $item->company);
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
		$res1 = $this->purchasing_model->add_batch_item($filtered['items']);
		$res2 = $this->purchasing_model->update_batch_item($filtered['invalid_ids'], $filtered['invalid_items']);
		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res1 && $res2,
			'invalid_ids' => $filtered['invalid_ids']
		));
	}
  public function update_item_status(){
    $res = $this->purchasing_model->update_item_status($this->input->post('status'), $this->input->post('id'));
    echo json_encode(array(
      'status' => 'success',
      'status_code' => 200,
      'data' => $res
    ));
  }
  public function remove_item(){
    $res = $this->purchasing_model->remove_item($this->input->post('id'));
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }

  public function add_item(){
    $item = array(
      'inventory_id' => $this->input->post('inventory_id'),
      'company' => $this->input->post('company'),
			'vendor_name' => $this->input->post('vendor_name'),
			'vendor_item_num' => $this->input->post('vendor_item_num'),
      'user_access' => $this->input->post('user_access'),
      'gross_weight' => $this->input->post('gross_weight'),
      'category' => $this->input->post('category'),
      'description' => $this->input->post('description'),
      'vendor_description' => $this->input->post('vendor_description'),
      'packing_info' => $this->input->post('packing_info'),
      'unit' => $this->input->post('unit'),
      'price' => $this->input->post('price'),
      'cbf' => $this->input->post('cbf'),
      'qty' => $this->input->post('qty'),
      'moq' => $this->input->post('moq'),
      'status' => $this->input->post('status'),
      'qty_display' => $this->input->post('qty_display'),
      'created_at' => date('Y-m-d h:i:s'),
      'updated_at' => date('Y-m-d h:i:s')
		);
    $id = $this->purchasing_model->add_item($item);
    $file_uploaded = 0;
    if($id){
      $file = $this->input->post('image');
      if(isset($file) && (!empty($file))){
        $file_uploaded = $this->upload_file($file, $id);
        if($file_uploaded != 1){
          $id = -2;
        }
      }
    }
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => 1
		));
  }

  public function get_item_by_id(){
    $res = $this->purchasing_model->get_item_by_id($this->input->post('id'));
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }

  public function update_item(){
    $item = array(
      'user_access' => $this->input->post('user_access'),
      'gross_weight' => $this->input->post('gross_weight'),
      'category' => $this->input->post('category'),
      'description' => $this->input->post('description'),
      'vendor_description' => $this->input->post('vendor_description'),
      'packing_info' => $this->input->post('packing_info'),
      'unit' => $this->input->post('unit'),
      'price' => $this->input->post('price'),
      'cbf' => $this->input->post('cbf'),
      'qty' => $this->input->post('qty'),
      'moq' => $this->input->post('moq'),
      'status' => $this->input->post('status'),
      'qty_display' => $this->input->post('qty_display'),
      'updated_at' => date('Y-m-d h:i:s')
		);
    $res = $this->purchasing_model->update_item($item, $this->input->post('id'));

    $file_uploaded = 0;
    if($res == 1){
      $file = $this->input->post('image');
      if(isset($file) && (!empty($file)) && (!strpos($file, 'uploads'))){
        $file_uploaded = $this->upload_file($file, $this->input->post('id'));
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

  public function get_all_orders(){
		$company = $this->input->post('company');
    $shop = $this->input->post('shop');
    $res = $this->purchasing_model->get_all_orders($company, $shop);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
  public function get_order_details(){
		$order_id = $this->input->post('order_id');
    $order_detail = $this->purchasing_model->get_order_detail($order_id);
    $order_items = $this->purchasing_model->get_order_items($order_id);
    $approvements = $this->purchasing_model->get_order_approvements($order_id);
    $shipments = $this->purchasing_model->get_order_shipments($order_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
      'order_detail' => $order_detail,
			'order_items' => $order_items,
      'order_approvements' => $approvements,
      'order_shipments' => $shipments
		));
  }
	public function approve_item(){
		$order_id = $this->input->post('order_id');
		$item_id = $this->input->post('item_id');
		$approved_qty = $this->input->post('approved_qty');
		$approved_price = $this->input->post('approved_price');

		$this->purchasing_model->update_order_status($order_id, 'processing');
		$this->purchasing_model->update_item_qty($item_id, $approved_qty);

		$res = $this->purchasing_model->approve_item(array(
			'order_id' => $order_id,
			'item_id' => $item_id,
			'approved_qty' => $approved_qty,
			'approved_price' => $approved_price
		));

		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function remove_approved_item(){
		$approvement_id = $this->input->post('approvement_id');
		$res = $this->purchasing_model->remove_approved_item($approvement_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function ship_order(){
		$order_id = $this->input->post('order_id');
		$shipment_date = $this->input->post('shipment_date');
		$shipment_ref_number = $this->input->post('shipment_ref_number');
		$shipment_id = $this->purchasing_model->ship_order(array(
			'order_id' => $order_id,
			'shipment_date' => $shipment_date,
			'shipment_ref_number' => $shipment_ref_number
		));
		$res = $this->purchasing_model->update_approvement_status($order_id, $shipment_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function complete_order(){
		$order_id = $this->input->post('order_id');
		$res = $this->purchasing_model->complete_order($order_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }

	public function get_user_items(){
		$company = $this->input->post('company');
		$res = $this->purchasing_model->get_item($company);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function create_order(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$branch = $this->input->post('branch');
		$customer_id = $this->input->post('customer_id');
		$order_ref = $this->input->post('order_ref');
		$res = $this->purchasing_model->create_order(array(
			'company' => $company,
			'shop' => $shop,
			'branch' => $branch,
			'customer_id' => $customer_id,
			'order_ref' => $order_ref,
			'status' => 'draft',
			'order_type' => 'purchasing',
			'order_time' => date("Y-m-d H:i:s")
		));
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }

	public function create_auto_order(){
		$order = array(
			'order_ref' => $this->input->post('order_ref'),
			'company' => $this->input->post('company'),
			'branch' => $this->input->post('branch'),
			'customer_id' => $this->input->post('customer_id'),
			'shop' => $this->input->post('shop'),
			'order_time' => $this->input->post('order_time'),
			'inventory_system_ref_id' => $this->input->post('ref_is_id'),
			'order_type' => $this->input->post('type'),
			'order_time' => date("Y-m-d H:i:s"),
			'status' => 'pending'
		);
		$items = json_decode($this->input->post('items'));
		$order_id = $this->purchasing_model->create_order($order);
		$res2 = $this->purchasing_model->add_ordered_items_auto($order_id, $items);

		echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res2
		));
	}

	public function get_current_order(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$res = $this->purchasing_model->get_current_order($company, $shop);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function get_ordered_items(){
		$order_id = $this->input->post('id');
		$res = $this->purchasing_model->get_ordered_items($order_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function add_ordered_item(){
		$order_ref = $this->input->post('order_ref');
		$item_id = $this->input->post('item_id');
		$order_qty = $this->input->post('order_qty');
		$res = $this->purchasing_model->add_ordered_item($order_ref, $item_id, $order_qty);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
  }
	public function remove_order_detail_item(){
		$order_id = $this->input->post('order_id');
		$item_id = $this->input->post('item_id');
		$res = $this->purchasing_model->remove_order_detail_item($order_id, $item_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function place_order(){
		$order_id = $this->input->post('order_id');
		$res = $this->purchasing_model->place_order($order_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function cancel_order(){
		$order_id = $this->input->post('order_id');
		$res = $this->purchasing_model->cancel_order($order_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function get_order_history(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$branch = $this->input->post('branch');
		$res = $this->purchasing_model->get_order_history($company, $shop, $branch);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function get_order_history_inventory(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$branch = $this->input->post('branch');
		$res = $this->purchasing_model->get_order_history_inventory($company, $shop, $branch);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}

	public function accept_item_inventory(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$branch_id = $this->input->post('branch_id');
		$customer_id = $this->input->post('customer_id');
		$item = json_decode($this->input->post('item'));
		$this->purchasing_model->update_stock_history_accept_from_inventory(
			$company,
			$shop,
			$branch_id,
			$customer_id,
			$item->id,
			$item->approved_qty
		);
		$res = $this->purchasing_model->accept_item($item->approvement_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
	public function accept_item_purchasing(){
		$company = $this->input->post('company');
		$shop = $this->input->post('shop');
		$branch_id = $this->input->post('branch_id');
		$customer_id = $this->input->post('customer_id');
		$item = json_decode($this->input->post('item'));
		$this->purchasing_model->update_stock_history_accept_from_purchasing(
			$company,
			$shop,
			$branch_id,
			$customer_id,
			$item->id,
			$item->approved_qty
		);
		$res = $this->purchasing_model->accept_item($item->approvement_id);
    echo json_encode(array(
			'status' => 'success',
			'status_code' => 200,
			'data' => $res
		));
	}
}
