<?php
class modelUtility extends CI_Model {

	var $valid = false;

	public function __construct(){
      parent::__construct();
      $this->load->helper('accesscontrol');
      LoggedSystem();
	}

	public function saveAdmin($args){

			$this->db->set("password", $args->password);
			$this->db->set("email", $args->email);

			if(!empty($args->id)) {
				$action = "update";
				$this->db->where("id", $args->id);
				$this->valid = $this->db->update("admintable");
			}
			else {
				$this->db->set("username", $args->username);
				$this->valid = $this->db->insert("admintable");
			}

		return $this->valid;
	}

	public function saveUser($args){

			$this->db->set("password", $args->password);
			$this->db->set("email", $args->email);
			$this->db->set("handphone", $args->handphone);
			$this->db->set("id_region", $args->id_region);
			$this->db->set("type", $args->type);

			if($args->type === "retailer") {
				$this->db->set("retail_id", $args->id_retailer);
			}

			if(!empty($args->id)) {
				$action = "update";
				$this->db->where("id", $args->id);
				$this->valid = $this->db->update("usertable");
			}
			else {
				$this->db->set("username", $args->username);
				$this->valid = $this->db->insert("usertable");
			}

		return $this->valid;
	}


	public function changePassword($args){
		$table = "usertable";
		if($this->session->userdata("sanitasType") === "admin") {
			$table = "admintable";
		}

		$this->db->set("password", $args->pass1);
		$this->db->where("id", $this->session->userdata("sanitasUserId"));
		$this->valid = $this->db->update($table);

		if($this->valid) {
			$session['sanitasPassword'] = $args->pass1;
			$this->session->set_userdata($session);
		}

		return $this->valid;
    }
    
    public function setSMS() {
        $sqlSelect = "SELECT a.`id`, 'retailer_order' AS order_from, 'orderretailtable' AS table_order, b.`telephone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `orderretailtable` a 
        JOIN `retailtable` b ON b.`id`=a.`id_retail` 
        JOIN `orderretailtranstable` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'retailer_order' AS order_from, 'orderretailtable' AS table_order, b.`telephone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `orderretailtable` a 
        JOIN `distributortable` b ON b.`id`=a.`id_distributor`
        JOIN `orderretailtranstable` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'retailer_order' AS order_from, 'orderretailtable' AS table_order, z.`handphone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `orderretailtable` a 
        JOIN `distributortable` b ON b.`id`=a.`id_distributor`
        JOIN `orderretailtranstable` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        LEFT JOIN `usertable` z ON z.id_region=b.id_region
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'retailer_order_update' AS order_from, 'orderretailtableupdate' AS table_order, b.`telephone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `orderretailtableupdate` a 
        JOIN `retailtable` b ON b.`id`=a.`id_retail` 
        JOIN `orderretailtranstableupdate` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'retailer_order_update' AS order_from, 'orderretailtableupdate' AS table_order, b.`telephone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `orderretailtableupdate` a 
        JOIN `distributortable` b ON b.`id`=a.`id_distributor`
        JOIN `orderretailtranstableupdate` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'retailer_order_update' AS order_from, 'orderretailtableupdate' AS table_order, z.`handphone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `orderretailtableupdate` a 
        JOIN `distributortable` b ON b.`id`=a.`id_distributor`
        JOIN `orderretailtranstableupdate` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        LEFT JOIN `usertable` z ON z.id_region=b.id_region
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'customer_order' AS order_from, 'ordertable' AS table_order, b.`telephone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `ordertable` a 
        JOIN `custtable` b ON b.id=a.id_customer
        JOIN `ordertranstable` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'customer_order' AS order_from, 'ordertable' AS table_order, b.`telephone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `ordertable` a 
        JOIN `retailtable` b ON b.id=a.id_retail
        JOIN `ordertranstable` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'customer_order' AS order_from, 'ordertable' AS table_order, z.`handphone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `ordertable` a 
        JOIN `retailtable` b ON b.`id`=a.`id_retail`
        JOIN `ordertranstable` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        LEFT JOIN `usertable` z ON z.id_region=b.id_region
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'customer_order_update' AS order_from, 'ordertableupdate' AS table_order, b.`telephone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `ordertableupdate` a 
        JOIN `custtable` b ON b.id=a.id_customer
        JOIN `ordertranstableupdate` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'customer_order_update' AS order_from, 'ordertableupdate' AS table_order, b.`telephone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `ordertableupdate` a 
        JOIN `retailtable` b ON b.id=a.id_retail
        JOIN `ordertranstableupdate` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order
        
        UNION ALL
        
        SELECT a.`id`, 'customer_order_update' AS order_from, 'ordertableupdate' AS table_order, z.`handphone` AS detination, 'system' AS `sent_from`,
        CONCAT('Kode Booking: ', a.code_booking, '|', GROUP_CONCAT(CONCAT(d.`name`,'|qty: ', c.qty,'', e.name),'||'))
         AS message
        FROM `ordertableupdate` a 
        JOIN `retailtable` b ON b.`id`=a.`id_retail`
        JOIN `ordertranstableupdate` c ON c.`id_order`=a.`id`
        JOIN `sanitasDist`.`itemtable` d ON d.`id`=c.id_item
        JOIN `sanitasDist`.`uomtable` e ON e.`id`=c.id_uom
        LEFT JOIN `usertable` z ON z.id_region=b.id_region
        WHERE a.`sms_flag`='new'
        GROUP BY c.id_order";

        $sqlInsert = "INSERT INTO `sms_outbox` (`trans_id`, `order_from`, `table_order`, `destination`, `sent_from`, `message`)
                      $sqlSelect";

        $insert = $this->db->query($sqlInsert);
        $select = $this->db->query($sqlSelect)->result();
        foreach ($select as $data) {            
            $this->db->set("sms_flag", "sent");
            $this->db->where("id", $data->id);
            $this->db->update($data->table_order);
        }

        return true;
    }



}
?>
