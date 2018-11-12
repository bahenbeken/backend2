<?php
class modelReferensi extends CI_Model {

	var $valid = false;
	var $db_ref;

	public function __construct(){
        parent::__construct();
        $this->load->helper('accesscontrol');
				$this->db_ref = $this->load->database('ref', TRUE);
        LoggedSystem();
	}

    public function saveUom($args)
    {
        $this->db_ref->set("name", $args->name);
        $this->db_ref->set("symbol", $args->symbol);

        if(!empty($args->id)) {
            $this->db_ref->where("id", $args->id);
            $this->valid = $this->db_ref->update("uomtable");
        }
        else{
            $this->valid = $this->db_ref->insert("uomtable");
        }

        return $this->valid;
    }

		public function saveSalesType($args)
    {
        $this->db_ref->set("name", $args->name);

        if(!empty($args->id)) {
            $this->db_ref->where("id", $args->id);
            $this->valid = $this->db_ref->update("salestypetable");
        }
        else{
            $this->valid = $this->db_ref->insert("salestypetable");
        }

        return $this->valid;
    }
}
?>
