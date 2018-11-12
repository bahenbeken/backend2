<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Referensi extends MX_Controller {

		private $container;
		private $valid = false;
		var $db_ref;

		public function __construct()
		{
			parent::__construct();
			$this->load->helper('accesscontrol');
			$this->load->helper('app');
			$this->load->model('modelReferensi');
			$this->load->helper('url');
			$this->container["data"] = null;
			$this->db_ref = $this->load->database('ref', TRUE);
			LoggedSystem();
		}

    public function uom($id = NULL)
    {
        if($_POST) {
            $args = (object) $this->input->post();
            $valid = $this->modelReferensi->saveUom($args);

						if($valid) {
                $this->session->set_flashdata(array("type" => "success", "notify" => "Data submition success!"));
            }
            else{
                $this->session->set_flashdata(array("type" => "warn", "notify" => "Data submition failed to save! please check the paramaters"));
            }

            redirect("/main/referensi/uom");
        }

        if(!empty($id)) {
            $edit = $this->db_ref->get_where("uomtable", array("id" => $id))->row();
            $this->container["edit"] = $edit;
        }

        $this->container["data"] = $this->db_ref->get("uomtable")->result();
        $this->twig->display("form/formUom.html", $this->container);
    }

    public function deleteUom($id)
    {
				$this->db_ref->where("id", $id);
				$valid = $this->db_ref->delete("uomtable");

        if($valid) {
            $this->session->set_flashdata(array("type" => "success", "notify" => "Data deleted from the system!"));
        }
        else{
            $this->session->set_flashdata(array("type" => "warn", "notify" => "Data unabled to delete, please check the paramaters or contact the administrator!"));

        }
        redirect("/main/referensi/uom");
    }

		public function salesType($id = NULL)
    {
        if($_POST) {
            $args = (object) $this->input->post();
            $valid = $this->modelReferensi->saveSalesType($args);

						if($valid) {
                $this->session->set_flashdata(array("type" => "success", "notify" => "Data submition success!"));
            }
            else{
                $this->session->set_flashdata(array("type" => "warn", "notify" => "Data submition failed to save! please check the paramaters"));
            }

            redirect("/main/referensi/salestype");
        }

        if(!empty($id)) {
            $edit = $this->db_ref->get_where("salestypetable", array("id" => $id))->row();
            $this->container["edit"] = $edit;
        }

        $this->container["data"] = $this->db_ref->get("salestypetable")->result();
        $this->twig->display("form/formSalesType.html", $this->container);
    }

    public function deleteSalesType($id)
    {
				$this->db_ref->where("id", $id);
				$valid = $this->db_ref->delete("salestypetable");

        if($valid) {
            $this->session->set_flashdata(array("type" => "success", "notify" => "Data deleted from the system!"));
        }
        else{
            $this->session->set_flashdata(array("type" => "warn", "notify" => "Data unabled to delete, please check the paramaters or contact the administrator!"));

        }
        redirect("/main/referensi/uom");
    }

}
