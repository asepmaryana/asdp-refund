<?php
class TiketRefundModel extends CI_Model
{
	public $table	= 'trx_tiket_refund';
	
	public function __construct() {
		parent::__construct();
	}
	
	public function get_list($crit, $sort, $order) {
	    $this->db->select('trd.id_tiket_refund_detail,trd.id_trx_tiket_refund,ts.id_trx_tiket_sales,ts.kode_booking,ts.tgl_berangkat,tsd.nama,jk.jenis_kelamin,tsd.alamat,src.nama_cabang as asal,dst.nama_cabang as tujuan,sr.status_refund,tr.kode_refund');
	    
	    $this->db->join('trx_tiket_refund tr', 'trd.id_trx_tiket_refund=tr.id_trx_tiket_refund', 'left');	    
	    $this->db->join('trx_tiket_sales ts', 'tr.id_trx_tiket_sales=ts.id_trx_tiket_sales', 'left');
	    $this->db->join('trx_tiket_sales_detail tsd', 'trd.id_trx_tiket_sales_detail=tsd.id_trx_tiket_sales_detail', 'inner');
	    $this->db->join('ref_status_refund sr', 'trd.id_status_refund=sr.id_status_refund', 'left');
	    $this->db->join('ref_cabang src', 'ts.pelabuhan_asal=src.id_cabang', 'left');
	    $this->db->join('ref_cabang dst', 'ts.pelabuhan_tujuan=dst.id_cabang', 'left');
	    $this->db->join('ref_jenis_identitas jid', 'tr.id_jenis_identitas=jid.id_jenis_identitas', 'left');
	    $this->db->join('ref_jenis_kelamin jk', 'tsd.id_jenis_kelamin=jk.id_jenis_kelamin', 'left');
	    $this->db->join('ref_jenis_layanan jl', 'ts.id_jenis_layanan=jl.id_jenis_layanan', 'left');
	    $this->db->join('ref_golongan gol', 'tsd.id_golongan=gol.id_golongan', 'left');
	    
	    if(isset($crit['kode_booking']) && $crit['kode_booking'] != '') $this->db->like('ts.kode_booking', $crit['kode_booking'], 'both');
	    if(isset($crit['kode_refund']) && $crit['kode_refund'] != '') $this->db->like('tr.kode_refund', $crit['kode_refund'], 'both');
	    if(isset($crit['tanggal']) && $crit['tanggal'] != '') $this->db->where('tr.tgl_pengajuan', $crit['tanggal']);
	    if(isset($crit['status']) && $crit['status'] != '') $this->db->where('trd.id_status_refund', $crit['status']);
	    
	    if($sort != '' && $order != '') $this->db->order_by($sort, $order);
	    return $this->db->get('trx_tiket_refund_detail trd');
	}
	
	public function get_by_id($id_trx_tiket_refund)
	{
	    $this->db->select('ttr.id_trx_tiket_refund,ttr.id_trx_tiket_sales,ttr.id_status_pesan,ttr.tgl_pengajuan,ttr.waktu_pengajuan,ttr.nama_pemohon,ttr.id_jenis_identitas,ttr.nomor_identitas,ttr.nomor_telp,ttr.id_bank,ttr.rekening,ttr.atas_nama,ttr.id_status_refund,ttr.tgl_proses,ttr.waktu_proses,ttr.alasan,b.nama_bank,i.jenis_identitas');
	    $this->db->join('ref_bank b', 'ttr.id_bank=b.id_bank', 'left');
	    $this->db->join('ref_jenis_identitas i', 'ttr.id_jenis_identitas=i.id_jenis_identitas', 'left');
	    $this->db->where('ttr.id_trx_tiket_refund', $id_trx_tiket_refund);
	    return $this->db->get($this->table.' ttr');
	}
	
	public function get_by_tiket_sales($id_trx_tiket_sales)
	{
	    $this->db->select('ttr.id_trx_tiket_refund,ttr.id_trx_tiket_sales,ttr.id_status_pesan,ttr.tgl_pengajuan,ttr.waktu_pengajuan,ttr.nama_pemohon,ttr.id_jenis_identitas,ttr.nomor_identitas,ttr.nomor_telp,ttr.id_bank,ttr.rekening,ttr.atas_nama,ttr.id_status_refund,ttr.tgl_proses,ttr.waktu_proses,ttr.alasan,b.nama_bank,i.jenis_identitas');	    
	    $this->db->join('ref_bank b', 'ttr.id_bank=b.id_bank', 'left');
	    $this->db->join('ref_jenis_identitas i', 'ttr.id_jenis_identitas=i.id_jenis_identitas', 'left');
	    $this->db->where('ttr.id_trx_tiket_sales', $id_trx_tiket_sales);
	    return $this->db->get($this->table.' ttr');
	}
	
	public function get_by_kode($kode_refund)
	{
	    $this->db->select('tr.id_trx_tiket_refund,tr.kode_refund,ts.kode_booking,ts.tgl_penjualan,ts.tgl_berangkat,tr.tgl_pengajuan,tr.nama_pemohon,tr.rekening,b.nama_bank,tr.nomor_identitas,tr.atas_nama,l.layanan,i.jenis_identitas,src.nama_cabang as asal,dst.nama_cabang as tujuan,tr.alasan');
	    $this->db->join('trx_tiket_sales ts', 'tr.id_trx_tiket_sales=ts.id_trx_tiket_sales', 'left');
	    $this->db->join('ref_bank b', 'tr.id_bank=b.id_bank', 'left');
	    $this->db->join('ref_jenis_identitas i', 'tr.id_jenis_identitas=i.id_jenis_identitas', 'left');
	    $this->db->join('ref_jenis_layanan l', 'ts.id_jenis_layanan=l.id_jenis_layanan', 'left');
	    $this->db->join('ref_cabang src', 'ts.pelabuhan_asal=src.id_cabang', 'left');
	    $this->db->join('ref_cabang dst', 'ts.pelabuhan_tujuan=dst.id_cabang', 'left');
	    $this->db->where('tr.kode_refund', $kode_refund);
	    
	    return $this->db->get($this->table.' tr');
	}
	
	public function get_info($id_trx_tiket_refund)
	{
	    $this->db->select('ts.kode_booking,ts.tgl_penjualan,ts.tgl_berangkat,tr.tgl_pengajuan,tr.nama_pemohon,tr.rekening,b.nama_bank,tr.nomor_identitas,tr.atas_nama,l.layanan,i.jenis_identitas,src.nama_cabang as asal,dst.nama_cabang as tujuan,sp.status_pesan,tr.alasan');
	    $this->db->join('trx_tiket_sales ts', 'tr.id_trx_tiket_sales=ts.id_trx_tiket_sales', 'left');
	    $this->db->join('ref_status_pesan sp', 'ts.id_status_pesan=sp.id_status_pesan', 'left');
	    $this->db->join('ref_bank b', 'tr.id_bank=b.id_bank', 'left');
	    $this->db->join('ref_jenis_identitas i', 'tr.id_jenis_identitas=i.id_jenis_identitas', 'left');
	    $this->db->join('ref_jenis_layanan l', 'ts.id_jenis_layanan=l.id_jenis_layanan', 'left');
	    $this->db->join('ref_cabang src', 'ts.pelabuhan_asal=src.id_cabang', 'left');
	    $this->db->join('ref_cabang dst', 'ts.pelabuhan_tujuan=dst.id_cabang', 'left');
	    $this->db->where('tr.id_trx_tiket_refund', $id_trx_tiket_refund);
	    
	    return $this->db->get($this->table.' tr');
	}
	
	public function count_total_refund($crit)
	{
	    $this->db->select('sum(tsd.tarif) as total, sum(trd.refund) as nominal');
	    $this->db->join('trx_tiket_sales_detail tsd', 'trd.id_trx_tiket_sales_detail=tsd.id_trx_tiket_sales_detail', 'inner');
	    
	    if(isset($crit['id_trx_tiket_refund']) && $crit['id_trx_tiket_refund'] != '') $this->db->where('trd.id_trx_tiket_refund', $crit['id_trx_tiket_refund']);
	    if(isset($crit['id_status_refund']) && !is_array($crit['id_status_refund']) && $crit['id_status_refund'] != '') $this->db->where('trd.id_status_refund', $crit['id_status_refund']);
	    if(isset($crit['id_status_refund']) && is_array($crit['id_status_refund']) && count($crit['id_status_refund']) > 0) $this->db->where_in('trd.id_status_refund', $crit['id_status_refund']);
	    
	    return $this->db->get('trx_tiket_refund_detail trd');
	}
	
	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	public function update($id, $data)
	{
		$this->db->where('id_trx_tiket_refund', $id);
		$this->db->update($this->table, $data);
	}
	
	public function delete($id)
	{
		$this->db->where('id_trx_tiket_refund', $id);
		return $this->db->delete($this->table);
	}
	
	public function is_kode_exists($kode_refund) {
	    $exists = false;
	    $this->db->select('count(kode_refund) as total');
	    $this->db->where('kode_refund', $kode_refund);
	    $rs = $this->db->get($this->table);
	    $row = $rs->row();
	    $exists = (intval($row->total) > 0) ? true : false;
	    $rs->free_result();
	    return $exists;
	}
}
?>