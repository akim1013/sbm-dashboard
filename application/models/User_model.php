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
        public function users(){
            $this->db->select('id, name, email, database, shop_name');
            return $this->db->get('users');
        }
        private function validate($name){
            $this->db->where('name', $name);
            $this->db->from('users');
            return $this->db->get()->num_rows();
        }
        public function delete($id){
            $this->db->delete('users', array('id' => $id));
        }
        public function get_DB($name){
            $data = '';
            $this->db->select('database');
            $this->db->where('name', $name);
            $this->db->from('users');
            $data = $this->db->get();
            foreach ($data->result() as $row){
                return $row->database;
            }
        }
    }
?>
