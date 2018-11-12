<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MX_Controller {

	private $container;
	private $valid = false;
    public $rt;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('accesscontrol');
		$this->load->helper('app');
		$this->load->model('modelReferensi');
        $this->load->model('modelKeuangan');
		$this->load->helper('url');
		$this->container["data"] = null;
		$this->rt = $this->session->userdata("rtId");

        LoggedSystem();
	}

	public function reportIuran()
	{
				$this->container["tahun"] = date("Y");
        if($_POST) {
            $args = (object) $this->input->post();

            $this->container["bulan"] = $args->bulan;
            $this->container["tahun"] = $args->tahun;
            $this->container["show"] = 1;

            $sql = "SELECT b.`jenis_penghuni`, b.`nama_lengkap`, CONCAT(b.`blok`,'/',b.`nomor_rumah`) AS nomor_rumah, SUM(a.bayar) AS terbayar, SUM(a.tarif) AS harus_dibayar, a.tanggal_bayar, a.bulan_iuran, a.tahun_iuran
            FROM `trans_iuran` a JOIN `mst_penghuni` b ON b.`id`=a.`id_penghuni` and b.`id_rt`=a.`id_rt` where a.`bulan_iuran`='".$args->bulan."' and a.`tahun_iuran`='".$args->tahun."' AND b.`id_rt`='".$this->session->userdata("rtId")."'
            GROUP BY `id_penghuni`";

            $get = $this->db->query($sql);
            $data = $get->result();
            $this->container["data"] = $data;

        }


        $this->twig->display("report/reportIuran.html", $this->container);
	}

	public function reportNonIuran()
	{
				$this->container["tahun"] = date("Y");
        if($_POST) {
            $args = (object) $this->input->post();

            $this->container["bulan"] = $args->bulan;
            $this->container["tahun"] = $args->tahun;
            $this->container["show"] = 1;

            $sql = "SELECT b.`jenis_penghuni`, b.`nama_lengkap`,  if(a.id_penghuni=0, 'Non Warga', b.nama_lengkap) as namanya, a.atau_dari_sumber, a.catatan,
						 CONCAT(b.`blok`,'/',b.`nomor_rumah`) AS nomor_rumah, a.total AS terbayar, a.`nilai_akhir_iuaran_asal`, a.tanggal_bayar, c.nama_tarif
            FROM `trans_saldo` a LEFT JOIN `mst_penghuni` b ON b.`id`=a.`id_penghuni` and b.`id_rt`=a.`id_rt`
						LEFT JOIN `tarif_iuran` c ON c.id=a.id_iuran where a.`bulan_iuran`='".$args->bulan."' and a.`tahun_iuran`='".$args->tahun."'
						AND a.`id_rt`='".$this->session->userdata("rtId")."'";

            $get = $this->db->query($sql);
            $data = $get->result();
            $this->container["data"] = $data;

        }


        $this->twig->display("report/reportNonIPL.html", $this->container);
	}

  public function reportIuranBelum()
  {
			$this->container["tahun"] = date("Y");
      if($_POST) {
          $args = (object) $this->input->post();

          $this->container["bulan"] = $args->bulan;
          $this->container["tahun"] = $args->tahun;
          $this->container["show"] = 1;

          $sql = "SELECT a.`jenis_penghuni`, a.`nama_lengkap`, CONCAT(a.`blok`,'/',a.`nomor_rumah`) AS nomor_rumah
          FROM `mst_penghuni` a WHERE a.`id_rt`='".$this->session->userdata("rtId")."' AND a.`id`
          NOT IN (SELECT id_penghuni FROM `trans_iuran` WHERE id_rt='".$this->session->userdata("rtId")."' AND bulan_iuran='".$args->bulan."' AND tahun_iuran='".$args->tahun."')";

          $get = $this->db->query($sql);
          $data = $get->result();
          $this->container["data"] = $data;

      }


      $this->twig->display("report/reportIuranbelum.html", $this->container);
  }

	public function reportPindahSaldo()
  {
			$this->container["tahun"] = date("Y");
      if($_POST) {
          $args = (object) $this->input->post();

          $this->container["bulan"] = $args->bulan;
          $this->container["tahun"] = $args->tahun;
          $this->container["show"] = 1;

					$sql = "SELECT c.`nama_tarif` as nama_iuran_tujuan, b.`nama_tarif` as nama_iuran_asal, a.* FROM `trans_mutasisaldo` a
										JOIN `tarif_iuran` b ON a.`id_iuran_asal`=b.`id`
										JOIN `tarif_iuran` c ON a.`id_iuran_tujuan`=c.`id`
										WHERE a.`id_rt`='".$this->session->userdata("rtId")."' AND a.`bulan`='".$args->bulan."' AND a.`tahun`='".$args->tahun."'
										ORDER BY a.`tanggal_trans` DESC";

					$get = $this->db->query($sql);
          $data = $get->result();
          $this->container["data"] = $data;

      }


      $this->twig->display("report/reportPindahSaldo.html", $this->container);
  }


  public function reportSaldoKas()
  {
	  $sql = "SELECT a.*, b.saldo, b.id_tarif FROM `tarif_iuran` a LEFT JOIN `reff_saldo_kas` b ON a.`id`=b.`id_tarif` and a.`id_rt`=b.`id_rt` WHERE a.id_rt='".$this->session->userdata("rtId")."'";
    $get = $this->db->query($sql);
    $data = $get->result();
    $this->container["data"] = $data;
    $this->twig->display("report/reportSaldokas.html", $this->container);
  }

  public function reportPenghuni()
  {
      $sql = "select a.*, b.nomor_kk, b.kepala_keluarga as nama_head from mst_penghuni a join mst_kk b on b.id_kk=a.id_kk where a.id_rt='".$this->session->userdata("rtId")."' order by b.nomor_kk asc";
      $query = $this->db->query($sql);
      $get = $this->db->query($sql);
      $data = $get->result();
      $this->container["data"] = $data;
      $this->twig->display("report/reportPenghuni.html", $this->container);
  }

  public function reportPengeluaran()
  {
		$this->container["tahun"] = date("Y");
		if($_POST) {
				$args = (object) $this->input->post();

				$this->container["bulan"] = $args->bulan;
				$this->container["tahun"] = $args->tahun;
				$this->container["show"] = 1;

				$sql = "SELECT a.*, b.nama_tarif from trans_pengeluaran a join tarif_iuran b on a.id_iuran_keluar=b.id where a.id_rt='".$this->session->userdata("rtId")."'
							AND a.`bulan`='".$args->bulan."' AND a.`tahun`='".$args->tahun."'";

				$get = $this->db->query($sql);
				$data = $get->result();
				$this->container["data"] = $data;

		}

		$this->twig->display("report/reportPengeluaran.html", $this->container);
  }

	public function reportTunggakan()
  {

      $query = $this->db->query("SELECT a.jumlah_bulan, a.`total_tunggakan`, a.`total_terbayar`,
												IF(a.`total_tunggakan` - a.`total_terbayar`<0,0,a.`total_tunggakan` - a.`total_terbayar`) AS sisa_tunggakan,  b.*
												FROM `trans_tunggak` a
												JOIN `mst_penghuni` b ON a.`id_penghuni`=b.`id` where a.id_rt='".$this->session->userdata("rtId")."'");

			$data = $query->result();
      $this->container["data"] = $data;
      $this->twig->display("report/reportTunggakanWarga.html", $this->container);
  }

	public function reportRincian($idTarif)
  {

      $query = $this->db->query("SELECT * FROM `trans_rincian_saldo` WHERE id_iuran='".$idTarif."' and id_rt='".$this->session->userdata("rtId")."'");

			$data = $query->result();
      $this->container["data"] = $data;
      $this->twig->display("report/reportRincianSaldo.html", $this->container);
  }

}
