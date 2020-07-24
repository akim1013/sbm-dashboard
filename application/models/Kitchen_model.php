<?php
    class Kitchen_model extends CI_model{
        public function log_history($data){
            $res = $this->db->insert('kt_histories', $data);
            return $res;
        }
    }
?>
