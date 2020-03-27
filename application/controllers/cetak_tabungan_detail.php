<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cetak_tabungan_detail extends OperatorController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('general_m');
		$this->load->model('simpanan_m');
		$this->load->model('angsuran_m');
		$this->load->model('setting_m');
	}	

	function cetak($id) {
		$rows = $this->simpanan_m->data_simpanan($id);
		if($rows == FALSE) {
			echo 'DATA KOSONG';
        //redirect('angsuran_detail');
			exit();
		}

		$opsi_val_arr = $this->setting_m->get_key_val();
		foreach ($opsi_val_arr as $key => $value){
			$out[$key] = $value;
		}

		$this->load->library('Pdf');
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->set_nsi_header(TRUE);
		$pdf->AddPage('P');
		$html = '';
		$html .= '
		<style>
			.h_tengah {text-align: center;}
			.h_kiri {text-align: left;}
			.h_kanan {text-align: right;}
			.txt_judul {font-size: 12pt; font-weight: bold; padding-bottom: 12px;}
			.header_kolom {background-color: #cccccc; text-align: center; font-weight: bold;}
			.txt_content {font-size: 10pt; font-style: arial;}
		</style>
		'.$pdf->nsi_box($text = '<span class="txt_judul">Kartu Tabungan Anggota <br></span>', $width = '100%', $spacing = '0', $padding = '1', $border = '0', $align = 'center').'
		<table width="100%" cellspacing="0" cellpadding="3" border="1" border-collapse= "collapse">';

			$anggota = $this->general_m->get_data_anggota($id);

			//AG'.sprintf('%05d', $row->anggota_id).'
			$html .='<table width="100%">   
			<tr>
				<td width="18%"> ID Anggota </td>
				<td width="2%"> : </td>
				<td width="45%"> '.$anggota->identitas.'</td>
			</tr>
			<tr>
				<td> Nama Anggota </td>
				<td> : </td>
				<td> <strong>'.strtoupper($anggota->nama).'</strong></td>
			</tr>
			<tr>
				<td> Alamat </td>
				<td> : </td>
				<td> '.$anggota->alamat.'</td>
			</tr>';
			$html .= '</table>';
			$html .= '<br><br><strong> Data Transaksi Tabungan </strong><br><br>';
		if(!empty($rows)) {
			$html .='<br><br><table width="100%" cellspacing="0" cellpadding="3" border="1" border-collapse= "collapse">
			<tr class="header_kolom">
				<th style="width:5%; vertical-align: middle"> No. </th>
				<th style="width:20%; vertical-align: middle"> Tanggal Bayar</th>
				<th style="width:15%; vertical-align: middle"> Kredit </th>
				<th style="width:15%; vertical-align: middle"> Debet </th>
				<th style="width:15%; vertical-align: middle"> Saldo </th>
				<th style="width:20%; vertical-align: middle"> Keterangan </th>
				<th style="width:10%; vertical-align: middle"> Petugas</th>
			</tr>';

			$mulai=1;
			$no=1;
			$saldo = 0;


			foreach ($rows as $row) {
				$tgl_bayar      = explode(' ', $row['tgl']);
				$txt_tanggal    = jin_date_ina($tgl_bayar[0],'p');
				
				$saldo = ($saldo - $row['kredit']) + $row['debet'];
				$jenis = $this->db->get_where('jns_simpan', array('id' => $row['transaksi']))->row();

				$html.= '<tr>
				<td class="h_tengah">'.$no++.'</td>
				<td class="h_tengah">'.$txt_tanggal.'</td>
				<td class="h_tengah">'.$row["kredit"].'</td>
				<td class="h_tengah">'.$row["debet"].'</td>
				<td class="h_tengah">'.$saldo.'</td>
				<td class="h_tengah">'.$jenis->jns_simpan.'</td>
				<td class="h_tengah">'.$row["user"].'</td>
			</tr>';
			}
			$html.='
			</table>';
		} else {
			$html.='Tidak Ada Data Transkasi';
		}
		$pdf->nsi_html($html);
		$pdf->Output('detail'.date('Ymd_His') . '.pdf', 'I');
	}
}