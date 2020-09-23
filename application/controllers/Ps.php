<?php
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
defined('BASEPATH') OR exit('No direct script access allowed');
class Ps extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('ps_model');
  }
  public function add_item(){
    $item = array(
      'inventory_id' => $this->input->post('inventory_id'),
      'branch' => $this->input->post('branch'),
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
      'branch' => $this->input->post('branch'),
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
  private function upload_file($file, $inventory_id){
    $target_dir = 'C:/inetpub/wwwroot/sbm-dashboard/uploads/'; // add the specific path to save the file
    $data = explode(',', $file);
    $decoded_file = base64_decode($data[1]); // decode the file
    $extension = explode('/', mime_content_type($file))[1]; // extract extension from mime type
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
    $res = $this->ps_model->get_item();
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
}
