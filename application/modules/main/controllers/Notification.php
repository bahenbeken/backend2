<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends MX_Controller {

	private $container;
	private $valid = false;

	public function __construct()
	{
        parent::__construct();
        $this->load->helper('url');
		$this->load->helper('sms');
		$this->load->model('modelUtility');
		$this->container["content"] = NULL;
	}

	public function sendSms()
	{
        $apikey      = 'a33ef2d9a49dc7991eb60c34f2585759'; // api key 
        $ipserver    = 'http://45.76.156.114'; // url server sms 
        $callbackurl = ''; // url callback get status sms 
        //$callbackurl = 'http://your_url_for_get_auto_update_status_sms'; // url callback get status sms 

        $setSMS = $this->modelUtility->setSMS();
       
        /* send sms  */
        $senddata = array(
            'apikey' => $apikey,  
            'callbackurl' => $callbackurl, 
            'datapacket'=>array()
        );

        $dataSend = $this->db->get_where("sms_outbox", array("send_status" => "new"))->result();
        $arrId = array();
        foreach ($dataSend as $data) {
            if(!empty($data->destination)) {
                $arrId[] = $data->id;
                array_push(
                    $senddata['datapacket'],array(
                        'number' => trim($data->destination),
                        'message' => str_replace("|","\n", $data->message),
                        //'message' => urlencode(stripslashes(utf8_encode($data->message))),
                        'sendingdatetime' => $data->date_time
                    )
                );
                
            }                          
        }
        
        $sms = new sms_class_reguler_json();
        $sms->setIp($ipserver);
        $sms->setData($senddata);
        $responjson = $sms->send();
        $output = (array) json_decode($responjson);
        
        $x = -1;
        foreach ($output["sending_respon"][0]->datapacket as $dt) {
            $x++;
            $obj = $dt->packet;
            $number = $obj->number;            
            $sendingStatus = $obj->sendingstatus;
            $sendingInfo = $obj->sendingstatustext;
            $outboxId = $arrId[$x];

            $sent = "sent";
            if($sendingStatus > 10) {
                $sent = "failed";
            }
           
            $this->db->set("send_status", $sent);  
            $this->db->set("sending_info", $sendingInfo );  
            $this->db->where("id", $outboxId);
            $this->db->update("sms_outbox");  
            
        }
        $response = array("status" => "success");
        echo json_encode($response);

	}


}
