<?php
  class Kitchen_model extends CI_model{

    public function kt_login($data){
      $this->db->select('*');
      $this->db->where('name', $data['name']);
      $this->db->where('email', $data['email']);
      $this->db->where('access', 'kitchen');
      $this->db->where('role', 'customer');
      $this->db->from('users');
      $res = $this->db->get();

      if($res->num_rows() == 0){
          return '0';
      }else{
          $this->db->set('last_login', date("Y-m-d H:i:s"));
          $this->db->where('id', $res->result()[0]->id);
          $this->db->update('users');
          $user_info = $res->result()[0];
          return $res->result();
      }
    }
    public function log_history($data){
      $res = $this->db->insert('kt_histories', $data);
      return $res;
    }

    public function get_kitchens($data){
      $this->db->distinct();
      $this->db->select('name');
      $this->db->from('users');
      $this->db->where('company', $data);
      $this->db->where('access', 'kitchen');
      $this->db->where('role', 'customer');
      $query = $this->db->get();

      return $query->result();
    }

    public function get_item_history($data){
      $ret = array();
      $sql = "
        SELECT item_id, item_code, item_name, SUM(amount) amount, type
        FROM kt_histories
        WHERE shop_id = '" . $data['shop_id'] . "' AND timestamp BETWEEN '" . $data['from'] . ' 00:00:00' . "' AND '" . $data['to'] . ' 23:59:59' . "'
        GROUP BY item_id, type
        ORDER BY item_id
      ";
      $query = $this->db->query($sql);
      foreach ($query->result() as $row){
          array_push($ret, $row);
      }
      return $ret;
    }
    public function get_history($data){
      $ret = array();
      $sql = "
        SELECT *
        FROM kt_histories
        WHERE shop_id = '" . $data['shop_id'] . "' AND timestamp BETWEEN '" . $data['from'] . ' 00:00:00' . "' AND '" . $data['to'] . ' 23:59:59' . "'
      ";
      $query = $this->db->query($sql);
      foreach ($query->result() as $row){
          array_push($ret, $row);
      }
      return $ret;
    }
    public function get_amount_history($data){
      $d = '';
      switch($data['d']){
        case 'hour':
          $d = "DATE_FORMAT(timestamp, '%H')";
          break;
        case 'day':
          $d = "DATE_FORMAT(timestamp, '%d')";
          break;
        case 'month':
          $d = "DATE_FORMAT(timestamp, '%m')";
          break;
        case 'year':
          $d = "DATE_FORMAT(timestamp, %YY'YY')";
          break;
        default:
          $d = "DATE_FORMAT(timestamp, '%d')";
          break;
      }

      $ret = array();
      $sql = "
        SELECT SUM(amount) amount, type, ".$d." d
        FROM kt_histories
        WHERE shop_id = '" . $data['shop_id'] . "' AND timestamp BETWEEN '" . $data['from'] . ' 00:00:00' . "' AND '" . $data['to'] . ' 23:59:59' . "'
        GROUP BY ".$d.", type
        ORDER BY ".$d."
      ";
      $query = $this->db->query($sql);
      foreach ($query->result() as $row){
          array_push($ret, $row);
      }
      return $ret;
    }

    public function kitchen_get_purchasing_item($data){
      $ret = array();
      $this->db->select('*');
      $this->db->from('purchasing_system_items');
      $this->db->where('company', $data['company']);
      $this->db->where('inventory_id', $data['inventory_id']);
      $query = $this->db->get();
      foreach ($query->result() as $row){
        array_push($ret, $row);
      }
      return $ret;
    }

    public function kitchen_item_use($data){
      return $this->db->insert('is_stock_history', $data);
    }
  }
?>
