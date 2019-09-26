<?php
    class User_model extends CI_model{
        public function create($data){
            if($this->validate($data['name']) > 0){
                return 0; // User name exists
            }
            $res = $this->db->insert('users', $data);
            if($res > 0){
                return 1; // User created successfully
            }else{
                return -1; // Database error
            }
        }
        public function login($data){
            $this->db->where('name', $data['name']);
            $this->db->where('password', $data['password']);
            $this->db->from('users');
            $res = $this->db->get()->num_rows();
            return $res;
        }
        private function validate($name){
            $this->db->where('name', $name);
            $this->db->from('users');
            return $this->db->get()->num_rows();
        }
    }
?>
