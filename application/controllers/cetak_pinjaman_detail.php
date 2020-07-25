<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cetak_pinjaman_detail extends OperatorController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('general_m');
		$this->load->model('pinjaman_m');
		$this->load->model('angsuran_m');
		$this->load->model('setting_m');
	}	

	function cetak($id) {
		$row = $this->pinjaman_m->get_data_pinjam($id);
		if($row == FALSE) {
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
		'.$pdf->nsi_box($text = '<span class="txt_judul">Detail Transaksi Pembayaran Kredit <br></span>', $width = '100%', $spacing = '0', $padding = '1', $border = '0', $align = 'center').'
		<table width="100%" cellspacing="0" cellpadding="3" border="1" border-collapse= "collapse">';

			$anggota = $this->general_m->get_data_anggota($row->anggota_id);
			$angsuran = $this->angsuran_m->get_data_angsuran($row->id);

			$hitung_denda = $this->general_m->get_jml_denda($row->id);
			$hitung_dibayar = $this->general_m->get_jml_bayar($row->id);
			$sisa_ags = $this->general_m->get_record_bayar($row->id);
			$angsuran = $this->angsuran_m->get_data_angsuran($row->id);

			$tgl_bayar = explode(' ', $row->tgl_pinjam);
			$txt_tanggal = jin_date_ina($tgl_bayar[0]);   

			$tgl_tempo = explode(' ', $row->tempo);
			$tgl_tempo = jin_date_ina($tgl_tempo[0]); 

			//AG'.sprintf('%05d', $row->anggota_id).'
			$html .='<table width="100%">   
			<tr>
				<td width="18%"> ID Anggota </td>
				<td width="2%"> : </td>
				<td width="45%"> '.$anggota->identitas.'</td>

				<td> Pokok Pinjaman </td>
				<td width="5%"> : Rp. </td>
				<td width="10%" class="h_kanan"> '.number_format($row->jumlah).'</td>
			</tr>
			<tr>
				<td> Nama Anggota </td>
				<td> : </td>
				<td> <strong>'.strtoupper($anggota->nama).'</strong></td>

				<td> Angsuran Pokok </td>
				<td> : Rp. </td>
				<td class="h_kanan"> '.number_format($row->pokok_angsuran).'</td>
			</tr>
			<tr>
				<td> Alamat </td>
				<td> : </td>
				<td> '.$anggota->alamat.'</td>

				<td> Biaya Admin </td>
				<td> : Rp. </td>
				<td class="h_kanan"> '.number_format($row->biaya_adm).'</td>

				
			</tr>
			<tr>
				<td > Nomor Pinjam </td>
				<td > :  </td>
				<td > '.'TPJ'.sprintf('%05d', $row->id).'</td>

				<td> Angsuran Bunga </td>
				<td> : Rp. </td>
				<td class="h_kanan"> '.number_format($row->bunga_pinjaman).'</td>

				
			</tr>
			<tr>
				<td> Tanggal Pinjam </td>
				<td> : </td>
				<td> '.$txt_tanggal.'</td>

				<td> Jumlah Angsuran </td>
				<td> : Rp. </td>
				<td class="h_kanan"> '.number_format(nsi_round($row->ags_per_bulan)).'</td>
			</tr>
			<tr>
				<td> Tanggal Tempo </td>
				<td> : </td>
				<td> '.$tgl_tempo.'</td>
			</tr>

			<tr>
				<td> Lama Pinjam </td>
				<td> : </td>
				<td> '.$row->lama_angsuran.' Bulan</td>
			</tr>';
			$html .= '</table>';

			$tagihan = $row->ags_per_bulan * $row->lama_angsuran;
			$dibayar = $hitung_dibayar->total;
			$jml_denda = $hitung_denda->total_denda;
			$sisa_bayar = $tagihan - $dibayar;
			$total_bayar = $sisa_bayar + $jml_denda;
			$sisa_angsuran = $row->lama_angsuran - $sisa_ags;

			$html .= '<br><br><strong> Detail Pembayaran </strong><br><br>';
			$html .= '<table width="80%">
			<tr>
				<td> Total Pinjman</td><td class="h_kanan">'.number_format(nsi_round($tagihan)).'</td>
				<td class="h_kanan"> Status Lunas </td> 
				<td class="h_kiri"> : '.$row->lunas.'</td>
			</tr>
			<tr>
				<td> Total Denda</td>
				<td class="h_kanan"> '.number_format(nsi_round($jml_denda)).'</td>
			</tr>
			<tr>
				<td> Total Tagihan</td>
				<td class="h_kanan">'.number_format(nsi_round($tagihan + $jml_denda)).'</td>
			</tr>
			<tr>
				<td> Sudah Dibayar </td>
				<td class="h_kanan"> '.number_format(nsi_round($dibayar)).'</td>
			</tr>
			<tr>
				<td> Sisa Tagihan </td>
				<td class="h_kanan"> '.number_format(nsi_round($total_bayar )).'</td>
			</tr>
		</table> <br><br>';

		$simulasi_tagihan = $this->pinjaman_m->get_simulasi_pinjaman($id);

		$html .= '<br><br><strong> Simulasi Tagihan </strong><br><br>';
		$html .= '<table width="100%">
			<tr class="header_kolom">
				<th style="width:10%;"> Bln ke</th>
				<th style="width:20%;"> Angsuran Pokok</th>
				<th style="width:20%;"> Angsuran Bunga</th>
				<th style="width:10%;"> Biaya Adm</th>
				<th style="width:20%;"> Jumlah Angsuran</th>
				<th style="width:20%;"> Tanggal Tempo</th>
			</tr>';

		if(!empty($simulasi_tagihan)) {
			$no = 1;
			$row = array();
			$jml_pokok = 0;
			$jml_bunga = 0;
			$jml_ags = 0;
			$jml_adm = 0;
			foreach ($simulasi_tagihan as $row) {

				$txt_tanggal = jin_date_ina($row['tgl_tempo']);
				$jml_pokok += $row['angsuran_pokok'];
				$jml_bunga += $row['bunga_pinjaman'];
				$jml_adm += $row['biaya_adm'];
				$jml_ags += $row['jumlah_ags'];

				$html .= '
					<tr>
						<td class="h_tengah">'.$no.'</td>
						<td class="h_kanan">'.number_format(nsi_round($row['angsuran_pokok'])).'</td>
						<td class="h_kanan">'.number_format(nsi_round($row['bunga_pinjaman'])).'</td>
						<td class="h_kanan">'.number_format(nsi_round($row['biaya_adm'])).'</td>
						<td class="h_kanan">'.number_format(nsi_round($row['jumlah_ags'])).'</td>
						<td class="h_kanan">'.$txt_tanggal.'</td>
					</tr>';
				$no++;
			}
			$html .= '<tr bgcolor="#eee">
						<td class="h_tengah"><strong>Jumlah</strong></td>
						<td class="h_kanan"><strong>'.number_format(nsi_round($jml_pokok)).'</strong></td>
						<td class="h_kanan"><strong>'.number_format(nsi_round($jml_bunga)).'</strong></td>
						<td class="h_kanan"><strong>'.number_format(nsi_round($jml_adm)).'</strong></td>
						<td class="h_kanan"><strong>'.number_format(nsi_round($jml_ags)).'</strong></td>
						<td></td>
					</tr>
				</table>';
		}
		$html .= '<br><br><br><br><br><br><br><strong> Data Pembayaran </strong>';
		if(!empty($angsuran)) {
			$html .='<br><br><table width="100%" cellspacing="0" cellpadding="3" border="1" border-collapse= "collapse">
			<tr class="header_kolom" >
				<th style=" width:5%;"> No. </th>
				<th style=" width:15%;"> Kode Bayar</th>
				<th style=" width:15%;"> Tanggal Bayar</th>
				<th style=" width:10%;"> Angsuran Ke </th>
				<th style=" width:15%;"> Jenis Pembayaran </th>
				<th style=" width:20%;"> Jumlah Bayar</th>
				<th style=" width:20%;"> Denda  </th>
			</tr>';

			$no=1;
			$jml_tot = 0;
			$jml_denda = 0;


			foreach ($angsuran as $rows) {
				$tgl_bayar      = explode(' ', $rows->tgl_bayar);
				$txt_tanggal    = jin_date_ina($tgl_bayar[0],'p');
				$jml_tot        += $rows->jumlah_bayar;
				$jml_denda      += $rows->denda_rp;

				$html.= '<tr>
				<td class="h_tengah"> '.$no++.'</td>
				<td class="h_tengah"> '.'TBY'.sprintf('%05d',$rows->id).'</td>
				<td class="h_tengah"> '.$txt_tanggal.'</td>
				<td class="h_tengah"> '.$rows->angsuran_ke.'</td>
				<td class="tengah"> '.$rows->ket_bayar.'</td>
				<td class="h_kanan"> '.number_format(nsi_round($rows->jumlah_bayar)).'</td>
				<td class="h_kanan"> '.number_format(nsi_round($rows->denda_rp)).'</td>
			</tr>';
			}
			$html.='
			<tr class="header_kolom">
				<td class="h_tengah" colspan="5"><strong>Jumlah</strong></td>
				<td class="h_kanan"><strong>'.number_format(nsi_round($jml_tot)).'</strong></td>
				<td class="h_kanan"><strong>'.number_format(nsi_round($jml_denda)).'</strong></td>
			</tr>
			</table>';
		} else {
			$html.='Tidak Ada Data Transkasi';
		}
		$pdf->nsi_html($html);
		$pdf->Output('detail'.date('Ymd_His') . '.pdf', 'I');
	}

	public function export($id){
		$row = $this->pinjaman_m->get_data_pinjam($id);
			if($row == FALSE) {
				echo 'DATA KOSONG';
	        //redirect('angsuran_detail');
				exit();
			}

		$opsi_val_arr = $this->setting_m->get_key_val();
		foreach ($opsi_val_arr as $key => $value){
			$out[$key] = $value;
		}
	    // Load plugin PHPExcel nya
	    include APPPATH.'libraries/phpexcel/PHPExcel.php';
	    
	    // Panggil class PHPExcel nya
	    $excel = new PHPExcel();
	    // Settingan awal fil excel
	    $excel->getProperties()->setCreator('My Notes Code')
	                 ->setLastModifiedBy('My Notes Code')
	                 ->setTitle("Detail Transkasi Pembayaran Kredit")
	                 ->setSubject("Anggota")
	                 ->setDescription("Detail Transkasi Pembayaran Kredit")
	                 ->setKeywords("Detail Transkasi Pembayaran Kredit");
	    // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
	    $style_col = array(
	      'font' => array('bold' => true), // Set font nya jadi bold
	      'alignment' => array(
	        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
	        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
	      ),
	      'borders' => array(
	        'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
	        'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
	        'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
	        'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
	      )
	    );
	    // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
	    $style_row = array(
	      'alignment' => array(
	        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
	      ),
	      'borders' => array(
	        'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
	        'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
	        'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
	        'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
	      )
	    );
	        $excel->setActiveSheetIndex(0)->setCellValue('A1', "Detail Transkasi Pembayaran Kredit"); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A1:N1'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
		    $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
		    $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1
		    $anggota = $this->general_m->get_data_anggota($row->anggota_id);
			$angsuran = $this->angsuran_m->get_data_angsuran($row->id);

			$hitung_denda = $this->general_m->get_jml_denda($row->id);
			$hitung_dibayar = $this->general_m->get_jml_bayar($row->id);
			$sisa_ags = $this->general_m->get_record_bayar($row->id);
			$angsuran = $this->angsuran_m->get_data_angsuran($row->id);

			$tgl_bayar = explode(' ', $row->tgl_pinjam);
			$txt_tanggal = jin_date_ina($tgl_bayar[0]);   

			$tgl_tempo = explode(' ', $row->tempo);
			$tgl_tempo = jin_date_ina($tgl_tempo[0]);
		    $excel->setActiveSheetIndex(0)->setCellValue('A2', "ID Anggota : "); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); //
		    $excel->setActiveSheetIndex(0)->setCellValue('C2', $anggota->identitas); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A2:B2'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A3', "Nama Anggota : "); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C3', $anggota->nama); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A3:B3'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A4', "Alamat : "); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C4', $anggota->alamat); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A4:B4'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->mergeCells('C4:D4');
		    $excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A5', "Nomor Pinjam : "); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C5', "TPJ0000"); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('D5', $row->id); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		    $excel->getActiveSheet()->mergeCells('A5:B5'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A5')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A6', "Tanggal Pinjam : "); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C6', $txt_tanggal); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A6:B6'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A6')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('E2', "Pokok Pinjaman Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('F2', number_format($row->jumlah)); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E2')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('E3', "Angsuran Pokok Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('F3', number_format($row->pokok_angsuran)); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E3')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('E4', "Biaya Admin Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('F4', number_format($row->biaya_adm)); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E4')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('E5', "Angsuran Bunga Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('F5', number_format($row->bunga_pinjaman)); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E5')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('E6', "Jumlah Angsuran Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('F6', number_format(nsi_round($row->ags_per_bulan))); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E6')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A7', "Tanggal Tempo : "); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C7', $tgl_tempo); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A7:B7'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A7')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A8', "Lama Pinjam : "); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C8', $row->lama_angsuran); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('D8', "Bulan"); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		    $excel->getActiveSheet()->mergeCells('A8:B8'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A8')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A10', "Detail Pembayaran "); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		    $excel->getActiveSheet()->mergeCells('A10:D10'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A10')->getFont()->setBold(TRUE); // Set bold kolom A1
		    $excel->getActiveSheet()->getStyle('A10')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $tagihan = $row->ags_per_bulan * $row->lama_angsuran;
			$dibayar = $hitung_dibayar->total;
			$jml_denda = $hitung_denda->total_denda;
			$sisa_bayar = $tagihan - $dibayar;
			$total_bayar = $sisa_bayar + $jml_denda;
			$sisa_angsuran = $row->lama_angsuran - $sisa_ags;

		    $excel->setActiveSheetIndex(0)->setCellValue('A12', "Total Pinjaman Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C12', $tagihan); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A12:B12'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A12')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('E12', "Status Lunas :"); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('F12', $row->lunas); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('E12')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A13', "Total Denda Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C13', number_format(nsi_round($jml_denda))); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A13:B13'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A13')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A14', "Total Tagihan Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C14', number_format(nsi_round($tagihan + $jml_denda))); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A14:B14'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A14')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A15', "Sudah Dibayar Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C15', number_format(nsi_round($dibayar))); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A15:B15'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A15')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A16', "Sisa Tagihan Rp."); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		    $excel->setActiveSheetIndex(0)->setCellValue('C16', number_format(nsi_round($total_bayar))); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->mergeCells('A16:B16'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A16')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    $excel->setActiveSheetIndex(0)->setCellValue('A17', "Simulasi Tagihan"); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('A17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		    $excel->getActiveSheet()->mergeCells('A17:D17'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('A17')->getFont()->setBold(TRUE); // Set bold kolom A1
		    $excel->getActiveSheet()->getStyle('A17')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

		    //Buat header tabel nya pada baris ke 3
		    $simulasi_tagihan = $this->pinjaman_m->get_simulasi_pinjaman($id);
		    $excel->setActiveSheetIndex(0)->setCellValue('A18', "Bln Ke"); // Set kolom A3 dengan tulisan "NO"
		    $excel->setActiveSheetIndex(0)->setCellValue('B18', "Angsuran Pokok"); // Set kolom B3 dengan tulisan "NIS"
		    $excel->setActiveSheetIndex(0)->setCellValue('C18', "Margin"); // Set kolom C3 dengan tulisan "NAMA"
		    $excel->setActiveSheetIndex(0)->setCellValue('D18', "Biaya Adm"); // Set kolom D3 dengan tulisan "JENIS KELAMIN"
		    $excel->setActiveSheetIndex(0)->setCellValue('E18', "Jumlah Angsuran"); // Set kolom E3 dengan tulisan "ALAMAT"
		    $excel->setActiveSheetIndex(0)->setCellValue('F18', "Tanggal Tempo"); // Set kolom E3 dengan tulisan "ALAMAT"
		    // Apply style header yang telah kita buat tadi ke masing-masing kolom header
		    $excel->getActiveSheet()->getStyle('A18')->applyFromArray($style_col);
		    $excel->getActiveSheet()->getStyle('B18')->applyFromArray($style_col);
		    $excel->getActiveSheet()->getStyle('C18')->applyFromArray($style_col);
		    $excel->getActiveSheet()->getStyle('D18')->applyFromArray($style_col);
		    $excel->getActiveSheet()->getStyle('E18')->applyFromArray($style_col);
		    $excel->getActiveSheet()->getStyle('F18')->applyFromArray($style_col);
		    // Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
		    
		     // Set baris pertama untuk isi tabel adalah baris ke 4
		if(!empty($simulasi_tagihan)) {
		    $no = 1;
		    $numrow = 19;
			$row = array();
			$jml_pokok = 0;
			$jml_bunga = 0;
			$jml_ags = 0;
			$jml_adm = 0;
			foreach ($simulasi_tagihan as $row) {
			 	$txt_tanggal = jin_date_ina($row['tgl_tempo']);
				$jml_pokok += $row['angsuran_pokok'];
				$jml_bunga += $row['bunga_pinjaman'];
				$jml_adm += $row['biaya_adm'];
				$jml_ags += $row['jumlah_ags'];
		     // Lakukan looping pada variabel siswa
		      $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
		      $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, number_format(nsi_round($row["angsuran_pokok"])));
		      $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, number_format(nsi_round($row["bunga_pinjaman"])));
		      $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, number_format(nsi_round($row["biaya_adm"])));
		      $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, number_format(nsi_round($row["jumlah_ags"])));
		      $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $txt_tanggal);
		      
		      // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
		      $excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_row);
		      $excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
		      $excel->getActiveSheet()->getStyle('C'.$numrow)->applyFromArray($style_row);
		      $excel->getActiveSheet()->getStyle('D'.$numrow)->applyFromArray($style_row);
		      $excel->getActiveSheet()->getStyle('E'.$numrow)->applyFromArray($style_row);
		      $excel->getActiveSheet()->getStyle('F'.$numrow)->applyFromArray($style_row);
		      
		      $no++; // Tambah 1 setiap kali looping
		      $numrow++; // Tambah 1 setiap kali looping
		    }
		      $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, "Jumlah ");
		      $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, number_format(nsi_round($jml_pokok)));
		      $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, number_format(nsi_round($jml_bunga)));
		      $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, number_format(nsi_round($jml_adm)));
		      $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, number_format(nsi_round($jml_ags)));
		      $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow);

		    // Set width kolom
		    $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Set width kolom A
		    $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20); // Set width kolom B
		    $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10); // Set width kolom C
		    $excel->getActiveSheet()->getColumnDimension('D')->setWidth(12); // Set width kolom D
		    $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30); // Set width kolom E
		    $excel->getActiveSheet()->getColumnDimension('F')->setWidth(40); // Set width kolom E
		    
		}

		if(!empty($angsuran)) {
			$excel->setActiveSheetIndex(0)->setCellValue('H2', "Data Pembayaran"); // Set kolom A1 dengan tulisan "DATA SISWA"
		    $excel->getActiveSheet()->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		    $excel->getActiveSheet()->mergeCells('H2:K2'); // Set Merge Cell pada kolom A1 sampai E1
		    $excel->getActiveSheet()->getStyle('H2')->getFont()->setBold(TRUE); // Set bold kolom A1
		    $excel->getActiveSheet()->getStyle('H2')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

			$excel->setActiveSheetIndex(0)->setCellValue('H3', "No"); // Set kolom A3 dengan tulisan "NO"
		 	$excel->setActiveSheetIndex(0)->setCellValue('I3', "Kode Bayar"); // Set kolom B3 dengan tulisan "NIS"
			$excel->setActiveSheetIndex(0)->setCellValue('J3', "Tanggal Bayar"); // Set kolom C3 dengan tulisan "NAMA"
			$excel->setActiveSheetIndex(0)->setCellValue('K3', "Angsuran Ke"); // Set kolom D3 dengan tulisan "JENIS KELAMIN"
			$excel->setActiveSheetIndex(0)->setCellValue('L3', "Jenis Pembayaran"); // Set kolom E3 dengan tulisan "ALAMAT"
			$excel->setActiveSheetIndex(0)->setCellValue('M3', "Jumlah Bayar"); // Set kolom E3 dengan tulisan "ALAMAT"
			$excel->setActiveSheetIndex(0)->setCellValue('N3', "Denda"); // Set kolom E3 dengan tulisan "ALAMAT"
			// Apply style header yang telah kita buat tadi ke masing-masing kolom header
			$excel->getActiveSheet()->getStyle('H3')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('I3')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('J3')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('K3')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('L3')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('M3')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('N3')->applyFromArray($style_col);

			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$no=1;
			$numrow = 4;
			$jml_tot = 0;
			$jml_denda = 0;

			foreach ($angsuran as $rows) {
				$tgl_bayar      = explode(' ', $rows->tgl_bayar);
				$txt_tanggal    = jin_date_ina($tgl_bayar[0],'p');
				$jml_tot        += $rows->jumlah_bayar;
				$jml_denda      += $rows->denda_rp;

				$excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $no);
			    $excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, 'TBY'.sprintf('%05d',$rows->id));
			    $excel->setActiveSheetIndex(0)->setCellValue('J'.$numrow, $txt_tanggal);
			    $excel->setActiveSheetIndex(0)->setCellValue('K'.$numrow, $rows->angsuran_ke);
			    $excel->setActiveSheetIndex(0)->setCellValue('L'.$numrow, $rows->ket_bayar);
			    $excel->setActiveSheetIndex(0)->setCellValue('M'.$numrow, number_format(nsi_round($rows->jumlah_bayar)));
			    $excel->setActiveSheetIndex(0)->setCellValue('N'.$numrow, number_format(nsi_round($rows->denda_rp)));
			      
			    // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
			    $excel->getActiveSheet()->getStyle('H'.$numrow)->applyFromArray($style_row);
			    $excel->getActiveSheet()->getStyle('I'.$numrow)->applyFromArray($style_row);
			    $excel->getActiveSheet()->getStyle('J'.$numrow)->applyFromArray($style_row);
			    $excel->getActiveSheet()->getStyle('K'.$numrow)->applyFromArray($style_row);
			    $excel->getActiveSheet()->getStyle('L'.$numrow)->applyFromArray($style_row);
			    $excel->getActiveSheet()->getStyle('M'.$numrow)->applyFromArray($style_row);
			    $excel->getActiveSheet()->getStyle('N'.$numrow)->applyFromArray($style_row);
				
				$no++; // Tambah 1 setiap kali looping
			    $numrow++; // Tambah 1 setiap kali looping
			}
			    $excel->setActiveSheetIndex(0)->setCellValue('L'.$numrow, "Jumlah ");
			    $excel->setActiveSheetIndex(0)->setCellValue('M'.$numrow, number_format(nsi_round($jml_tot)));
			    $excel->setActiveSheetIndex(0)->setCellValue('N'.$numrow, number_format(nsi_round($jml_denda)));

			    $excel->getActiveSheet()->getStyle('L'.$numrow)->applyFromArray($style_row);
			    $excel->getActiveSheet()->getStyle('M'.$numrow)->applyFromArray($style_row);
			    $excel->getActiveSheet()->getStyle('N'.$numrow)->applyFromArray($style_row);

			$excel->getActiveSheet()->getColumnDimension('H')->setWidth(10); // Set width kolom A
		    $excel->getActiveSheet()->getColumnDimension('I')->setWidth(20); // Set width kolom B
		    $excel->getActiveSheet()->getColumnDimension('J')->setWidth(20); // Set width kolom C
		    $excel->getActiveSheet()->getColumnDimension('K')->setWidth(15); // Set width kolom D
		    $excel->getActiveSheet()->getColumnDimension('L')->setWidth(30); // Set width kolom E
		    $excel->getActiveSheet()->getColumnDimension('M')->setWidth(20); // Set width kolom E
		    $excel->getActiveSheet()->getColumnDimension('N')->setWidth(20); // Set width kolom E
			
		}
		else {
			    	'Tidak ada transaksi';
		}
		// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
		 	$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Detail Transaksi Pembayaran Kredit");
			$excel->setActiveSheetIndex(0);
			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Detail Transaksi Pembayaran Kredit.xlsx"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');
			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->save('php://output');
	}
}