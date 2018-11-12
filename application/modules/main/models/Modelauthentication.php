<?php
class modelAuthentication extends CI_Model {

    function __construct(){
        parent::__construct();
    }

	public function loginAuth($userName, $password)
	{
		$valid = false;
		//$password = md5($password);

		$query = $this->db->get_where("admintable", array("username" => $userName,"password" => $password));
		if($query->num_rows() > 0)
		{
		   $data = $query->row();
		   if($data->username == $userName and $data->password == $password)
		   {
		   		$session = array(
  					'sanitasUserId' => $data->id,
  					'sanitasUsername' => $data->username,
  					'sanitasPassword' => $data->password,
  					'sanitasType' => "admin",
  					'sanitasSalesId' => 0,
  					'sanitasRetailId' => 0,
  					'sanitasEmail' => $data->email,
  					'sanitasLogged' => TRUE
  				);

				$this->session->set_userdata($session);
				$valid = true;
		   }
		}
    else{
      $query = $this->db->get_where("usertable", array("username" => $userName,"password" => $password));
      if($query->num_rows() > 0) {
        $data = $query->row();
        if($data->username == $userName and $data->password == $password)
   		   {
          $session = array(
            'sanitasUserId' => $data->id,
            'sanitasUsername' => $data->username,
            'sanitasPassword' => $data->password,
            'sanitasType' => $data->type,
            'sanitasEmail' => $data->email,
            'sanitasLogged' => TRUE
          );

          if($data->type === "retailer") {
             $session['sanitasSalesId'] = 0;
             $session['sanitasRetailId'] = $data->retail_id;
          }
          else if($data->type === "sales") {
             $session['sanitasSalesId'] = $data->id;
             $session['sanitasRetailId'] = 0;
          }
          else {
             $session['sanitasSalesId'] = 0;
             $session['sanitasRetailId'] = 0;
          }

          $this->session->set_userdata($session);
          $valid = true;
        }
      }
    }
		return $valid;
	}

	public function gantiPassword($password)
	{
		$log = $this->session->all_userdata();
		$userId = $this->session->userdata('userId');
		$valid = false;

		$this->db->set("password", $password);
		$this->db->where("id_user", $userId);
		$valid = $this->db->update("mst_user");

		$session = array(
			 'userPassword' => $password,
		);

		$this->session->set_userdata($session);

		return $valid;
	}
}
?>
