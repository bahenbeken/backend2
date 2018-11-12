<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class retailerOrder extends MX_Controller {

		private $container;
		private $valid = false;

		public function __construct()
		{
			parent::__construct();
			$this->load->helper('accesscontrol');
			$this->load->helper('app');
			$this->load->helper('url');
			$this->container["data"] = null;
			LoggedSystem();
		}

		public function getDataNewOrder($deliveryStatus = "-", $idDistributor = "-", $idRetailer = "-", $bookingcode = "-", $startDate = "-", $endDate = "-") {

			$sql = "SELECT d.`username` AS sales_namec, c.`name` AS distributor_name, b.`name` AS retailer_name,
			FORMAT(a.`total_amount`, 0) AS total,
			CASE a.`delivery_status`
			    WHEN 'P' THEN 'Pending'
			    WHEN 'N' THEN 'Open'
			    WHEN 'Y' THEN 'Closed'
			    WHEN 'C' THEN 'Canceled'
			END AS status_name, a.*,e.name as salestypename
			FROM orderretailtable a
			JOIN `retailtable` b ON b.`id`=a.`id_retail`
			JOIN `distributortable` c ON c.`id`=a.id_distributor
			JOIN `usertable` d ON d.`id`=a.`sales_name`
			JOIN sanitasDist.salestypetable e ON a.sales_type=e.id
			WHERE 1=1";

			if($deliveryStatus !== "-") {
				$sql.=" AND a.delivery_status='".$deliveryStatus."'";
			}

			if($idDistributor !== "-") {
				$sql.=" AND a.id_distributor='".$idDistributor."'";
			}

			if($idRetailer !== "-") {
				$sql.=" AND a.id_retail='".$idRetailer."'";
			}

			if($bookingcode !== "-") {
				$sql.=" AND a.code_booking='".$bookingcode."'";
			}

			if($startDate !== "-" and $endDate !== "-") {
				$sql.=" AND a.order_date >= '".$startDate."' AND a.order_date <= '".$endDate."'";
			}
			else if($startDate !== "-" and $endDate === "-") {
				$sql.=" AND a.order_date >= '".$startDate."'";
			}
			else if($startDate === "-" and $endDate !== "-") {
				$sql.=" AND a.order_date <= '".$endDate."'";
			}

			if($this->session->userdata("sanitasType") === "retailer" and $this->session->userdata("sanitasRetailId") > 0 ) {
				$retId = $this->session->userdata("sanitasRetailId");
				$sql.=" AND a.id_retail='".$retId."'";
			}
			else if($this->session->userdata("sanitasType") === "sales" and $this->session->userdata("sanitasSalesId") > 0 ) {
				$salesId = $this->session->userdata("sanitasSalesId");
				$sql.=" AND a.sales_name='".$salesId."'";
			}

			$query = $this->db->query($sql);
			$result = $query->result();

			$x = 0;

			if(empty($query->num_rows())){
				$responce->data[] = [];
			}

			foreach($result as $row) {
				$x++;
				$responce->data[] = array(
					$x.".",
					$row->code_booking,
					date_format(date_create($row->order_date),"d/m/Y"),
					$row->sales_namec,
					$row->salestypename,
					$row->distributor_name,
					$row->retailer_name,
					$row->total,
					$row->kode_voucher,
					$row->nota_1,
					$row->nota_2,
					$row->nota_3,
					$row->status_name,
					$row->reason,
					$row->id
				);
			}
			echo json_encode($responce);
		}
		public function getDetailNewOrder($idOrder) {

			$sql = "SELECT
				sanitasDist.`itemtable`.`name` AS item_name,
				sanitasDist.`uomtable`.`name` AS uom_name,
				sanitasRet.orderretailtranstable.*
				FROM sanitasRet.orderretailtranstable
				JOIN sanitasDist.itemtable ON sanitasDist.itemtable.id=sanitasRet.`orderretailtranstable`.`id_item`
				JOIN sanitasDist.`uomtable` ON sanitasDist.`uomtable`.`id`=sanitasRet.`orderretailtranstable`.`id_uom`
				WHERE sanitasRet.orderretailtranstable.id_order='".$idOrder."'";

			$query = $this->db->query($sql);
			return $query->result();

		}

		public function getDataUpdatedOrder($deliveryStatus = "-", $idDistributor = "-", $idRetailer = "-", $bookingcode = "-", $startDate = "-", $endDate = "-") {

			$sql = "SELECT d.`username` AS sales_namec, c.`name` AS distributor_name, b.`name` AS retailer_name,
			FORMAT(a.`total_amount`, 0) AS total,
			CASE a.`delivery_status`
			    WHEN 'P' THEN 'Pending'
			    WHEN 'N' THEN 'Open'
			    WHEN 'Y' THEN 'Closed'
			    WHEN 'C' THEN 'Canceled'
			END AS status_name, a.id as id_header, a.*,e.name as salestypename
			FROM orderretailtableupdate a
			JOIN `retailtable` b ON b.`id`=a.`id_retail`
			JOIN `usertable` d ON d.`id`=a.`sales_name`
			JOIN sanitasDist.salestypetable e ON a.sales_type=e.id
			LEFT JOIN `distributortable` c ON c.`id`=a.id_distributor
			WHERE 1=1
			";

			if($deliveryStatus !== "-") {
				$sql.=" AND a.delivery_status='".$deliveryStatus."'";
			}

			if($idDistributor !== "-") {
				$sql.=" AND a.id_distributor='".$idDistributor."'";
			}

			if($idRetailer !== "-") {
				$sql.=" AND a.id_retail='".$idRetailer."'";
			}

			if($bookingcode !== "-") {
				$sql.=" AND a.code_booking='".$bookingcode."'";
			}

			if($startDate !== "-" and $endDate !== "-") {
				$sql.=" AND a.order_date >= '".$startDate."' AND a.order_date <= '".$endDate."'";
			}
			else if($startDate !== "-" and $endDate === "-") {
				$sql.=" AND a.order_date >= '".$startDate."'";
			}
			else if($startDate === "-" and $endDate !== "-") {
				$sql.=" AND a.order_date <= '".$endDate."'";
			}

			if($this->session->userdata("sanitasType") === "retailer" and $this->session->userdata("sanitasRetailId") > 0 ) {
				$retId = $this->session->userdata("sanitasRetailId");
				$sql.=" AND a.id_retail='".$retId."'";
			}
			else if($this->session->userdata("sanitasType") === "sales" and $this->session->userdata("sanitasSalesId") > 0 ) {
				$salesId = $this->session->userdata("sanitasSalesId");
				$sql.=" AND a.sales_name='".$salesId."'";
			}

			$query = $this->db->query($sql);
			$result = $query->result();

			$x = 0;

			if(empty($query->num_rows())){
				$responce->data[] = [];
			}

			foreach($result as $row) {
				$x++;
				$responce->data[] = array(
					$x.".",
					$row->code_booking,
					date_format(date_create($row->order_date),"d/m/Y"),
					$row->sales_namec,
					$row->salestypename,
					$row->distributor_name,
					$row->retailer_name,
					$row->total,
					$row->update_date,
					$row->reason,
					$row->id_header
				);
			}
			echo json_encode($responce);
		}

		public function getDetailUpdatedOrder($idOrder) {

			$sql = "SELECT
				sanitasDist.`itemtable`.`name` AS item_name,
				sanitasDist.`uomtable`.`name` AS uom_name,
				sanitasRet.orderretailtranstableupdate.*
				FROM sanitasRet.orderretailtranstableupdate
				JOIN sanitasDist.itemtable ON sanitasDist.itemtable.id=sanitasRet.`orderretailtranstableupdate`.`id_item`
				JOIN sanitasDist.`uomtable` ON sanitasDist.`uomtable`.`id`=sanitasRet.`orderretailtranstableupdate`.`id_uom`
				WHERE sanitasRet.orderretailtranstableupdate.id_order='".$idOrder."'";
            $query = $this->db->query($sql);
			return $query->result();

		}

    public function newOrder()
    {
        $this->twig->display("grid/gridRetailerOrderNew.html", $this->container);
    }

		public function detailNewOrder($idOrder)
    {
				$this->container["detail"] = $this->getDetailNewOrder($idOrder);
        $this->twig->display("grid/gridDetailRetailerOrderNew.html", $this->container);
    }

		public function updatedOrder()
    {
        $this->twig->display("grid/gridRetailerOrderUpdated.html", $this->container);
    }

		public function detailUpdatedOrder($idOrder)
    {
				$this->container["detail"] = $this->getDetailUpdatedOrder($idOrder);
        $this->twig->display("grid/gridDetailRetailerOrderUpdated.html", $this->container);
    }

}
