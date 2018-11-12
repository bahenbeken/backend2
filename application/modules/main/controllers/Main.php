<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MX_Controller {

	private $container;
	private $valid = false;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('accesscontrol');
		$this->load->helper('url');
		$this->load->library('zip');
		$this->load->helper('download');
		$this->load->helper('email');
		$this->container["content"] = NULL;
		LoggedSystem();
	}

	public function index()
	{

		$this->container["content"] = NULL;
		$this->twig->display("home.html", $this->container);
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect(base_url());
	}

	

}
