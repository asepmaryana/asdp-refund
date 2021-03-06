<?php
class TiketSalesDetailModel extends CI_Model
{
	public $table	= 'trx_tiket_sales_detail';
	
	public function __construct() {
		parent::__construct();
	}
	
	public function get_count() {
		$this->db->select('count(id_trx_tiket_sales_detail) as total');
		$rs = $this->db->get($this->table);        		
        if($rs->num_rows() > 0) {
            $row = $rs->row();
            return $row->total;
        } else return 0;
	}
	
	public function get_paged_list($sort, $order, $limit = 10, $offset = 0) {
	    $this->db->select('id_peran,kode_peran,deskripsi');
		if($sort != '' && $order != '') $this->db->order_by($sort, $order);
        return $this->db->get($this->table, $limit, $offset);
	}
	
	public function get_list($crit, $sort, $order) {
	    $this->db->select('tsd.id_trx_tiket_sales_detail,tsd.nama,jid.id_jenis_identitas,jid.jenis_identitas,tsd.no_identitas,jk.jenis_kelamin,tsd.usia,tsd.alamat,tsd.kode_boarding,pa.dermaga as asal,pt.dermaga as tujuan,jl.layanan,gol.golongan,tsd.no_polisi,tsd.tarif,tsd.masuk_kapal,ts.tgl_berangkat');
	    
	    $this->db->join('trx_tiket_sales ts', 'tsd.id_trx_tiket_sales=ts.id_trx_tiket_sales', 'left');
	    $this->db->join('ref_dermaga pa', 'ts.pelabuhan_asal=pa.id_dermaga', 'left');
	    $this->db->join('ref_dermaga pt', 'ts.pelabuhan_tujuan=pt.id_dermaga', 'left');
	    $this->db->join('ref_jenis_identitas jid', 'tsd.id_jenis_identitas=jid.id_jenis_identitas', 'left');
	    $this->db->join('ref_jenis_kelamin jk', 'tsd.id_jenis_kelamin=jk.id_jenis_kelamin', 'left');
	    $this->db->join('ref_jenis_layanan jl', 'ts.id_jenis_layanan=jl.id_jenis_layanan', 'left');
	    $this->db->join('ref_golongan gol', 'tsd.id_golongan=gol.id_golongan', 'left');
	    
	    if(isset($crit['id_layanan']) && $crit['id_layanan'] != '') $this->db->where('ts.id_jenis_layanan', $crit['id_layanan']);	    
	    if(isset($crit['tanggal']) && $crit['tanggal'] != '') $this->db->where('ts.tgl_berangkat', $crit['tanggal']);
	    if(isset($crit['status']) && $crit['status'] == 'sudah') {
	        $this->db->where('tsd.masuk_kapal is not null');
	        if(isset($crit['id_kapal']) && $crit['id_kapal'] != '') $this->db->where('tsd.id_kapal', $crit['id_kapal']);
	        if(isset($crit['id_dermaga']) && $crit['id_dermaga'] != '') $this->db->where('tsd.id_dermaga', $crit['id_dermaga']);
	    }
	    if(isset($crit['status']) && $crit['status'] == 'belum') $this->db->where('tsd.masuk_kapal is null and tsd.tgl_checkin is not null');
	    if(isset($crit['id_trx_tiket_sales']) && $crit['id_trx_tiket_sales'] != '') $this->db->where('ts.id_trx_tiket_sales', $crit['id_trx_tiket_sales']);
	    #if($exclude) $this->db->where("tsd.id_trx_tiket_sales_detail not in(select id_trx_tiket_sales_detail from trx_tiket_refund_detail where id_trx_tiket_refund = '".."')");
		if($sort != '' && $order != '') $this->db->order_by($sort, $order);
        return $this->db->get($this->table.' tsd');
	}
	
	public function get_rekap($crit) {
	    $this->db->select('gol.id_golongan, count(tsd.id_trx_tiket_sales_detail) as total');
	    
	    $this->db->join('trx_tiket_sales ts', 'tsd.id_trx_tiket_sales=ts.id_trx_tiket_sales', 'left');
	    $this->db->join('ref_dermaga pa', 'ts.pelabuhan_asal=pa.id_dermaga', 'left');
	    $this->db->join('ref_dermaga pt', 'ts.pelabuhan_tujuan=pt.id_dermaga', 'left');
	    $this->db->join('ref_jenis_kelamin jk', 'tsd.id_jenis_kelamin=jk.id_jenis_kelamin', 'left');
	    $this->db->join('ref_jenis_layanan jl', 'ts.id_jenis_layanan=jl.id_jenis_layanan', 'left');
	    $this->db->join('ref_golongan gol', 'tsd.id_golongan=gol.id_golongan', 'left');
	    
	    if(isset($crit['id_golongan']) && $crit['id_golongan'] != '') $this->db->where('tsd.id_golongan', $crit['id_golongan']);
	    if(isset($crit['id_layanan']) && $crit['id_layanan'] != '') $this->db->where('ts.id_jenis_layanan', $crit['id_layanan']);
	    if(isset($crit['id_kapal']) && $crit['id_kapal'] != '') $this->db->where('tsd.id_kapal', $crit['id_kapal']);
	    if(isset($crit['id_dermaga']) && $crit['id_dermaga'] != '') $this->db->where('tsd.id_dermaga', $crit['id_dermaga']);
	    if(isset($crit['tanggal']) && $crit['tanggal'] != '') $this->db->where('ts.tgl_berangkat', $crit['tanggal']);
	    #if(isset($crit['jam']) && $crit['jam'] != '') $this->db->where('ts.jam', $crit['jam'].':00');
	    if(isset($crit['id_kelamin']) && $crit['id_kelamin'] != '') $this->db->where('tsd.id_jenis_kelamin', $crit['id_kelamin']);
	    
	    $this->db->group_by('gol.id_golongan'); 
	    $this->db->order_by('gol.id_golongan');
	    
	    return $this->db->get($this->table.' tsd');
	}
	
	public function get_by_id($id)
	{
	    #$this->db->select('id_peran,kode_peran,deskripsi');
		$this->db->where('id_trx_tiket_sales_detail', $id);
		return $this->db->get($this->table);
	}
	
	public function get_by_kode($kode)
	{
	    $this->db->select('tsd.id_trx_tiket_sales_detail as id,tsd.nama,jid.id_jenis_identitas,jid.jenis_identitas,tsd.no_identitas,jk.jenis_kelamin,tsd.usia,tsd.alamat,tsd.kode_boarding,pa.dermaga as asal,pt.dermaga as tujuan,jl.layanan,gol.golongan,tsd.no_polisi,tsd.tarif,tsd.masuk_kapal,ts.tgl_berangkat');
	    
	    $this->db->join('trx_tiket_sales ts', 'tsd.id_trx_tiket_sales=ts.id_trx_tiket_sales', 'left');
	    $this->db->join('ref_dermaga pa', 'ts.pelabuhan_asal=pa.id_dermaga', 'left');
	    $this->db->join('ref_dermaga pt', 'ts.pelabuhan_tujuan=pt.id_dermaga', 'left');
	    $this->db->join('ref_jenis_identitas jid', 'tsd.id_jenis_identitas=jid.id_jenis_identitas', 'left');
	    $this->db->join('ref_jenis_kelamin jk', 'tsd.id_jenis_kelamin=jk.id_jenis_kelamin', 'left');
	    $this->db->join('ref_jenis_layanan jl', 'ts.id_jenis_layanan=jl.id_jenis_layanan', 'left');
	    $this->db->join('ref_golongan gol', 'tsd.id_golongan=gol.id_golongan', 'left');
	    
	    $this->db->where('tsd.kode_boarding', $kode);
	    return $this->db->get($this->table.' tsd');
	}
	
	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	public function update($id, $data)
	{
		$this->db->where('id_trx_tiket_sales_detail', $id);
		$this->db->update($this->table, $data);
	}
	
	public function delete($id)
	{
		$this->db->where('id_trx_tiket_sales_detail', $id);
		return $this->db->delete($this->table);
	}
	
	public function get_by_tikets($tikets)
	{
	    $this->db->select('tsd.id_trx_tiket_sales_detail,tsd.tarif');
	    $this->db->where_in('tsd.id_trx_tiket_sales_detail', explode(',', $tikets));
	    return $this->db->get($this->table.' tsd');
	}
}
?>