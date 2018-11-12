<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Popup extends MX_Controller {

	private $container;
	private $valid = false;
	var $db_ref;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('accesscontrol');
		$this->load->helper('app');
		$this->load->model('modelDataMaster');
		$this->load->helper('url');
		$this->container["data"] = null;
		$this->db_ref = $this->load->database('ref', TRUE);
		LoggedSystem();
	}

	public function productItem($id = NULL)
  {
      $this->container["data"] = $this->db_ref->get("itemtable")->result();
      $this->twig->display("form/formProductItem.html", $this->container);
  }

  public function distributor($id = NULL)
  {
      $this->container["data"] = $this->db->get("distributortable")->result();
      $this->twig->display("popup/popupDistributor.html", $this->container);
  }


  public function retailer()
  {
      $this->container["data"] = $this->db->get("retailtable")->result();
      $this->twig->display("popup/popupRetailer.html", $this->container);
  }

  public function customer()
  {
      $this->container["data"] = $this->db->get("custtable")->result();
      $this->twig->display("popup/popupCustomer.html", $this->container);
  }
  
  public function sales()
  {	  
      $this->db->select('*');
	  $this->db->from('usertable');
	  //$this->db->join("tbl_specialisation", "tbl_specialisation.spec_id = tbl_doctor.spec_id",'left');
	  //$this->db->where("(district LIKE '$distct' AND place LIKE '$locat' AND spec_specialise LIKE '$sepcli')");
	  $this->db->where("(Type LIKE 'sales')");
      $this->container["data"] = $this->db->get()->result_array();
      $this->twig->display("popup/popupSales.html", $this->container);
  }

}
