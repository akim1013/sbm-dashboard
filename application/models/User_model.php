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
        public function update($data){
            $this->db->set('name', $data['name']);
            $this->db->set('shop_name', $data['shop_name']);
            $this->db->where('id', $data['id']);
            $res = $this->db->update('users');
            if($res > 0){
                return 1; // User created successfully
            }else{
                return -1; // Database error
            }
        }
        public function login($data){
            $this->db->select('id, name, email, database, shop_name, member_since, last_login');
            $this->db->where('name', $data['name']);
            $this->db->where('password', $data['password']);
            $this->db->from('users');
            $res = $this->db->get();
            if($res->num_rows() == 0){
                return '0';
            }else{
                $shop_name = $res->result()[0];
                return $shop_name;
            }
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
        public function save_presence($data){
            $res = $this->db->insert('presence', $data);
            if($res > 0){
                return 1; // User created successfully
            }else{
                return -1; // Database error
            }
        }
        public function get_presence(){
            $this->db->select('id, manager, date, operators');
            $data = $this->db->get('presence');
            $ret = array();
            foreach ($data->result() as $row){
                array_push($ret, $row);
            }
            return $ret;
        }
        public function delete_presence($id){
            return $this->db->delete('presence', array('id' => $id));
        }
    }
?>
