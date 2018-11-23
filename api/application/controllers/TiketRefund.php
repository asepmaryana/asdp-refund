<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';

class TiketRefund extends REST_Controller 
{
	public function __construct() {
        parent::__construct();
        $this->load->model('TiketRefundModel');
        $this->load->model('TiketRefundDetailModel');
        $this->load->model('TiketSalesRefundModel');
        $this->load->model('TiketSalesDetailModel');
        $this->load->model('StatusRefundModel');
        $this->load->model('UsersModel');
        
    }
	
    public function get_kode_refund($length = 5) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function cek_get() {
        $kode  = $this->uri->segment(3);
        $rs    = $this->TiketRefundModel->get_by_kode(trim($kode));
        if($rs->num_rows() > 0) {
            $row    = $rs->row();
            $row->tgl_penjualan = date('d-m-Y', strtotime($row->tgl_penjualan));
            $rsd    = $this->TiketRefundModel->count_total_refund(['id_trx_tiket_refund'=>$row->id_trx_tiket_refund]);
            if($rsd->num_rows() > 0) {
                $sum    = $rsd->row();
                $row->total     = $sum->total;
                $row->nominal   = $sum->nominal;
            }
            else {
                $row->total     = 0;
                $row->nominal   = 0;
            }
            $rsd->free_result();
            $row->details   = $this->TiketRefundDetailModel->get_detail_by_refund($row->id_trx_tiket_refund)->result();
            if(count($row->details) == 0) $this->set_response(['success'=>false, 'message'=>'Tidak ada tiket yang refund !', 'data'=>$row], REST_Controller::HTTP_OK);
            else $this->set_response(['success'=>true, 'message'=>'Tiket Ditemukan.', 'data'=>$row], REST_Controller::HTTP_OK);
        }
        else $this->set_response(['success'=>false, 'message'=>'Tiket Tidak Ditemukan !'], REST_Controller::HTTP_NOT_FOUND);
        $rs->free_result();
    }
    
    public function cetak_get() {
        $kode  = $this->uri->segment(3);
        $rs    = $this->TiketRefundModel->get_by_kode(trim($kode));
        if($rs->num_rows() > 0) {
            $row    = $rs->row();
            $row->tgl_penjualan = date('d-m-Y', strtotime($row->tgl_penjualan));
            $rsd    = $this->TiketRefundModel->count_total_refund(['id_trx_tiket_refund'=>$row->id_trx_tiket_refund, 'id_status_refund'=>array(3,4)]);
            if($rsd->num_rows() > 0) {
                $sum    = $rsd->row();
                $row->total     = $sum->total;
                $row->nominal   = $sum->nominal;
            }
            else {
                $row->total     = 0;
                $row->nominal   = 0;
            }
            $rsd->free_result();
            
            if($row->total == 0) $this->set_response(['success'=>false, 'message'=>'Refund '.$kode.' belum ada yang disetujui !', 'data'=>$row], REST_Controller::HTTP_NOT_FOUND);
            else $this->set_response(['success'=>true, 'message'=>'Refund Ditemukan.', 'data'=>$row], REST_Controller::HTTP_OK);
        }
        else $this->set_response(['success'=>false, 'message'=>'Refund Tidak Ditemukan !'], REST_Controller::HTTP_NOT_FOUND);
        $rs->free_result();
    }
    
    public function lists_get() {
        $kode_booking   = $this->uri->segment(3);
        $kode_refund    = $this->uri->segment(4);
        $tanggal        = $this->uri->segment(5);
        $status         = $this->uri->segment(6);
        $doc            = $this->uri->segment(7);
        
        if($kode_booking == '_') $kode_booking = '';
        if($kode_refund == '_') $kode_refund = '';
        if($tanggal == '_') $tanggal = '';
        if($status == '_') $status = '';
        
        $kode_booking   = str_replace('_', ' ', $kode_booking);
        $kode_booking   = trim($kode_booking);
        $kode_refund    = str_replace('_', ' ', $kode_refund);
        $kode_refund    = trim($kode_refund);
        
        $crit   = ['kode_booking'=>$kode_booking,'kode_refund'=>$kode_refund,'tanggal'=>$tanggal,'status'=>$status];
        $sort	= 'trd.id_trx_tiket_sales_detail';
        $order	= 'asc';
        
        $rows   = $this->TiketRefundModel->get_list($crit, $sort, $order)->result();
        if($status != '') {
            $sr     = $this->StatusRefundModel->get_by_id($status)->row();
            $status = $sr->status_refund;
        }
        else $status = 'SEMUA';
        
        if ($doc == 'xls') {
            require_once APPPATH.'/third_party/phpexcel/PHPExcel/IOFactory.php';
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load(APPPATH."/templates/refund.xlsx");
            
            $objPHPExcel->getActiveSheet()->setCellValue('C3', ': '.tgl_rev($tanggal));
            $objPHPExcel->getActiveSheet()->setCellValue('C4', ': '.strtoupper($status));
            
            $objWorksheet  = $objPHPExcel->getActiveSheet();
            $start         = 8;
            $r=$start;
            $c=0;
            foreach ($rows as $row) {
                $c++;
                $objWorksheet->insertNewRowBefore($r, 1);
                
                $objWorksheet->setCellValue('A'.$r, $c);
                $objWorksheet->setCellValue('B'.$r, $row->kode_booking);
                $objWorksheet->setCellValue('C'.$r, $row->asal.' - '.$row->tujuan);
                $objWorksheet->setCellValue('D'.$r, tgl_rev($row->tgl_berangkat));
                $objWorksheet->setCellValue('E'.$r, $row->nama);
                $objWorksheet->setCellValue('F'.$r, $row->jenis_kelamin);
                $objWorksheet->setCellValue('G'.$r, $row->alamat);
                $objWorksheet->setCellValue('H'.$r, $row->status_refund);
                
                $r++;
            }
            $stop  = $start+count($rows)-1;
            if(count($rows) > 0) $objWorksheet->removeRow($start-1);
            download_excel($objPHPExcel, 'Refund-Tiket.'.date('YmdHis'));
        }
        elseif ($doc == 'pdf') {
            $data  = array();
            $data['tanggal']   = tgl_rev($tanggal);
            $data['status']    = strtoupper($status);
            $data['rows']      = $rows;
            
            $this->load->library('M_pdf');
            $mpdf = $this->m_pdf->load(['mode' => 'utf-8', 'format' => 'A4-L']);
            $html = $this->load->view('refund', $data, true);
            
            $stylesheet = file_get_contents('../assets/bootstrap/css/bootstrap.css');
            $mpdf->WriteHTML($stylesheet,1);
            #$mpdf->SetHTMLHeader('<img src="../assets/images/header.png" width="100%" border="0"/>');
            $mpdf->WriteHTML($html);
            $mpdf->Output('Refund-Tiket.'.date('YmdHis').'.pdf', 'D');
        }
        else $this->response($rows, REST_Controller::HTTP_OK);
    }
    
    public function info_get(){
        $id_trx_tiket_refund = $this->uri->segment(3);
        $row    = $this->TiketRefundModel->get_info($id_trx_tiket_refund)->row();
        $row->tgl_penjualan = date('d-m-Y', strtotime($row->tgl_penjualan));
        $row->tgl_berangkat = date('d-m-Y', strtotime($row->tgl_berangkat));
        $row->tgl_pengajuan = date('d-m-Y', strtotime($row->tgl_pengajuan));
        $this->response($row, REST_Controller::HTTP_OK);
    }
    
    public function proses_post() {
        $headers	= $this->input->request_headers();
        
        
        $id_trx_tiket_sales   = $this->uri->segment(3);
        $values = json_decode(file_get_contents('php://input'), true);
        
        $data = array();
        $kode_refund                   = 'RFD'.$this->get_kode_refund(5);
        while ($this->TiketRefundModel->is_kode_exists($kode_refund)) $kode_refund = 'RFD'.$this->get_kode_refund();
        
        $data['kode_refund']           = $kode_refund;
        $data['id_trx_tiket_sales']    = $id_trx_tiket_sales;
        $data['id_status_pesan']       = $values['id_status_pesan'];
        if(isset($headers[config_item('rest_key_name')])) {
            $user		= $this->UsersModel->getInfo($headers[config_item('rest_key_name')]);
            $data['id_pengguna'] = $user->ID_PENGGUNA;
        }
        $data['tgl_pengajuan']         = date('Y-m-d');
        $data['waktu_pengajuan']       = date('H:i:s');
        $data['nama_pemohon']          = $values['nama_pemohon'];
        $data['id_jenis_identitas']    = $values['id_jenis_identitas'];
        $data['nomor_identitas']       = $values['nomor_identitas'];
        $data['nomor_telp']            = $values['nomor_telp'];
        $data['id_bank']               = $values['id_bank'];
        $data['rekening']              = $values['rekening'];
        $data['atas_nama']             = $values['atas_nama'];
        $data['alasan']                = $values['alasan'];
        
        $id_trx_tiket_refund           = $this->TiketRefundModel->save($data);

        /*
        $rs = $this->TiketRefundModel->get_by_tiket_sales($id_trx_tiket_sales);
        if($rs->num_rows() > 0) {
            #update
            $row = $rs->row();
            $id_trx_tiket_refund = $row->id_trx_tiket_refund;
            $this->TiketRefundModel->update($id_trx_tiket_refund, $data);
            
            #delete and insert
            #$this->TiketRefundDetailModel->delete_by_tiket_refund($id_trx_tiket_refund);
        }
        else {
            #insert
            $id_trx_tiket_refund = $this->TiketRefundModel->save($data);
        }
        $rs->free_result();
        */
        
        #insert detail
        $refunds    = $this->TiketSalesDetailModel->get_by_tikets($values['refunds'])->result_array();
        foreach ($refunds as $r)
        {
            $details    = array();
            $details['id_trx_tiket_sales_detail']   = $r['id_trx_tiket_sales_detail'];
            $details['id_trx_tiket_refund']         = $id_trx_tiket_refund;
            $details['id_status_refund']            = 1;
            $details['refund']                      = intval($r['tarif']) * 0.75;
            
            $this->TiketRefundDetailModel->save($details);
        }
        $this->set_response(['success'=>true, 'message'=>"Proses pengajuan refund berhasil dikirim.\nKode Refund Anda: ".$data['kode_refund']], REST_Controller::HTTP_OK);
    }
    
    public function find_get(){
        $tiket_sales_id = $this->uri->segment(3);
        $rs    = $this->TiketRefundModel->get_by_tiket_sales($tiket_sales_id);
        if($rs->num_rows() > 0) 
        {
            $row = $rs->row();
            $this->response($row, REST_Controller::HTTP_OK);
        }
        else $this->response(null, REST_Controller::HTTP_OK);
    }
    
    public function statistic_get(){
        $data   = ['diajukan'=>0,'diajukan_p'=>0,'ditolak'=>0,'ditolak_p'=>0,'disetujui'=>0,'disetujui_p'=>0,'diproses'=>0,'diproses_p'=>0,];
        $total  = 0;
        $rows   = $this->TiketRefundDetailModel->get_statistic()->result();
        foreach ($rows as $r) {
            $total += $r->total;
            if($r->id_status_refund == 1) $data['diajukan'] = $r->total;
            elseif($r->id_status_refund == 2) $data['ditolak'] = $r->total;
            elseif($r->id_status_refund == 3) $data['disetujui'] = $r->total;
            elseif($r->id_status_refund == 4) $data['diproses'] = $r->total;
        }
        if($data['diajukan'] > 0) $data['diajukan_p'] = ceil(($data['diajukan']/$total) * 100);
        if($data['ditolak'] > 0) $data['ditolak_p'] = ceil(($data['ditolak']/$total) * 100);
        if($data['disetujui'] > 0) $data['disetujui_p'] = ceil(($data['disetujui']/$total) * 100);
        if($data['diproses'] > 0) $data['diproses_p'] = ceil(($data['diproses']/$total) * 100);
        $this->response($data, REST_Controller::HTTP_OK);
    }
}
?>