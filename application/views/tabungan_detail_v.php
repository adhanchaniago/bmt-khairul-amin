<!-- Styler -->
<style type="text/css">
	.panel * {
		font-family: "Arial","​Helvetica","​sans-serif";
	}
	.fa {
		font-family: "FontAwesome";
	}
	.datagrid-header-row * {
		font-weight: bold;
	}
	.messager-window * a:focus, .messager-window * span:focus {
		color: blue;
		font-weight: bold;
	}
	.daterangepicker * {
		font-family: "Source Sans Pro","Arial","​Helvetica","​sans-serif";
		box-sizing: border-box;
	}
	.glyphicon	{font-family: "Glyphicons Halflings"}
	.form-control {
		height: 20px;
		padding: 4px;
	}	

	th {
		text-align: center;
		background: #3c8dbc;
		height: 30px;
		border-width: 1px;
		border-style: solid;
		color :#ffffff;
	}
</style>

<!-- buaat tanggal sekarang -->
<!-- menu atas -->
<?php
echo '<a href="'.site_url().'tabungan" class="btn btn-sm btn-danger" title="Kembali"> <i class="glyphicon glyphicon-circle-arrow-left"></i> Kembali </a>

<a href="'.site_url('cetak_tabungan_detail').'/cetak/'.$master_id.'"  title="Cetak Detail" class="btn btn-sm btn-success" target="_blank"> <i class="glyphicon glyphicon-print"></i> Cetak Detail
</a>';
?>
<p></p>
<!-- detail data anggota -->
<div class="box box-solid box-primary">
	<div class="box-header" title="Detail Simpanan" data-toggle="" data-original-title="Detail Simpanan">
		<h3 class="box-title"> Detail Simpanan </h3> 
		<div class="box-tools pull-right">
			<button class="btn btn-primary btn-xs" data-widget="collapse">
				<i class="fa fa-minus"></i>
			</button>
		</div>
	</div>
	<div class="box-body">
		<table style="font-size: 13px; width:100%">
			<tr>
				<td style="width:10%; text-align:center;">
					<?php
					$photo_w = 3 * 30;
					$photo_h = 4 * 30;
					if($data_anggota->file_pic == '') {
						echo '<img src="'.base_url().'assets/theme_admin/img/photo.jpg" alt="default" width="'.$photo_w.'" height="'.$photo_h.'" />';
					} else {
						echo '<img src="'.base_url().'uploads/anggota/' . $data_anggota->file_pic . '" alt="Foto" width="'.$photo_w.'" height="'.$photo_h.'" />';
					}
					?>
				</td> 
				<td>
					<table style="width:100%">
						<tr>
							<td><label class="text-green">Data Anggota</label></td>
						</tr>
						<?php //echo 'AG' . sprintf('%04d', $row_pinjam->anggota_id) . '' ?>
						<tr>
							<td> ID Anggota</td>
							<td> : </td>
							<td> <?php echo $data_anggota->identitas; ?></td>
						</tr>
						<tr>
							<td> Nama Anggota </td>
							<td> : </td>
							<td> <?php echo $data_anggota->nama; ?></td>
						</tr>
						<tr>
							<td> Tempat, Tanggal Lahir  </td>
							<td> : </td>
							<td> <?php echo $data_anggota->tmp_lahir .', '. jin_date_ina ($data_anggota->tgl_lahir); ?></td>
						</tr>
						<tr>
							<td> Kota Tinggal</td> 
							<td> : </td>
							<td> <?php echo $data_anggota->kota; ?></td>
						</tr>
					</table>
				</td>		
			</tr>
		</table>
	</div>

	<div class="box box-solid bg-light-blue">
		
	</div>
</div>

<label class="text-green"> Detail Transaksi Setoran :</label>
<table  class="table table-bordered">
	<tr class="header_kolom">
		<th style="width:5%; vertical-align: middle " rowspan="2"> No. </th>
		<th style="width:20%; vertical-align: middle" rowspan="2"> Tanggal Bayar</th>
		<th style="width:15%; vertical-align: middle" colspan="2">Mutasi</th>
		<th style="width:15%; vertical-align: middle" rowspan="2"> Saldo</th>
		<th style="width:20%; vertical-align: middle" rowspan="2"> Ket  </th>
		<th style="width:10%; vertical-align: middle" rowspan="2"> User  </th>
	</tr>
	<tr class="header_kolom">
		<th style="width:15%; vertical-align: middle"> Kredit </th>
		<th style="width:15%; vertical-align: middle"> Debet </th>
	</tr>
	<?php 

	$mulai=1;
	$no=1;
	$saldo = 0;
	// $jml_denda = 0;

	if(empty($simpanan)) {
		echo '<code> Tidak Ada Transaksi Pembayaran</code>';
	} else {

		foreach ($simpanan as $row) {
			if(($no % 2) == 0) {
				$warna="#FAFAD2";
			} else {
				$warna="#FFFFFF";
			}

			$saldo = ($saldo - $row['kredit']) + $row['debet'];
			$jenis = $this->db->get_where('jns_simpan', array('id' => $row['transaksi']))->row();

			echo '
			<tr bgcolor='.$warna.'>
				<td class="h_tengah">'.$no++.'</td>
				<td class="h_tengah">'.$row["tgl"].'</td>
				<td class="h_tengah">'.$row["kredit"].'</td>
				<td class="h_tengah">'.$row["debet"].'</td>
				<td class="h_tengah">'.$saldo.'</td>
				<td class="h_tengah">'.$jenis->jns_simpan.'</td>
				<td class="h_tengah">'.$row["user"].'</td>
			</tr>';
		}
		// echo '<tr bgcolor="#eee">
		// 	<td class="h_tengah" colspan="5"><strong>Jumlah</strong></td>
		// 	<td class="h_kanan"><strong>'.number_format(nsi_round($jml_tot)).'</strong></td>
		// 	<td class="h_kanan"><strong>'.number_format(nsi_round($jml_denda)).'</strong></td>
		// 	<td></td>
		// 	</tr>';
		echo '</table>';
	}