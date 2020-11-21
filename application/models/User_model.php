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
            $this->db->set('email', $data['email']);
            $this->db->set('shop_name', $data['shop_name']);
            $this->db->set('database', $data['database']);
            $this->db->set('password', $data['password']);
            $this->db->set('role', $data['role']);
            $this->db->set('access', $data['access']);
            $this->db->set('company', $data['company']);
            $this->db->set('branch_id', $data['branch_id']);
            $this->db->where('id', $data['id']);
            $res = $this->db->update('users');
            if($res > 0){
                return 1; // User created successfully
            }else{
                return -1; // Database error
            }
        }
        public function login($data){
            $this->db->select('id, name, email, phone, address, status, access, role, database, shop_name, company, branch_id, member_since, last_login');
            $this->db->where('name', $data['name']);
            $this->db->where('password', $data['password']);
            $this->db->from('users');
            $res = $this->db->get();

            if($res->num_rows() == 0){
                return '0';
            }else{
                $this->db->set('last_login', date("Y-m-d H:i:s"));
                $this->db->where('id', $res->result()[0]->id);
                $this->db->update('users');
                $user_info = $res->result()[0];
                return $user_info;
            }
        }
        public function ps_login($data){
            $this->db->select('id, name, email, phone, address, status, access, role, database, shop_name, company, branch_id, member_since, last_login');
            $this->db->where('name', $data['name']);
            $this->db->where('password', $data['password']);
            $this->db->where('access', $data['access']);
            $this->db->from('users');
            $res = $this->db->get();

            if($res->num_rows() == 0){
                return '0';
            }else{
                $this->db->set('last_login', date("Y-m-d H:i:s"));
                $this->db->where('id', $res->result()[0]->id);
                $this->db->update('users');
                $user = $res->result()[0];
                return $user;
            }
        }
        public function is_login($data){
            $this->db->select('*');
            $this->db->where('name', $data['name']);
            $this->db->where('password', $data['password']);
            $this->db->where('access', $data['access']);
            $this->db->where('role !=', 'admin');
            $this->db->from('users');
            $res = $this->db->get();

            if($res->num_rows() == 0){
                return '0';
            }else{
                $this->db->set('last_login', date("Y-m-d H:i:s"));
                $this->db->where('id', $res->result()[0]->id);
                $this->db->update('users');
                $user = $res->result()[0];
                return $user;
            }
        }
        public function logHistory($data){

            // Update events table
            $this->db->select('*');
            $this->db->where('user_id', $data['user_id']);
            $this->db->from('events');
            $events = $this->db->get();
            if($events->num_rows() == 0){
                $login = 0;
                $page = 0;
                $export = 0;
                if($data['event_detail'] == 'login'){
                    $login ++;
                }else if($data['event_detail'] == 'page'){
                    $page ++;
                }else if($data['event_detail'] == 'export'){
                    $export ++;
                }else{

                }
                $this->db->insert('events', array(
                    'user_id' => $data['user_id'],
                    'login_counts' => $login,
                    'page_visits' => $page,
                    'export_counts' => $export
                ));
            }else{
                $login = (int)$events->result()[0]->login_counts;
                $page = (int)$events->result()[0]->page_visits;
                $export = (int)$events->result()[0]->export_counts;
                if($data['event_detail'] == 'login'){
                    $login ++;
                }else if($data['event_detail'] == 'page'){
                    $page ++;
                }else if($data['event_detail'] == 'export'){
                    $export ++;
                }else{

                }
                $update_event = array(
                    'login_counts' => $login,
                    'page_visits' => $page,
                    'export_counts' => $export
                );
                $this->db->where('user_id', $data['user_id']);
                $this->db->update('events', $update_event);
            }

            // Insert logs table
            $this->db->insert('logs', array(
                'user_id' => $data['user_id'],
                'event_description' => $data['event_description']
            ));
        }
        public function getEvents($data){
            // Get user id, member_since and last_login
            $this->db->select('*');
            $this->db->where('name', $data['user']);
            $this->db->from('users');
            $res = $this->db->get();
            $user_id = $res->result()[0]->id;
            $member_since = $res->result()[0]->member_since;
            $last_login = $res->result()[0]->last_login;
            // Get events
            $this->db->select('*');
            $this->db->where('user_id', $user_id);
            $this->db->from('events');
            $events = $this->db->get()->result();

            return array(
                'member_since' => $member_since,
                'last_login' => $last_login,
                'events'    => $events
            );
        }
        public function getLogs($data){
            $this->db->select('*');
            $this->db->where('name', $data['user']);
            $this->db->from('users');
            $user_id = $this->db->get()->result()[0]->id;

            $this->db->select('*');
            $this->db->where('user_id', $user_id);
            $this->db->where('timestamp >=', $data['start']);
            $this->db->where('timestamp <=', $data['end']);
            $this->db->from('logs');
            $logs = $this->db->get()->result();
            return $logs;
        }
        public function users(){
            $this->db->select('id, name, email, phone, address, status, access, role, database, shop_name, company, branch_id, member_since, last_login');
            return $this->db->get('users');
        }
        public function get_all_users(){
          $this->db->select('id, name, email, phone, address, status, access, role, database, shop_name, company, branch_id, member_since, last_login');
          $this->db->from('users');
          return $this->db->get();

        }
        public function get_ps_users(){
          $res = array();
          $this->db->select('id, name, email, company, branch_id');
          $this->db->where('access', 'purchasing_system');
          $this->db->from('users');
          $data = $this->db->get();
          foreach ($data->result() as $row){
              array_push($res, $row);
          }
          return $res;
        }
        public function get_ps_admin($company){
          $res = array();
          $this->db->select('id, name, email, company, branch_id');
          $this->db->where('access', 'purchasing_system');
          $this->db->where('role', 'admin');
          $this->db->where('company', $company);
          $this->db->from('users');
          $data = $this->db->get();
          foreach ($data->result() as $row){
              array_push($res, $row);
          }
          return $res;
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

        // Kitchen users
        private function kt_validate($code){
            $this->db->where('usercode', $code);
            $this->db->from('kt_users');
            return $this->db->get()->num_rows();
        }

        public function kt_create($data){
            if($this->kt_validate($data['usercode']) > 0){
                return 0; // User code exists
            }
            $res = $this->db->insert('kt_users', $data);
            if($res > 0){
                return 1; // User created successfully
            }else{
                return -1; // Database error
            }
        }
        public function kt_update($data){
            $this->db->set('username', $data['username']);
            $this->db->set('usercode', $data['usercode']);
            $this->db->set('db', $data['db']);
            $this->db->set('shop', $data['shop']);
            $this->db->set('ktkey', $data['ktkey']);
            $this->db->where('id', $data['id']);
            $res = $this->db->update('kt_users');
            if($res > 0){
                return 1; // User updated successfully
            }else{
                return -1; // Database error
            }
        }
        public function kt_users(){
            $this->db->select('id, username, usercode, db, shop, ktkey, activated, created_at, updated_at');
            return $this->db->get('kt_users');
        }
        public function kt_delete($id){
            return $this->db->delete('kt_users', array('id' => $id));
        }
    }
?>
