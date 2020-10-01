<?php
class Ps_model extends CI_model{
  public function add_item($data){
    if($this->validate($data['inventory_id']) > 0){
      return 0;
    }
    $res = $this->db->insert('ps_items', $data);
    if($res > 0){
      return 1;
    }else{
      return -1;
    }
  }
  public function update_item($data, $inventory_id){
    $this->db->set('created_user_id', $data['created_user_id']);
    $this->db->set('user_access', $data['user_access']);
    $this->db->set('gross_weight', $data['gross_weight']);
    $this->db->set('category', $data['category']);
    $this->db->set('description', $data['description']);
    $this->db->set('vendor_description', $data['vendor_description']);
    $this->db->set('packing_info', $data['packing_info']);
    $this->db->set('uom', $data['uom']);
    $this->db->set('price', $data['price']);
    $this->db->set('cbm', $data['cbm']);
    $this->db->set('qty', $data['qty']);
    $this->db->set('moq', $data['moq']);
    $this->db->set('status', $data['status']);
    $this->db->set('qty_display', $data['qty_display']);
    $this->db->set('updated_at', $data['updated_at']);
    $this->db->where('inventory_id', $inventory_id);
    $res = $this->db->update('ps_items');
    if($res > 0){
        return 1;
    }else{
        return -1;
    }
  }
  public function update_item_status($status, $inventory_id){
    $this->db->set('status', $status);
    $this->db->where('inventory_id', $inventory_id);
    $res = $this->db->update('ps_items');
    if($res > 0){
        return 1;
    }else{
        return -1;
    }
  }
  public function validate($inventory_id){
    $this->db->where('inventory_id', $inventory_id);
    $this->db->from('ps_items');
    return $this->db->get()->num_rows();
  }
  public function update_item_image($file, $inventory_id){
    $this->db->set('image', $file);
    $this->db->where('inventory_id', $inventory_id);
    $res = $this->db->update('ps_items');
    if($res > 0){
        return 1;
    }else{
        return 0;
    }
  }
  public function get_item(){
    $ret = array();
    $this->db->select('*');
    $query = $this->db->get('ps_items');
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function get_item_by_id($inventory_id){
    $this->db->select('*');
    $this->db->where('inventory_id', $inventory_id);
    $this->db->from('ps_items');
    $res = $this->db->get();
    return $res->result()[0];
  }
  public function remove_item($inventory_id){
    return $this->db->delete('ps_items', array('inventory_id' => $inventory_id));
  }
  public function add_batch_item($data){
    $sql = "INSERT INTO ps_items (inventory_id,gross_weight,category,vendor_description,description,image,packing_info,uom,price,cbm,qty,moq,status,qty_display,user_access,created_user_id) VALUES ";
    $idx = 0;
    foreach($data as $array){
      $idx++;
      $sql = $sql . "(";
      $count = 0;
      foreach($array as $item){
        $count++;
        $sql = $sql . "'" . $item . "'";
        if($count != 16){
          $sql = $sql . ", ";
        }
      }
      $sql = $sql . ")";
      if($idx != count($data)){
        $sql = $sql . ", ";
      }
    }
    $res = $this->db->query($sql);
    return $res;
  }
  public function add_order($order){
    $res = $this->db->insert('ps_orders', $order);
    if($res > 0){
      return 1;
    }else{
      return -1;
    }
  }
  public function add_order_items($order_id, $items){
    $sql = "INSERT INTO ps_order_details (order_id, item_id, qty) VALUES";
    $idx = 0;
    foreach($items as $item){
      $idx++;
      $sql = $sql . "('" . $order_id . "', " . "'" . $item->item_id . "', " . "'" . $item->qty . "')";
      if($idx != count($items)){
        $sql = $sql . ", ";
      }
    }
    $res = $this->db->query($sql);
    return $res;
  }
  public function get_orders($customer_id, $limit){
    $ret = array();
    $this->db->select('*');
    if($limit != 0){
      $this->db->limit($limit);
    }
    $this->db->where('customer_id', $customer_id);
    $query = $this->db->get('ps_orders');
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function get_all_orders(){
    $ret = array();
    $this->db->select('*');
    $query = $this->db->get('ps_orders');
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function get_order_details($order_id){
    $ret = array();
    $this->db->select('*');
    $this->db->where('order_id', $order_id);
    $query = $this->db->get('ps_order_details');
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function approve_order($order_id){
    $this->db->set('status', 'approved');
    $this->db->where('order_id', $order_id);
    $res = $this->db->update('ps_orders');
    if($res > 0){
        return true;
    }else{
        return false;
    }
  }
  public function delete_order($order_id){
    $res1 = $this->db->delete('ps_orders', array('order_id' => $order_id));
    $res2 = $this->db->delete('ps_order_details', array('order_id' => $order_id));
    return $res1 && $res2;
  }
}
?>
