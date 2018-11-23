<?php
class TiketRefundDetailModel extends CI_Model
{
	public $table	= 'trx_tiket_refund_detail';
	
	public function __construct() {
		parent::__construct();
	}
	
	public function is_exists($id_trx_tiket_sales_detail)
	{
	    $this->db->select('trd.id_trx_tiket_sales_detail');
	    $this->db->where('trd.id_trx_tiket_sales_detail', $id_trx_tiket_sales_detail);
	    $rs = $this->db->get($this->table.' trd');
	    $exists = ($rs->num_rows() > 0) ? true : false;
	    $rs->free_result();
	    return $exists;
	}
	
	public function get_by_id($id_tiket_refund_detail)
	{
	    $this->db->select('trd.id_tiket_refund_detail,trd.id_trx_tiket_sales_detail,trd.id_trx_tiket_refund,trd.id_status_refund,trd.refund,tsd.nama,tsd.alamat,tsd.usia,tsd.tarif,trd.catatan');
	    $this->db->join('trx_tiket_sales_detail tsd', 'trd.id_trx_tiket_sales_detail=tsd.id_trx_tiket_sales_detail', 'left');
	    $this->db->where('trd.id_tiket_refund_detail', $id_tiket_refund_detail);
	    return $this->db->get($this->table.' trd');
	}
	
	public function get_by_tiket_refund($id_trx_tiket_refund)
	{
	    $this->db->select('trd.id_trx_tiket_sales_detail,trd.id_trx_tiket_refund,trd.id_status_refund,trd.refund,tsd.tarif');
	    $this->db->join('trx_tiket_sales_detail tsd', 'trd.id_trx_tiket_sales_detail=tsd.id_trx_tiket_sales_detail', 'left');
	    $this->db->where('trd.id_trx_tiket_refund', $id_trx_tiket_refund);
	    return $this->db->get($this->table.' trd');
	}
	
	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	public function update($id, $data)
	{
		$this->db->where('id_tiket_refund_detail', $id);
		return $this->db->update($this->table, $data);
	}
	
	public function delete($id)
	{
		$this->db->where('id_trx_tiket_sales_detail', $id);
		return $this->db->delete($this->table);
	}
	
	public function delete_by_tiket_refund($id_trx_tiket_refund)
	{
	    $this->db->where('id_trx_tiket_refund', $id_trx_tiket_refund);
	    return $this->db->delete($this->table);
	}
	
	public function get_detail_by_refund($id_trx_tiket_refund)
	{
	    $this->db->select('tsd.id_trx_tiket_sales_detail as id,tsd.nama,jid.id_jenis_identitas,jid.jenis_identitas,tsd.no_identitas,jk.jenis_kelamin,tsd.usia,tsd.alamat,tsd.kode_boarding,pa.dermaga as asal,pt.dermaga as tujuan,jl.layanan,gol.golongan,tsd.no_polisi,tsd.tarif,tsd.masuk_kapal,ts.tgl_berangkat,sr.status_refund');

	    $this->db->join('trx_tiket_sales_detail tsd', 'trd.id_trx_tiket_sales_detail=tsd.id_trx_tiket_sales_detail', 'left');
	    $this->db->join('trx_tiket_sales ts', 'ts.id_trx_tiket_sales=tsd.id_trx_tiket_sales', 'left');
	    $this->db->join('ref_status_refund sr', 'trd.id_status_refund=sr.id_status_refund', 'left');
	    $this->db->join('trx_tiket_refund tr', 'tr.id_trx_tiket_refund=trd.id_trx_tiket_refund', 'left');
	    $this->db->join('ref_dermaga pa', 'ts.pelabuhan_asal=pa.id_dermaga', 'left');
	    $this->db->join('ref_dermaga pt', 'ts.pelabuhan_tujuan=pt.id_dermaga', 'left');
	    $this->db->join('ref_jenis_identitas jid', 'tsd.id_jenis_identitas=jid.id_jenis_identitas', 'left');
	    $this->db->join('ref_jenis_kelamin jk', 'tsd.id_jenis_kelamin=jk.id_jenis_kelamin', 'left');
	    $this->db->join('ref_jenis_layanan jl', 'ts.id_jenis_layanan=jl.id_jenis_layanan', 'left');
	    $this->db->join('ref_golongan gol', 'tsd.id_golongan=gol.id_golongan', 'left');
	    
	    $this->db->where('trd.id_trx_tiket_refund', $id_trx_tiket_refund);
	    return $this->db->get($this->table.' trd');
	}
	
	public function get_statistic()
	{
	    $this->db->select('sr.id_status_refund,count(trd.id_status_refund) as total');
	    $this->db->join('trx_tiket_refund_detail trd', 'sr.id_status_refund=trd.id_status_refund', 'left');
	    $this->db->group_by('sr.id_status_refund');
	    $this->db->order_by('sr.id_status_refund');
	    return $this->db->get('ref_status_refund sr');
	}
}
?>