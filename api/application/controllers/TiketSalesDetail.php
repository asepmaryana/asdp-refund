<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';

class TiketSalesDetail extends REST_Controller 
{
	public function __construct() {
        parent::__construct();
        $this->load->model('TiketSalesDetailModel');
    }
	
    public function sum_get(){
        $tikets = $this->uri->segment(3);
        $rows   = $this->TiketSalesDetailModel->get_by_tikets($tikets)->result();
        $row    = ['total'=>0, 'refund'=>0];
        foreach ($rows as $r) {
            $row['total']   += intval($r->tarif);
            $row['refund']  += intval($r->tarif) * 0.75;
        }
        $this->response($row, REST_Controller::HTTP_OK);
    }
}
?>