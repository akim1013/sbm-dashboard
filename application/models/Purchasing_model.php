<?php
class Purchasing_model extends CI_model{
  public function validate($inventory_id){
    $this->db->where('inventory_id', $inventory_id);
    $this->db->from('purchasing_system_items');
    return $this->db->get()->num_rows();
  }
  public function get_item($company, $shop){
    $ret = array();
    $this->db->select('*');
    $this->db->from('purchasing_system_items');
    $this->db->where('company', $company);
    $this->db->where('shop', $shop);
    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function add_batch_item($data){
    if(count($data) == 0){
      return 1;
    }
    $res = $this->db->insert_batch('purchasing_system_items', $data);
    return $res;
  }
  public function update_batch_item($ids, $data){
    if(count($data) == 0){
      return 1;
    }
    foreach($ids as $id){
      foreach($data as $item){
        if($id == $item->inventory_id){
          $_item = array(
            'qty' => $item->qty,
            'moq' => $item->moq,
            'cbf' => $item->cbf,
            'price' => $item->price,
            'gross_weight' => $item->gross_weight,
            'packing_info' => $item->packing_info
          );
          $this->db->where('inventory_id', $id);
          $this->db->update('purchasing_system_items', $_item);
        }
      }
    }
    return 1;
  }
  public function update_item_status($status, $inventory_id){
    $this->db->set('status', $status);
    $this->db->where('inventory_id', $inventory_id);
    $res = $this->db->update('purchasing_system_items');
    if($res > 0){
        return 1;
    }else{
        return -1;
    }
  }
  public function remove_item($inventory_id){
    return $this->db->delete('purchasing_system_items', array('inventory_id' => $inventory_id));
  }
  public function add_item($data){
    if($this->validate($data['inventory_id']) > 0){
      return 0;
    }
    $res = $this->db->insert('purchasing_system_items', $data);
    if($res > 0){
      return 1;
    }else{
      return -1;
    }
  }
  public function update_item_image($file, $inventory_id){
    $this->db->set('image', $file);
    $this->db->where('inventory_id', $inventory_id);
    $res = $this->db->update('purchasing_system_items');
    if($res > 0){
        return 1;
    }else{
        return 0;
    }
  }
  public function get_item_by_id($inventory_id){
    $this->db->select('*');
    $this->db->where('inventory_id', $inventory_id);
    $this->db->from('purchasing_system_items');
    $res = $this->db->get();
    return $res->result()[0];
  }
  public function update_item($data, $inventory_id){
    $this->db->set('user_access', $data['user_access']);
    $this->db->set('gross_weight', $data['gross_weight']);
    $this->db->set('category', $data['category']);
    $this->db->set('description', $data['description']);
    $this->db->set('vendor_description', $data['vendor_description']);
    $this->db->set('packing_info', $data['packing_info']);
    $this->db->set('unit', $data['unit']);
    $this->db->set('price', $data['price']);
    $this->db->set('cbf', $data['cbf']);
    $this->db->set('qty', $data['qty']);
    $this->db->set('moq', $data['moq']);
    $this->db->set('status', $data['status']);
    $this->db->set('qty_display', $data['qty_display']);
    $this->db->set('updated_at', $data['updated_at']);
    $this->db->where('inventory_id', $inventory_id);
    $res = $this->db->update('purchasing_system_items');
    if($res > 0){
        return 1;
    }else{
        return -1;
    }
  }

  // Orders
  public function get_all_orders($company, $shop){
    $ret = array();
    $this->db->select('purchasing_system_orders.*, users.name, users.email');
    $this->db->from('purchasing_system_orders');
    $this->db->join('users', 'users.id = purchasing_system_orders.customer_id', 'left');
    $this->db->where('purchasing_system_orders.company', $company);
    $this->db->where('purchasing_system_orders.shop', $shop);
    $this->db->where('purchasing_system_orders.status <>', 'draft');
    $this->db->order_by('order_time', 'DESC');

    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function get_order_detail($order_id){
    $ret = array();
    $this->db->select('*');
    $this->db->from('purchasing_system_orders');
    $this->db->where('purchasing_system_orders.id', $order_id);
    $query = $this->db->get();

    return $query->result()[0];
  }
  public function get_order_items($order_id){
    $ret = array();
    $this->db->select('purchasing_system_order_items.order_qty, purchasing_system_items.*');
    $this->db->from('purchasing_system_order_items');
    $this->db->join('purchasing_system_items', 'purchasing_system_items.id = purchasing_system_order_items.item_id', 'left');
    $this->db->where('purchasing_system_order_items.order_id', $order_id);
    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function get_order_shipments($order_id){
    $ret = array();
    $this->db->select('
      purchasing_system_shipments.id shipment_id,
      purchasing_system_shipments.order_id order_id,
      purchasing_system_shipments.shipment_ref_number,
      purchasing_system_shipments.shipment_date,
      purchasing_system_approvements.id approvement_id,
      purchasing_system_approvements.approved_qty,
      purchasing_system_approvements.approved_price,
      purchasing_system_approvements.status item_status,
      purchasing_system_items.*
    ');
    $this->db->from('purchasing_system_shipments');
    $this->db->join('purchasing_system_approvements', 'purchasing_system_approvements.shipment_id = purchasing_system_shipments.id', 'left');
    $this->db->join('purchasing_system_items', 'purchasing_system_items.id = purchasing_system_approvements.item_id', 'left');
    $this->db->where('purchasing_system_shipments.order_id', $order_id);
    $this->db->where('purchasing_system_approvements.status', 'shipped');
    $this->db->or_where('purchasing_system_approvements.status', 'accepted');
    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function get_order_approvements($order_id){
    $ret = array();
    $this->db->select('
      purchasing_system_approvements.id approvement_id,
      purchasing_system_approvements.approved_qty,
      purchasing_system_approvements.approved_price,
      purchasing_system_approvements.status approvement_status,
      purchasing_system_items.*
    ');
    $this->db->from('purchasing_system_approvements');
    $this->db->join('purchasing_system_items', 'purchasing_system_items.id = purchasing_system_approvements.item_id', 'left');
    $this->db->where('purchasing_system_approvements.order_id', $order_id);
    $this->db->where('purchasing_system_approvements.status', 'approved');
    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function approve_item($data){
    $this->db->select('*');
    $this->db->from('purchasing_system_approvements');
    $this->db->where('order_id', $data['order_id']);
    $this->db->where('item_id', $data['item_id']);
    $this->db->where('status', 'approved');
    $this->db->where('approved_price', $data['approved_price']);

    $exist = $this->db->get();
    if($exist->num_rows() > 0){
      $item = $exist->result()[0];
      $this->db->set('approved_qty', $item->approved_qty + $data['approved_qty']);
      $this->db->where('id', $item->id);
      return $this->db->update('purchasing_system_approvements');
    }else{
      return $this->db->insert('purchasing_system_approvements', $data);
    }
  }
  public function update_order_status($order_id, $status){
    $this->db->set('status', $status);
    $this->db->where('id', $order_id);
    return $this->db->update('purchasing_system_orders');
  }
  public function remove_approved_item($id){
    return $this->db->delete('purchasing_system_approvements', array('id' => $id));
  }
  public function ship_order($data){
    $this->db->insert('purchasing_system_shipments', $data);
    return $this->db->insert_id();
  }
  public function update_approvement_status($order_id, $shipment_id){
    $this->db->set('status', 'shipped');
    $this->db->set('shipment_id', $shipment_id);
    $this->db->where('order_id', $order_id);
    $this->db->where('status', 'approved');
    return $this->db->update('purchasing_system_approvements');
  }
  public function complete_order($order_id){
    $this->db->set('status', 'completed');
    $this->db->where('id', $order_id);
    return $this->db->update('purchasing_system_orders');
  }
  public function update_item_qty($id, $approved_qty){
    $this->db->select('qty');
    $this->db->from('purchasing_system_items');
    $this->db->where('id', $id);

    $result = $this->db->get();
    $qty = $result->result()[0];

    $this->db->set('qty', $qty->qty - $approved_qty);
    $this->db->where('id', $id);
    return $this->db->update('purchasing_system_items');
  }
  public function create_order($data){
    $this->db->insert('purchasing_system_orders', $data);
    return $this->db->insert_id();
  }

  public function get_current_order($company, $shop){
    $ret = array();
    $this->db->where('company', $company);
    $this->db->where('shop', $shop);
    $this->db->where('status', 'draft');
    $query = $this->db->get('purchasing_system_orders');
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }

  public function get_ordered_items($order_id){
    $ret = array();
    $this->db->select('
      purchasing_system_order_items.order_qty ordered_qty,
      purchasing_system_order_items.status status,
      purchasing_system_items.*
    ');
    $this->db->from('purchasing_system_order_items');
    $this->db->join('purchasing_system_items', 'purchasing_system_items.id = purchasing_system_order_items.item_id', 'left');
    $this->db->where('purchasing_system_order_items.order_id', $order_id);
    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }

  public function add_ordered_item($order_ref, $item_id, $order_qty){
    $this->db->select('*');
    $this->db->from('purchasing_system_orders');
    $this->db->where('order_ref', $order_ref);
    $order_id_query = $this->db->get();
    $order_id = $order_id_query->result()[0]->id;

    $this->db->from('purchasing_system_order_items');
    $this->db->where('order_id', $order_id);
    $this->db->where('item_id', $item_id);
    if($this->db->get()->num_rows() > 0){
      $this->db->set('order_qty', $order_qty);
      $this->db->where('order_id', $order_id);
      $this->db->where('item_id', $item_id);
      $this->db->update('purchasing_system_order_items');
    }else{
      $this->db->insert('purchasing_system_order_items', array(
        'order_id' => $order_id,
        'item_id' => $item_id,
        'order_qty' => $order_qty
      ));
    }
    $this->db->select('purchasing_system_order_items.order_qty ordered_qty, purchasing_system_items.*');
    $this->db->from('purchasing_system_order_items');
    $this->db->join('purchasing_system_items', 'purchasing_system_items.id = purchasing_system_order_items.item_id', 'left');
    $this->db->where('purchasing_system_order_items.order_id', $order_id);
    $query = $this->db->get();
    $ret = array();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }

  public function remove_order_detail_item($order_id, $item_id){
    $this->db->delete('purchasing_system_order_items', array('order_id' => $order_id, 'item_id' => $item_id));
    $this->db->select('purchasing_system_order_items.order_qty ordered_qty, purchasing_system_items.*');
    $this->db->from('purchasing_system_order_items');
    $this->db->join('purchasing_system_items', 'purchasing_system_items.id = purchasing_system_order_items.item_id', 'left');
    $this->db->where('purchasing_system_order_items.order_id', $order_id);
    $query = $this->db->get();
    $ret = array();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }

  public function place_order($order_id){
    $this->db->set('status', 'pending');
    $this->db->where('id', $order_id);
    return $this->db->update('purchasing_system_orders');
  }
  public function cancel_order($order_id){
    $this->db->delete('purchasing_system_orders', array('id' => $order_id));
    return $this->db->delete('purchasing_system_order_items', array('id' => $order_id));
  }

  public function get_order_history($company, $shop, $branch){
    $ret = array();
    $this->db->select('purchasing_system_orders.*, users.name, users.email');
    $this->db->from('purchasing_system_orders');
    $this->db->join('users', 'users.id = purchasing_system_orders.customer_id', 'left');
    $this->db->where('purchasing_system_orders.company', $company);
    $this->db->where('purchasing_system_orders.shop', $shop);
    $this->db->where('purchasing_system_orders.branch', $branch);
    $this->db->where('purchasing_system_orders.status <>', 'draft');
    $this->db->order_by('order_time', 'DESC');

    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function accept_item($id){
    $this->db->set('status', 'accepted');
    $this->db->where('id', $id);
    return $this->db->update('purchasing_system_approvements');
  }
}
?>
