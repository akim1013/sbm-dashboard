<?php
class Is_model extends CI_model{
  public function get_item($company, $shop){
    $ret = array();
    $this->db->select('*');
    $this->db->from('is_items');
    $this->db->where('company', $company);
    $this->db->where('shop', $shop);
    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function add_is_item($data){
    if($this->validate($data['company'], $data['shop'], $data['purchasing_item_id']) > 0){
      // update
      $this->db->set('safety_qty', $data['safety_qty']);
      $this->db->set('sp_qty', $data['sp_qty']);
      $this->db->set('primary_unit', $data['primary_unit']);
      $this->db->set('secondary_unit', $data['secondary_unit']);
      $this->db->where('purchasing_item_id', $data['purchasing_item_id']);
      $this->db->where('company', $data['company']);
      $this->db->where('shop', $data['shop']);
      $res = $this->db->update('is_items');
      if($res > 0){
        return 1;
      }else{
        return -1;
      }
    }else{
      $res = $this->db->insert('is_items', $data);
      if($res > 0){
        return 1;
      }else{
        return -1;
      }
    }
  }
  public function remove_is_item($id){
    return $this->db->delete('is_items', array('id' => $id));
  }
  public function validate($company, $shop, $purchasing_item_id){
    $this->db->where('purchasing_item_id', $purchasing_item_id);
    $this->db->where('company', $company);
    $this->db->where('shop', $shop);
    $this->db->from('is_items');
    return $this->db->get()->num_rows();
  }
  public function get_counts($branch_id){
    $sql = "
      select
        is_counts.id is_count_id,
        is_counts.status status,
        is_counts.period period,
        is_counts.timestamp timestamp,
        users.name counter_name
      from is_counts
      left join users on users.id = is_counts.counter_id
      where users.branch_id = '".$branch_id."'
      order by is_counts.timestamp desc
    ";
    $query = $this->db->query($sql);
    $res = array();
    foreach ($query->result() as $row){
      array_push($res, $row);
    }
    return $res;
  }
  public function add_count($data){
    $this->db->insert('is_counts', $data);
    $insert_id = $this->db->insert_id();
    return $insert_id;
  }
  public function is_draft($id){
    $this->db->select('status, period');
    $this->db->from('is_counts');
    $this->db->where('id', $id);
    $res = $this->db->get();
    return $res->result();
  }
  public function remove_count($id){
    $res1 = $this->db->delete('is_counts', array('id' => $id));
    $res2 = $this->db->delete('is_count_details', array('is_count_id' => $id));
    return $res1 && $res2;
  }
  public function remove_draft_detail_item($id){
    return $this->db->delete('is_count_details', array('id' => $id));
  }
  public function get_c_item($company, $shop){
    $sql = "
      select
        is_items.id is_item_id,
        is_items.safety_qty safety_qty,
        purchasing_system_items.category category,
        purchasing_system_items.inventory_id inventory_id,
        purchasing_system_items.description description,
        purchasing_system_items.vendor_description vendor_description,
        purchasing_system_items.image image,
        purchasing_system_items.price price,
        purchasing_system_items.packing_info packing_info
      from is_items
      left join purchasing_system_items on purchasing_system_items.id = is_items.purchasing_item_id
      where is_items.company = '".$company."' and is_items.shop = '".$shop."'
        and purchasing_system_items.company = '".$company."'
    ";
    $query = $this->db->query($sql);
    $res = array();
    foreach ($query->result() as $row){
      array_push($res, $row);
    }
    return $res;
  }
  public function get_draft_items($is_count_id){
    $sql = "
      select
        is_count_details.id draft_detail_id,
        is_count_details.qty_primary qty_primary,
        is_count_details.qty_secondary qty_secondary,
        is_count_details.value value,
        is_items.safety_qty safety_qty,
        purchasing_system_items.id item_id,
        purchasing_system_items.category category,
        purchasing_system_items.inventory_id inventory_id,
        purchasing_system_items.description description,
        purchasing_system_items.price price,
        purchasing_system_items.packing_info packing_info,
        purchasing_system_items.vendor_description vendor_description,
        purchasing_system_items.image image
      from is_count_details
      left join is_items on is_items.id = is_count_details.is_item_id
      left join purchasing_system_items on purchasing_system_items.id = is_items.purchasing_item_id
      where is_count_details.is_count_id = '".$is_count_id."'
    ";
    $query = $this->db->query($sql);
    $res = array();
    foreach ($query->result() as $row){
      array_push($res, $row);
    }
    return $res;
  }
  public function add_count_detail($data){
    return $this->db->insert('is_count_details', $data);
  }
  public function complete_count($id){
    $this->db->set('status', 'completed');
    $this->db->where('id', $id);
    return $this->db->update('is_counts');
  }
  public function order_status_update($id, $order_status){
    $this->db->set('order_status', $order_status);
    $this->db->where('id', $id);
    return $this->db->update('is_counts');
  }
  public function can_start_count($counter_id){
    $sql = "
      select is_counts.id count_id, is_counts.period period, is_counts.status status, is_counts.timestamp
      from is_counts
      left join users on users.branch_id = is_counts.branch_id
      where users.id = '".$counter_id."'
      order by is_counts.timestamp desc
      limit 1
    ";
    $query = $this->db->query($sql);
    $res = array();
    foreach ($query->result() as $row){
      array_push($res, $row);
    }
    return $res;
  }
  public function send_data_to_dashboard($company, $shop, $branch_id, $counter_id, $is_count_id, $timestamp, $items){
    foreach($items as $item){

      // inventory system count history

      $this->db->insert('is_count_history', array(
        'company' => $company,
        'shop' => $shop,
        'branch_id' => $branch_id,
        'counter_id' => $counter_id,
        'is_count_id' => $is_count_id,
        'item_id' => $item->item_id,
        'price' => $item->price,
        'primary_qty' => $item->qty_primary,
        'secondary_qty' => $item->qty_secondary,
        'value' => $item->value,
        'timestamp' => $timestamp
      ));

      // inventory system items stock update
      $this->db->set('stock_qty_primary', $item->qty_primary);
      $this->db->set('stock_qty_secondary', $item->qty_secondary);
      $this->db->where('company', $company);
      $this->db->where('shop', $shop);
      $this->db->where('branch_id', $branch_id);
      $this->db->where('purchasing_item_id', $item->item_id);
      $this->db->update('is_items');

      // inventory system items stock update history

      $this->db->insert('is_stock_history', array(
        'company' => $company,
        'shop' => $shop,
        'branch_id' => $branch_id,
        'customer_id' => $counter_id,
        'purchasing_item_id' => $item->item_id,
        'primary_qty_change' => $item->qty_primary,
        'secondary_qty_change' => $item->qty_secondary,
        'platform' => 'Inventory System',
        'description' => 'Monthly count'
      ));
    }

    return 1;
  }
  public function get_inventory_history($company, $branch_id){
    $sql = "
      select
        users.name counter_name,
        users.email counter_email,
        purchasing_system_items.category category,
        purchasing_system_items.inventory_id inventory_id,
        purchasing_system_items.description description,
        purchasing_system_items.vendor_description vendor_description,
        purchasing_system_items.packing_info packing_info,
        is_history.price price,
        is_history.value value,
        is_history.primary_qty primary_qty,
        is_history.secondary_qty secondary_qty,
        is_history.timestamp timestamp
      from is_items
      left join purchasing_system_items on purchasing_system_items.id = is_items.purchasing_item_id
      left join is_history on purchasing_system_items.id = is_history.item_id
      left join users on users.id = is_history.counter_id
      order by is_history.timestamp desc
    ";
    $query = $this->db->query($sql);
    $res = array();
    foreach ($query->result() as $row){
      array_push($res, $row);
    }
    return $res;
  }
}
?>
