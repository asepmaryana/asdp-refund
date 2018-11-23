<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';

class TiketRefundDetail extends REST_Controller 
{
	public function __construct() {
        parent::__construct();
        $this->load->model('TiketRefundDetailModel');
    }
	
    public function info_get(){
        $id_tiket_refund_detail = $this->uri->segment(3);
        $row    = $this->TiketRefundDetailModel->get_by_id($id_tiket_refund_detail)->row();
        $row->tarif = number_format ($row->tarif, 0, "" , ".");
        $row->refund = number_format ($row->refund, 0, "" , ".");
        $this->response($row, REST_Controller::HTTP_OK);
    }
    
    public function find_get(){
        $tiket_refund_id = $this->uri->segment(3);
        $row    = $this->TiketRefundDetailModel->get_by_tiket_refund($tiket_refund_id)->result();
        $this->response($row, REST_Controller::HTTP_OK);
    }
    
    public function update_post() {
        $id_tiket_refund_detail    = $this->uri->segment(3);
        $values = json_decode(file_get_contents('php://input'), true);
        $data   = ['id_status_refund'=>$values['id_status_refund']];
        if(isset($values['catatan']) && $values['catatan'] != '') $data['catatan'] = $values['catatan'];
        
        if($this->TiketRefundDetailModel->update($id_tiket_refund_detail, $data))
            $this->response(['success'=>true,'message'=>'Update status refund berhasil.', 'data'=>$values], REST_Controller::HTTP_OK);
        else 
            $this->response(['success'=>false,'message'=>'Update status refund gagal !', 'data'=>$values], REST_Controller::HTTP_BAD_REQUEST);
    }
}
?>