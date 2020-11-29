<?php
class Is_model extends CI_model{
  public function get_item($branch_id){
    $ret = array();
    $this->db->select('*');
    $this->db->from('is_items');
    $this->db->where('branch_id', $branch_id);
    $query = $this->db->get();
    foreach ($query->result() as $row){
      array_push($ret, $row);
    }
    return $ret;
  }
  public function add_is_item($data){
    if($this->validate($data['branch_id'], $data['inventory_id']) > 0){
      // update
      $this->db->set('safety_qty', $data['safety_qty']);
      $this->db->set('sp_qty', $data['sp_qty']);
      $this->db->set('primary_unit', $data['primary_unit']);
      $this->db->set('secondary_unit', $data['secondary_unit']);
      $this->db->where('inventory_id', $data['inventory_id']);
      $this->db->where('branch_id', $data['branch_id']);
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
  public function validate($branch_id, $inventory_id){
    $this->db->where('inventory_id', $inventory_id);
    $this->db->where('branch_id', $branch_id);
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
  public function get_c_item($branch_id){
    $sql = "
      select
        is_items.id is_item_id,
        is_items.safety_qty safety_qty,
        ps_items.category category,
        ps_items.inventory_id inventory_id,
        ps_items.description description,
        ps_items.vendor_description vendor_description,
        ps_items.image image,
        ps_items.price price,
        ps_items.packing_info packing_info
      from is_items
      left join ps_items on ps_items.inventory_id = is_items.inventory_id
      where is_items.branch_id = '".$branch_id."'
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
        is_items.safety_qty safety_qty,
        ps_items.id item_id,
        ps_items.category category,
        ps_items.inventory_id inventory_id,
        ps_items.description description,
        ps_items.price price,
        ps_items.vendor_description vendor_description,
        ps_items.image image
      from is_count_details
      left join is_items on is_items.id = is_count_details.is_item_id
      left join ps_items on ps_items.inventory_id = is_items.inventory_id
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
}
?>
