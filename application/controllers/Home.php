<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
    }

	public function index(){
		if($this->session->has_userdata('user_name')){

            $this->load->view('home');
        }else{
            $this->load->view('login');
        }
	}
}
