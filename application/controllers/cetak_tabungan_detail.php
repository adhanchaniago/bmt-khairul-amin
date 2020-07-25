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
				<td width="45%"> AG'. sprintf('%04d', $anggota->id) .'</td>
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

	public function export($id){
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
    // Load plugin PHPExcel nya
    include APPPATH.'libraries/phpexcel/PHPExcel.php';
    
    // Panggil class PHPExcel nya
    $excel = new PHPExcel();
    // Settingan awal fil excel
    $excel->getProperties()->setCreator('My Notes Code')
                 ->setLastModifiedBy('My Notes Code')
                 ->setTitle("Data Tabungan")
                 ->setSubject("Anggota")
                 ->setDescription("Laporan Semua Data Tabungan Anggota")
                 ->setKeywords("Data Tabungan");
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
        $excel->setActiveSheetIndex(0)->setCellValue('A1', "KARTU TABUNGAN ANGGOTA"); // Set kolom A1 dengan tulisan "DATA SISWA"
	    $excel->getActiveSheet()->mergeCells('A1:G1'); // Set Merge Cell pada kolom A1 sampai E1
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
	    $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1
	    $anggota = $this->general_m->get_data_anggota($id);
	    $excel->setActiveSheetIndex(0)->setCellValue('A2', "ID Anggota : "); // Set kolom A1 dengan tulisan "DATA SISWA"
	    $excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); //
	    $excel->setActiveSheetIndex(0)->setCellValue('C2', "AG0000"); // Set kolom A1 dengan tulisan "DATA SISWA"
	    $excel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	    $excel->setActiveSheetIndex(0)->setCellValue('D2', $anggota->id);
	    $excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	    $excel->getActiveSheet()->mergeCells('A2:B2'); // Set Merge Cell pada kolom A1 sampai E1
	    $excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

	    $excel->setActiveSheetIndex(0)->setCellValue('A3', "Nama Anggota : "); // Set kolom A1 dengan tulisan "DATA SISWA"
	    $excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	    $excel->setActiveSheetIndex(0)->setCellValue('C3', $anggota->nama); // Set kolom A1 dengan tulisan "DATA SISWA"
	    $excel->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	    $excel->getActiveSheet()->mergeCells('A3:B3'); // Set Merge Cell pada kolom A1 sampai E1
	    $excel->getActiveSheet()->mergeCells('C3:E3');
	    $excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12); // Set font size 15 untuk kolom A1

	    $excel->setActiveSheetIndex(0)->setCellValue('A4', "Alamat Anggota : "); // Set kolom A1 dengan tulisan "DATA SISWA"
	    $excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	    $excel->setActiveSheetIndex(0)->setCellValue('C4', $anggota->alamat); // Set kolom A1 dengan tulisan "DATA SISWA"
	    $excel->getActiveSheet()->mergeCells('A4:B4'); // Set Merge Cell pada kolom A1 sampai E1
	    $excel->getActiveSheet()->mergeCells('C4:G4');
	    $excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(12); // Set font size 15 untuk kolom A1
	    // Buat header tabel nya pada baris ke 3
	if(!empty($rows)) {
	    $excel->setActiveSheetIndex(0)->setCellValue('A5', "NO"); // Set kolom A3 dengan tulisan "NO"
	    $excel->setActiveSheetIndex(0)->setCellValue('B5', "TGL BAYAR"); // Set kolom B3 dengan tulisan "NIS"
	    $excel->setActiveSheetIndex(0)->setCellValue('C5', "KREDIT"); // Set kolom C3 dengan tulisan "NAMA"
	    $excel->setActiveSheetIndex(0)->setCellValue('D5', "DEBET"); // Set kolom D3 dengan tulisan "JENIS KELAMIN"
	    $excel->setActiveSheetIndex(0)->setCellValue('E5', "SALDO"); // Set kolom E3 dengan tulisan "ALAMAT"
	    $excel->setActiveSheetIndex(0)->setCellValue('F5', "KETERANGAN"); // Set kolom E3 dengan tulisan "ALAMAT"
	    $excel->setActiveSheetIndex(0)->setCellValue('G5', "USER"); // Set kolom E3 dengan tulisan "ALAMAT"
	    // Apply style header yang telah kita buat tadi ke masing-masing kolom header
	    $excel->getActiveSheet()->getStyle('A5')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('B5')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('C5')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('D5')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('E5')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('F5')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('G5')->applyFromArray($style_col);
	    // Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
	    $no = 1; // Untuk penomoran tabel, di awal set dengan 1
	    $numrow = 6; // Set baris pertama untuk isi tabel adalah baris ke 4
	    $saldo = 0;

	    foreach ($rows as $data) {
				$tgl_bayar      = explode(' ', $data['tgl']);
				$txt_tanggal    = jin_date_ina($tgl_bayar[0],'p');
				
				$saldo = ($saldo - $data['kredit']) + $data['debet'];
				$jenis = $this->db->get_where('jns_simpan', array('id' => $data['transaksi']))->row();
	     // Lakukan looping pada variabel siswa
	      $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no++);
	      $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $txt_tanggal);
	      $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $data["kredit"]);
	      $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $data["debet"]);
	      $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $saldo);
	      $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $jenis->jns_simpan);
	      $excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $data["user"]);
	      
	      // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
	      $excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_row);
	      $excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
	      $excel->getActiveSheet()->getStyle('C'.$numrow)->applyFromArray($style_row);
	      $excel->getActiveSheet()->getStyle('D'.$numrow)->applyFromArray($style_row);
	      $excel->getActiveSheet()->getStyle('E'.$numrow)->applyFromArray($style_row);
	      $excel->getActiveSheet()->getStyle('F'.$numrow)->applyFromArray($style_row);
	      $excel->getActiveSheet()->getStyle('G'.$numrow)->applyFromArray($style_row);
	      
	      $no++; // Tambah 1 setiap kali looping
	      $numrow++; // Tambah 1 setiap kali looping
	    }
	    // Set width kolom
	    $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); // Set width kolom A
	    $excel->getActiveSheet()->getColumnDimension('B')->setWidth(12); // Set width kolom B
	    $excel->getActiveSheet()->getColumnDimension('C')->setWidth(12); // Set width kolom C
	    $excel->getActiveSheet()->getColumnDimension('D')->setWidth(12); // Set width kolom D
	    $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30); // Set width kolom E
	    $excel->getActiveSheet()->getColumnDimension('F')->setWidth(40); // Set width kolom E
	    $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20); // Set width kolom E
	    
	    // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
	    $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
	    // Set orientasi kertas jadi LANDSCAPE
	    $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	    $excel->getActiveSheet()->getStyle('A6:G19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    // Set judul file excel nya
	    $excel->getActiveSheet(0)->setTitle("Laporan Data Tabungan");
	    $excel->setActiveSheetIndex(0);
	    // Proses file excel
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header('Content-Disposition: attachment; filename="Data Tabungan.xlsx"'); // Set nama file excel nya
	    header('Cache-Control: max-age=0');
	    $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	    $write->save('php://output');
	}else {
	    	'Tidak ada transaksi';
	    }
  }

}