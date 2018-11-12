<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class reportpenjualan extends MX_Controller {

		private $container;
		private $valid = false;
		var $helper;
		
		public function __construct()
		{
			parent::__construct();
			$this->load->helper('accesscontrol');
			$this->load->helper('app');
			$this->load->model('modelDataMaster');
			$this->load->helper('url');
			$this->container["data"] = null;
			LoggedSystem();
		}

	    public function penjualanCustomerDisplay($fromDate, $toDate, $isDetail = "0", $idRetailer = NULL, $idCustomer = NULL, $idsales = NULL,$deliveryStatus = "-")
		{   
			$this->container["from_date"] = $fromDate;
			$this->container["to_date"] = $toDate;
			$this->container["is_detail"] = $isDetail;
		
			$sql = "SELECT d.`username` AS sales_namec, c.`name` AS customer_name, b.`name` AS retailer_name,
			FORMAT(a.`total_amount`, 0) AS total,
			CASE a.`delivery_status`
				WHEN 'P' THEN 'Pending'
				WHEN 'N' THEN 'Open'
				WHEN 'Y' THEN 'Closed'
				WHEN 'C' THEN 'Canceled'
			END AS status_name,a.*,e.name as salestypename,f.name as itemname,
			f.price as pricef,f.pricekrt,g.name as UOM,h.qty,h.discount,h.total_amount as total_amounth,
			h.price as priceh
			FROM ordertable a
			JOIN `retailtable` b ON b.`id`=a.`id_retail`
			JOIN `custtable` c ON c.`id`=a.`id_customer`
			JOIN `usertable` d ON d.`id`=a.`sales_name` 
			JOIN sanitasDist.salestypetable e ON a.sales_type=e.id
			JOIN ordertranstable h ON a.id=h.id_order 
			JOIN sanitasDist.itemtable f ON f.id=h.id_item
			JOIN sanitasDist.uomtable g ON g.id=h.id_uom where 1=1";

			$sql.=" AND a.delivery_status='Y'";

			if($idCustomer !== "-") {
				$sql.=" AND a.id_customer='".$idCustomer."'";
			}

			if($idRetailer !== "-") {
				$sql.=" AND a.id_retail='".$idRetailer."'";
			}

			if($idsales !== "-") {
				$sql.=" AND d.id='".$idsales."'";
			}

			if($fromDate !== "-" and $toDate !== "-") {
				$sql.=" AND a.order_date >= '".$fromDate."' AND a.order_date <= '".$toDate."'";
			}
			else if($fromDate !== "-" and $toDate === "-") {
				$sql.=" AND a.order_date >= '".$fromDate."'";
			}
			else if($fromDate === "-" and $toDate !== "-") {
				$sql.=" AND a.order_date <= '".$toDates."'";
			}

			if($this->session->userdata("sanitasType") === "retailer" and $this->session->userdata("sanitasRetailId") > 0 ) {
				$retId = $this->session->userdata("sanitasRetailId");
				$sql.=" AND a.id_retail='".$retId."'";
			}
			else if($this->session->userdata("sanitasType") === "sales" and $this->session->userdata("sanitasSalesId") > 0 ) {
				$salesId = $this->session->userdata("sanitasSalesId");
				$sql.=" AND a.sales_name='".$salesId."'";
			}
			
			$sql.=" order by c.name asc ";
							
			$query = $this->db->query($sql);
			$data = $query->result();

			$this->container["data"] = $data;
			$this->twig->display("report/reportPenjualanCustDisplayDetail.html", $this->container);
		} 
		
		public function penjualanRetailerDisplay($fromDate, $toDate, $isDetail = "0", $idRetailer = NULL, $idCustomer = NULL, $idsales = NULL,$deliveryStatus = "-")
		{   
			$this->container["from_date"] = $fromDate;
			$this->container["to_date"] = $toDate;
			$this->container["is_detail"] = $isDetail;

			//if($isDetail == "1") {		
				$sql = "SELECT d.username AS sales_namec, c.name AS distributor_name, b.name AS retailer_name,
				FORMAT(a.total_amount, 0) AS total,a.*,f.name as itemname,
				f.price as pricef,f.pricekrt,g.name as UOM,h.qty,h.discount,h.total_amount as total_amounth,
				h.price as priceh
				FROM orderretailtable a
				JOIN orderretailtranstable h ON a.id=h.id_order 
				JOIN retailtable b ON b.id=a.id_retail
				JOIN distributortable c ON c.id=a.id_distributor
				JOIN usertable d ON d.id=a.sales_name 
				JOIN sanitasDist.itemtable f ON f.id=h.id_item
				JOIN sanitasDist.uomtable g ON g.id=h.id_uom where 1=1";

				//$sql.=" AND a.delivery_status='Y'";

				if($idCustomer !== "-") {
					$sql.=" AND a.id_customer='".$idCustomer."'";
				}

				if($idRetailer !== "-") {
					$sql.=" AND a.id_retail='".$idRetailer."'";
				}

				if($idsales !== "-") {
					$sql.=" AND d.id='".$idsales."'";
				}

				if($fromDate !== "-" and $toDate !== "-") {
					$sql.=" AND a.order_date >= '".$fromDate."' AND a.order_date <= '".$toDate."'";
				}
				else if($fromDate !== "-" and $toDate === "-") {
					$sql.=" AND a.order_date >= '".$fromDate."'";
				}
				else if($fromDate === "-" and $toDate !== "-") {
					$sql.=" AND a.order_date <= '".$toDates."'";
				}

				if($this->session->userdata("sanitasType") === "retailer" and $this->session->userdata("sanitasRetailId") > 0 ) {
					$retId = $this->session->userdata("sanitasRetailId");
					$sql.=" AND a.id_retail='".$retId."'";
				}
				else if($this->session->userdata("sanitasType") === "sales" and $this->session->userdata("sanitasSalesId") > 0 ) {
					$salesId = $this->session->userdata("sanitasSalesId");
					$sql.=" AND a.sales_name='".$salesId."'";
				}
				
				$sql.=" order by b.name asc ";
				
				$query = $this->db->query($sql);
				$data = $query->result();

				$this->container["data"] = $data;
				$this->twig->display("report/reportPenjualanRetDisplayDetail.html", $this->container);
			//}
			//else{
				//$data = $this->modelReport->penjualanRekap($fromDate, $toDate, $isDetail, $idDistributor, $idCustomer, $idSales);
				//$this->container["data"] = $data;
				//$this->twig->display("report/reportPenjualanDisplayRekap.html", $this->container);
			//}
		} 
		
		public function Customer() {
			$this->twig->display("report/reportPenjualanCustFilter.html", $this->container);
		}
		
		public function retailer() {
			$this->twig->display("report/reportPenjualanRetFilter.html", $this->container);
		}
}
