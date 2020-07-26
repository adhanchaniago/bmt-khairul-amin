<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simpanan extends OperatorController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('simpanan_m');
		$this->load->model('general_m');
	}	

	public function index() {
		$this->data['judul_browser'] = 'Transaksi';
		$this->data['judul_utama'] = 'Transaksi';
		$this->data['judul_sub'] = 'Setoran Tunai <a href="'.site_url('simpanan/import').'" class="btn btn-sm btn-success">Import Data</a>';

		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/default/easyui.css';
		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/icon.css';
		$this->data['js_files'][] = base_url() . 'assets/easyui/jquery.easyui.min.js';

		#include tanggal
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap_date_time/css/bootstrap-datetimepicker.min.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/bootstrap-datetimepicker.min.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/locales/bootstrap-datetimepicker.id.js';

		#include daterange
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/daterangepicker/daterangepicker-bs3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/daterangepicker/daterangepicker.js';

		//number_format
		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		$this->data['kas_id'] = $this->simpanan_m->get_data_kas();
		$this->data['jenis_id'] = $this->general_m->get_id_simpanan();

		$this->data['isi'] = $this->load->view('simpanan_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	function import() {
		$this->data['judul_browser'] = 'Import Data';
		$this->data['judul_utama'] = 'Import Data';
		$this->data['judul_sub'] = 'Setoran <a href="'.site_url('simpanan').'" class="btn btn-sm btn-success">Kembali</a>';

		$this->load->helper(array('form'));

		if($this->input->post('submit')) {
			$config['upload_path']   = FCPATH . 'uploads/simpanan/';
			$config['allowed_types'] = '*';
			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('import_simpanan')) {
				$this->data['error'] = $this->upload->display_errors();
			} else {
				// ok uploaded
				$file = $this->upload->data();
				$this->data['file'] = $file;

				$this->data['lokasi_file'] = $file['full_path'];

				$this->load->library('excel');

				// baca excel
				$objPHPExcel = PHPExcel_IOFactory::load($file['full_path']);
				$no_sheet = 1;
				$header = array();
				$data_list_x = array();
				$data_list = array();
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					if($no_sheet == 1) { // ambil sheet 1 saja
						$no_sheet++;
						$worksheetTitle = $worksheet->getTitle();
						$highestRow = $worksheet->getHighestRow(); // e.g. 10
						$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
						$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

						$nrColumns = ord($highestColumn) - 64;
						//echo "File ".$worksheetTitle." has ";
						//echo $nrColumns . ' columns';
						//echo ' y ' . $highestRow . ' rows.<br />';

						$data_jml_arr = array();
						//echo 'Data: <table width="100%" cellpadding="3" cellspacing="0"><tr>';
						for ($row = 1; $row <= $highestRow; ++$row) {
						   //echo '<tr>';
							for ($col = 0; $col < $highestColumnIndex; ++$col) {
								$cell = $worksheet->getCellByColumnAndRow($col, $row);
								$val = $cell->getValue();
								$kolom = PHPExcel_Cell::stringFromColumnIndex($col);
								if($row === 1) {
									if($kolom == 'A') {
										$header[$kolom] = 'Nama';
									} else {
										$header[$kolom] = $val;
									}
								} else {
									$data_list_x[$row][$kolom] = $val;
								}
							}
						}
					}
				}

				$no = 1;
				foreach ($data_list_x as $data_kolom) {
					if((@$data_kolom['A'] == NULL || trim(@$data_kolom['A'] == '')) ) { continue; }
					foreach ($data_kolom as $kolom => $val) {
						if(in_array($kolom, array('E', 'K', 'L')) ) {
							$val = ltrim($val, "'");
						}
						$data_list[$no][$kolom] = $val;
					}
					$no++;
				}

				//$arr_data = array();
				$this->data['header'] = $header;
				$this->data['values'] = $data_list;
				/*
				$data_import = array(
					'import_anggota_header'		=> $header,
					'import_anggota_values' 	=> $data_list
					);
				$this->session->set_userdata($data_import);
				*/
			}
		}


		$this->data['isi'] = $this->load->view('simpanan_import_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	function import_db() {
		if($this->input->post('submit')) {
			$this->load->model('Member_m','member', TRUE);
			$data_import = $this->input->post('val_arr');
			if($this->member->import_db($data_import)) {
				$this->session->set_flashdata('import', 'OK');
			} else {
				$this->session->set_flashdata('import', 'NO');
			}
			//hapus semua file di temp
			$files = glob('uploads/temp/*');
			foreach($files as $file){ 
				if(is_file($file)) {
					@unlink($file);
				}
			}
			redirect('simpanan/import');
		} else {
			$this->session->set_flashdata('import', 'NO');
			redirect('simpanan/import');
		}
	}

	function import_batal() {
		//hapus semua file di temp
		$files = glob('uploads/temp/*');
		foreach($files as $file){ 
			if(is_file($file)) {
				@unlink($file);
			}
		}
		$this->session->set_flashdata('import', 'BATAL');
		redirect('simpanan/import');
	}

	function list_anggota() {
		$q = isset($_POST['q']) ? $_POST['q'] : '';
		$data   = $this->general_m->get_data_anggota_ajax($q);
		$i	= 0;
		$rows   = array(); 
		foreach ($data['data'] as $r) {
			if($r->file_pic == '') {
				$rows[$i]['photo'] = '<img src="'.base_url().'assets/theme_admin/img/photo.jpg" alt="default" width="30" height="40" />';
			} else {
				$rows[$i]['photo'] = '<img src="'.base_url().'uploads/anggota/' . $r->file_pic . '" alt="Foto" width="30" height="40" />';
			}
			$rows[$i]['id'] = $r->id;
			$rows[$i]['kode_anggota'] = 'AG'.sprintf('%04d', $r->id) . '<br>' . $r->identitas;
			$rows[$i]['nama'] = $r->nama;
			$rows[$i]['kota'] = $r->kota;		
			$i++;
		}
		//keys total & rows wajib bagi jEasyUI
		$result = array('total'=>$data['count'],'rows'=>$rows);
		echo json_encode($result); //return nya json
	}

	function get_anggota_by_id() {
		$id = isset($_POST['anggota_id']) ? $_POST['anggota_id'] : '';
		$r   = $this->general_m->get_data_anggota($id);
		$out = '';
		$photo_w = 3 * 30;
		$photo_h = 4 * 30;
		if($r->file_pic == '') {
			$out ='<img src="'.base_url().'assets/theme_admin/img/photo.jpg" alt="default" width="'.$photo_w.'" height="'.$photo_h.'" />'
			.'<br> ID : '.'AG' . sprintf('%04d', $r->id) . '';
		} else {
			$out = '<img src="'.base_url().'uploads/anggota/' . $r->file_pic . '" alt="Foto" width="'.$photo_w.'" height="'.$photo_h.'" />'
			.'<br> ID : '.'AG' . sprintf('%04d', $r->id) . '';
		}
		echo $out;
		exit();
	}

	function ajax_list() {
		/*Default request pager params dari jeasyUI*/
		$offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$sort  = isset($_POST['sort']) ? $_POST['sort'] : 'tgl_transaksi';
		$order  = isset($_POST['order']) ? $_POST['order'] : 'desc';
		$kode_transaksi = isset($_POST['kode_transaksi']) ? $_POST['kode_transaksi'] : '';
		$cari_simpanan = isset($_POST['cari_simpanan']) ? $_POST['cari_simpanan'] : '';
		$cari_nama = isset($_POST['cari_nama']) ? $_POST['cari_nama'] : ''; 
		$tgl_dari = isset($_POST['tgl_dari']) ? $_POST['tgl_dari'] : '';
		$tgl_sampai = isset($_POST['tgl_sampai']) ? $_POST['tgl_sampai'] : '';
		$search = array('kode_transaksi' => $kode_transaksi, 
			'cari_simpanan' => $cari_simpanan,
			'cari_nama' => $cari_nama,
			'tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai);
		$offset = ($offset-1)*$limit;
		$data   = $this->simpanan_m->get_data_transaksi_ajax($offset,$limit,$search,$sort,$order);
		$i	= 0;
		$rows   = array(); 

		foreach ($data['data'] as $r) {
			$tgl_bayar = explode(' ', $r->tgl_transaksi);
			$txt_tanggal = jin_date_ina($tgl_bayar[0]);
			$txt_tanggal .= ' - ' . substr($tgl_bayar[1], 0, 5);		

			//array keys ini = attribute 'field' di view nya
			$anggota = $this->general_m->get_data_anggota($r->anggota_id);  
			$nama_simpanan = $this->general_m->get_jns_simpanan($r->jenis_id);  

			$rows[$i]['id'] = $r->id;
			$rows[$i]['id_txt'] ='TRD' . sprintf('%05d', $r->id) . '';
			$rows[$i]['tgl_transaksi'] = $r->tgl_transaksi;
			$rows[$i]['tgl_transaksi_txt'] = $txt_tanggal;
			$rows[$i]['anggota_id'] = $r->anggota_id;
			//$rows[$i]['anggota_id_txt'] = 'AG' . sprintf('%04d', $r->anggota_id);
			$rows[$i]['anggota_id_txt'] = $anggota->identitas;
			$rows[$i]['nama'] = $anggota->nama;
			$rows[$i]['jenis_id'] = $r->jenis_id;
			$rows[$i]['jenis_id_txt'] =$nama_simpanan->jns_simpan;
			$rows[$i]['jumlah'] = number_format($r->jumlah);
			$rows[$i]['ket'] = $r->keterangan;
			$rows[$i]['user'] = $r->user_name;
			$rows[$i]['kas_id'] = $r->kas_id;
			$rows[$i]['nama_penyetor'] = $r->nama_penyetor;
			$rows[$i]['no_identitas'] = $r->no_identitas;
			$rows[$i]['alamat'] = $r->alamat;
			$rows[$i]['detail'] ='<a href="'.site_url('cetak_simpanan').'/cetak/' . $r->id . '"  title="Cetak Bukti Transaksi" target="_blank"> <i class="glyphicon glyphicon-print"></i> Nota </a></p>';
			$i++;
		}
		//keys total & rows wajib bagi jEasyUI
		$result = array('total'=>$data['count'],'rows'=>$rows);
		echo json_encode($result); //return nya json
	}

	function get_jenis_simpanan() {
		$id = $this->input->post('jenis_id');
		$jenis_simpanan = $this->general_m->get_id_simpanan();
		foreach ($jenis_simpanan as $row) {
			if($row->id == $id) {
				echo number_format($row->jumlah);
			}
		}
		exit();
	}

	public function create() {
		if(!isset($_POST)) {
			show_404();
		}
		if($this->simpanan_m->create()){
			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil disimpan </div>'));
		}else
		{
			echo json_encode(array('ok' => false, 'msg' => '<div class="text-red"><i class="fa fa-ban"></i> Gagal menyimpan data, pastikan nilai lebih dari <strong>0 (NOL)</strong>. </div>'));
		}
	}

	public function update($id=null) {
		if(!isset($_POST)) {
			show_404();
		}
		if($this->simpanan_m->update($id)) {
			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil diubah </div>'));
		} else {
			echo json_encode(array('ok' => false, 'msg' => '<div class="text-red"><i class="fa fa-ban"></i>  Maaf, Data gagal diubah, pastikan nilai lebih dari <strong>0 (NOL)</strong>. </div>'));
		}

	}
	public function delete() {
		if(!isset($_POST))	 {
			show_404();
		}
		$id = intval(addslashes($_POST['id']));
		if($this->simpanan_m->delete($id))
		{
			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil dihapus </div>'));
		} else {
			echo json_encode(array('ok' => false, 'msg' => '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Data gagal dihapus </div>'));
		}
	}


	function cetak_laporan() {
		$simpanan = $this->simpanan_m->lap_data_simpanan();
		if($simpanan == FALSE) {
			//redirect('simpanan');
			echo 'DATA KOSONG<br>Pastikan Filter Tanggal dengan benar.';
			exit();
		}

		$tgl_dari = $_REQUEST['tgl_dari']; 
		$tgl_sampai = $_REQUEST['tgl_sampai']; 

		$this->load->library('Pdf');
		$pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->set_nsi_header(TRUE);
		$pdf->AddPage('L');
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
		'.$pdf->nsi_box($text = '<span class="txt_judul">Laporan Data Simpanan Anggota <br></span>
			<span> Periode '.jin_date_ina($tgl_dari).' - '.jin_date_ina($tgl_sampai).'</span> ', $width = '100%', $spacing = '0', $padding = '1', $border = '0', $align = 'center').'
		<table width="100%" cellspacing="0" cellpadding="3" border="1" border-collapse= "collapse">
		<tr class="header_kolom">
			<th class="h_tengah" style="width:5%;" > No. </th>
			<th class="h_tengah" style="width:8%;"> No Transaksi</th>
			<th class="h_tengah" style="width:7%;"> Tanggal </th>
			<th class="h_tengah" style="width:25%;"> Nama Anggota </th>
			<th class="h_tengah" style="width:18%;"> Jenis Simpanan </th>
			<th class="h_tengah" style="width:13%;"> Jumlah  </th>
			<th class="h_tengah" style="width:10%;"> User </th>
		</tr>';

		$no =1;
		$jml_simpanan = 0;
		foreach ($simpanan as $row) {
			$anggota= $this->simpanan_m->get_data_anggota($row->anggota_id);
			$jns_simpan= $this->simpanan_m->get_jenis_simpan($row->jenis_id);

			$tgl_bayar = explode(' ', $row->tgl_transaksi);
			$txt_tanggal = jin_date_ina($tgl_bayar[0],'p');

			$jml_simpanan += $row->jumlah;

			// '.'AG'.sprintf('%04d', $row->anggota_id).'
			$html .= '
			<tr>
				<td class="h_tengah" >'.$no++.'</td>
				<td class="h_tengah"> '.'TRD'.sprintf('%05d', $row->id).'</td>
				<td class="h_tengah"> '.$txt_tanggal.'</td>
				<td class="h_kiri"> '.$anggota->identitas.' - '.$anggota->nama.'</td>
				<td> '.$jns_simpan->jns_simpan.'</td>
				<td class="h_kanan"> '.number_format($row->jumlah).'</td>
				<td> '.$row->user_name.'</td>
			</tr>';
		}
		$html .= '
		<tr>
			<td colspan="5" class="h_tengah"><strong> Jumlah Total </strong></td>
			<td class="h_kanan"> <strong>'.number_format($jml_simpanan).'</strong></td>
		</tr>
		</table>';
		$pdf->nsi_html($html);
		$pdf->Output('trans_sp'.date('Ymd_His') . '.pdf', 'I');
	} 

	public function export(){
		$anggota = $this->db->get('tbl_anggota')->result_array();
		$jenis = $this->db->get('jns_simpan')->result_array();
		$kas = $this->db->get_where('nama_kas_tbl', array('tmpl_simpan' => 'Y'))->result_array();
	    include APPPATH.'libraries/phpexcel/PHPExcel.php';

	    $excel = new PHPExcel();
	    $excel->getProperties()->setCreator('My Notes Code')
	                 ->setLastModifiedBy('My Notes Code')
	                 ->setTitle("Data Simpanan")
	                 ->setSubject("Simpanan")
	                 ->setDescription("Laporan Semua Data Simpanan Anggota")
	                 ->setKeywords("Data Simpanan");
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
		//Sheet 1
	    $excel->setActiveSheetIndex(0)->setCellValue('A1', "ID Anggota");
	    $excel->setActiveSheetIndex(0)->setCellValue('B1', "ID Jenis Simpanan");
	    $excel->setActiveSheetIndex(0)->setCellValue('C1', "ID KAS");
	    $excel->setActiveSheetIndex(0)->setCellValue('D1', "Jumlah");
	    $excel->setActiveSheetIndex(0)->setCellValue('E1', "Keterangan");
	    $excel->getActiveSheet()->getStyle('A1')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('B1')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('C1')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('D1')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('E1')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	    $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	    $excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	    $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	    $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	    
	    
	    $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
	    $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	    $excel->getActiveSheet()->getStyle('A6:G19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $excel->getActiveSheet(0)->setTitle("Data Setoran Tunai");
	    $excel->setActiveSheetIndex(0);
	    $myWorkSheet = new PHPExcel_Worksheet($excel, 'Keterangan');
		$excel->addSheet($myWorkSheet, 1);

		// Sheet 2
		$numrow = 3;
	    foreach ($anggota as $data) {
	    	$excel->setActiveSheetIndex(1)->setCellValue('A1', "ID ANGGOTA");
		    $excel->getActiveSheet()->mergeCells('A1:B1');
		    $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE);
		    $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
		    $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $excel->setActiveSheetIndex(1)->setCellValue('A2', "ID");
	    	$excel->setActiveSheetIndex(1)->setCellValue('B2', "Nama");
	    	$excel->getActiveSheet()->getStyle('A2')->applyFromArray($style_col);
	   	 	$excel->getActiveSheet()->getStyle('B2')->applyFromArray($style_col);
			$excel->setActiveSheetIndex(1)->setCellValue('A'.$numrow, $data["id"]);
			$excel->setActiveSheetIndex(1)->setCellValue('B'.$numrow, $data["nama"]);
			$excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	    	$excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
			$numrow++;
	    }

	    $num = 3;
	    foreach ($jenis as $data) {
	    	$excel->setActiveSheetIndex(1)->setCellValue('D1', "ID JENIS SIMPANAN");
		    $excel->getActiveSheet()->mergeCells('D1:E1');
		    $excel->getActiveSheet()->getStyle('D1')->getFont()->setBold(TRUE);
		    $excel->getActiveSheet()->getStyle('D1')->getFont()->setSize(15);
		    $excel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $excel->setActiveSheetIndex(1)->setCellValue('D2', "ID");
	    	$excel->setActiveSheetIndex(1)->setCellValue('E2', "Jenis Simpan");
	    	$excel->getActiveSheet()->getStyle('D2')->applyFromArray($style_col);
	   	 	$excel->getActiveSheet()->getStyle('E2')->applyFromArray($style_col);
			$excel->setActiveSheetIndex(1)->setCellValue('D'.$num, $data["id"]);
			$excel->setActiveSheetIndex(1)->setCellValue('E'.$num, $data["jns_simpan"]);
			$excel->getActiveSheet()->getStyle('D'.$num)->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('E'.$num)->applyFromArray($style_row);
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	    	$excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
			$num++;
	    }

	    $row = 3;
	    foreach ($kas as $data) {
	    	$excel->setActiveSheetIndex(1)->setCellValue('G1', "ID KAS");
		    $excel->getActiveSheet()->mergeCells('G1:H1');
		    $excel->getActiveSheet()->getStyle('G1')->getFont()->setBold(TRUE);
		    $excel->getActiveSheet()->getStyle('G1')->getFont()->setSize(15);
		    $excel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $excel->setActiveSheetIndex(1)->setCellValue('G2', "ID");
	    	$excel->setActiveSheetIndex(1)->setCellValue('H2', "Nama Kas");
	    	$excel->getActiveSheet()->getStyle('G2')->applyFromArray($style_col);
	   	 	$excel->getActiveSheet()->getStyle('H2')->applyFromArray($style_col);
			$excel->setActiveSheetIndex(1)->setCellValue('G'.$row, $data["id"]);
			$excel->setActiveSheetIndex(1)->setCellValue('H'.$row, $data["nama"]);
			$excel->getActiveSheet()->getStyle('G'.$row)->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H'.$row)->applyFromArray($style_row);
			$excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	    	$excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
			$row++;
	    }
	    //Setting
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header('Content-Disposition: attachment; filename="import_setoran.xlsx"');
	    header('Cache-Control: max-age=0');
	    $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	    $write->save('php://output');
	}
}