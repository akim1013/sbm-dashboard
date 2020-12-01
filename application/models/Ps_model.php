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
  public function get_item($company){
    $ret = array();
    $this->db->select('ps_items.*');
    $this->db->from('ps_items');
    $this->db->join('users', 'users.id = ps_items.created_user_id', 'left');
    $this->db->where('users.company', $company);
    $query = $this->db->get();
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
  public function remove_ref_is_order($ref_is_id){
    return $this->db->delete('ps_orders', array('ref_is_id' => $ref_is_id));
  }
  public function add_batch_item($data){
    if(count($data) == 0){
      return 1; // Nothing to be added
    }
    $res = $this->db->insert_batch('ps_items', $data);
    return $res;
  }
  public function update_batch_item($ids, $data){
    if(count($data) == 0){
      return 1;
    }
    // $this->db->update_batch('ps_items', $data, 'sales_price');
    // $this->db->update_batch('ps_items', $data, 'qty');
    // $this->db->update_batch('ps_items', $data, 'moq');
    // $this->db->update_batch('ps_items', $data, 'cbm');
    // $this->db->update_batch('ps_items', $data, 'price');
    // $this->db->update_batch('ps_items', $data, 'gross_weight');
    // $this->db->update_batch('ps_items', $data, 'packing_info');
    foreach($ids as $id){
      foreach($data as $item){
        if($id == $item->inventory_id){
          $_item = array(
            'sales_price' => $item->sales_price,
            'qty' => $item->qty,
            'moq' => $item->moq,
            'cbm' => $item->cbm,
            'price' => $item->price,
            'gross_weight' => $item->gross_weight,
            'packing_info' => $item->packing_info
          );
          $this->db->where('inventory_id', $id);
          $this->db->update('ps_items', $_item);
        }
      }
    }
    return 1;
  }
  public function add_order($order){
    $this->db->where('order_id', $order['order_id']);
    $this->db->from('ps_orders');
    $temp = $this->db->get()->num_rows();
    if($temp == 0){
      $res = $this->db->insert('ps_orders', $order);
      if($res > 0){
        return 1;
      }else{
        return -1;
      }
    }else{
      // update if order is existing -- for draft orders
      $this->db->set('status', $order['status']);
      $this->db->set('updated_date', date("Y-m-d H:i:s"));
      $this->db->where('order_id', $order['order_id']);
      $res = $this->db->update('ps_orders');
      if($res > 0){
        return 1;
      }else{
        return -1;
      }
    }
  }
  public function add_order_items($order_id, $items, $flag){
    if($flag != 1){
      $this->db->delete('ps_order_details', array('order_id' => $order_id));
    }

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
    $this->db->order_by('order_time', 'DESC');

    $query = $this->db->get('ps_orders');
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function get_all_orders($company){
    $ret = array();
    $this->db->select('ps_orders.*');
    $this->db->from('ps_orders');
    $this->db->join('users', 'users.id = ps_orders.customer_id', 'left');
    $this->db->where('users.company', $company);

    $this->db->order_by('order_time', 'DESC');

    $query = $this->db->get();
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
  public function update_order_status($order_id, $status, $shipment_date, $shipment_ref_number, $updated_date){
    $this->db->set('status', $status);
    $this->db->set('shipment_date', $shipment_date);
    $this->db->set('shipment_ref_number', $shipment_ref_number);
    $this->db->set('updated_date', $updated_date);
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
  public function update_order($ordered_item, $item){
    $this->db->set('qty', $ordered_item->qty);
    $this->db->where('id', $ordered_item->id);
    $res1 = $this->db->update('ps_order_details');

    $this->db->set('price', $item->price);
    $this->db->where('id', $item->id);
    $res2 = $this->db->update('ps_items');

    return $res1 && $res2;
  }
  public function delete_ordered_item($item){
    return $this->db->delete('ps_order_details', array('id' => $item->id));
  }
}
?>
