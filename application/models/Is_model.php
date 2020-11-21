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
  public function validate($branch_id, $inventory_id){
    $this->db->where('inventory_id', $inventory_id);
    $this->db->where('branch_id', $branch_id);
    $this->db->from('is_items');
    return $this->db->get()->num_rows();
  }
}
?>
