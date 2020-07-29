<?php
    class Kitchen_model extends CI_model{
        public function log_history($data){
            $res = $this->db->insert('kt_histories', $data);
            return $res;
        }

        public function get_history($data){
            $ret = array();
            $sql = "
                SELECT
                	kt.item_id,
                    kt.item_name,
                    IFNULL(kt_cooked.qty, 0) cooked_qty,
                    IFNULL(kt_disposed.qty, 0) disposed_qty,
                    DATE_FORMAT(kt.timestamp, '%Y-%m-%d') d
                FROM kt_histories kt
                LEFT JOIN (
                    SELECT  item_id,
                            SUM(qty) qty,
                            DATE_FORMAT(timestamp, '%Y-%m-%d')
                    FROM kt_histories
                    WHERE type = 'cook'
                    GROUP BY item_id, DATE_FORMAT(timestamp, '%Y-%m-%d')
                ) kt_cooked ON kt.item_id = kt_cooked.item_id
                LEFT JOIN (
                    SELECT  item_id,
                            SUM(qty) qty,
                            DATE_FORMAT(timestamp, '%Y-%m-%d')
                    FROM kt_histories
                    WHERE type = 'dispose'
                    GROUP BY item_id, DATE_FORMAT(timestamp, '%Y-%m-%d')
                ) kt_disposed ON kt.item_id = kt_disposed.item_id
                WHERE shop_id = 1 AND timestamp BETWEEN '" . $data['start'] . "' AND '" . $data['end'] . "'
                GROUP BY kt.item_id, DATE_FORMAT(kt.timestamp, '%Y-%m-%d')
                ORDER BY DATE_FORMAT(kt.timestamp, '%Y-%m-%d')
            ";
            $query = $this->db->query($sql);
            foreach ($query->result() as $row){
                array_push($ret, $row);
            }
            return $ret;
        }
    }
?>
