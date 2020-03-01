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
</style>

<div class="box box-solid box-primary">
	<div class="box-header">
		<h3 class="box-title">Data Simpanan Anggota</h3>
		<div class="box-tools pull-right">
			<button class="btn btn-primary btn-sm" data-widget="collapse">
				<i class="fa fa-minus"></i>
			</button>
		</div>
	</div>
	<div class="box-body">
		<table>
			<tr>
				<td> Pilih ID Anggota </td>
				<td>
					<form id="fmCari">
					 <input id="anggota_id" name="anggota_id" value="" style="width:200px; height:25px" class="">
					 </form>
				</td>	
				<td>
					<a href="javascript:void(0);" id="btn_filter" class="easyui-linkbutton" iconCls="icon-search" plain="false" onclick="doSearch()">Lihat Laporan</a>
					<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="false" onclick="cetak()">Cetak Laporan</a>
					<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-clear" plain="false" onclick="clearSearch()">Hapus Filter</a>
				</tr>
			</table>
		</div>
</div>

<div class="box box-primary">
	<div class="box-body">
	<p></p>
	<table  class="table table-bordered">
		<tr class="header_kolom">
			<th style="width:5%; vertical-align: middle; text-align:center" > No. </th>
			<th style="width:5%; vertical-align: middle; text-align:center">Photo</th>
			<th style="width:10%; vertical-align: middle; text-align:center">ID Anggota</th>
			<th style="width:25%; vertical-align: middle; text-align:center">Nama Lengkap</th>
			<th style="width:15%; vertical-align: middle; text-align:center">Jenis Kelamin</th>
			<th style="width:20%; vertical-align: middle; text-align:center">Aktif Keanggotaan</th>
			<th style="width:23%; vertical-align: middle; text-align:center"> Pilihan </th>
		</tr>
	<?php
	
	$no = $offset + 1;
	$mulai=1;
	if (!empty($data_anggota)) {

		foreach ($data_anggota as $row) {
		if(($no % 2) == 0) {
			$warna="#EEEEEE"; } 
		else {
			$warna="#FFFFFF";}

		//photo
		$photo_w = 1 * 20;
		$photo_h = 2 * 20;
		if($row->file_pic == '') {
			$photo ='<img src="'.base_url().'assets/theme_admin/img/photo.jpg" alt="default" width="'.$photo_w.'" height="'.$photo_h.'" />';
		} else {
			$photo= '<img src="'.base_url().'uploads/anggota/' . $row->file_pic . '" alt="Foto" width="'.$photo_w.'" height="'.$photo_h.'" />';
		}

		//jk
		if ($row->jk == "L") {
			$jk="Laki-Laki";
		} else {
			$jk="Perempuan"; 
		}

		if ($row->aktif == "Y") {
			$aktif="Aktif";
		} else {
			$aktif="Tidak Aktif"; 
		}

		//jabatan
		if ($row->jabatan_id == "1") {
			$jabatan="Pengurus";
		} else {
			$jabatan="Anggota"; 
		}
		// AG'.sprintf('%04d', $row->id).'
	 	echo '
			<tr bgcolor='.$warna.' >
				<td class="h_tengah" style="vertical-align: middle "> '.$no++.' </td>
				<td class="h_tengah" style="vertical-align: middle "> '.$photo.'</td>
				<td>A000'.$row->id.'</td>
				<td>'.strtoupper($row->nama).'</td>
				<td>'.$jk.'</td>
				<td>'.$aktif.'</td>
				<td><a href="'.site_url('tabungan_detail').'/index/' . $row->id . '" title="Detail"> <i class="fa fa-search"></i> Detail </a></td>
			</tr>';
			}
		echo '</table>
		<div class="box-footer">'.$halaman.'</div>';
	} else {
		echo '<tr>
					<td colspan="9" >
						<code> Tidak Ada Data <br> </code>
					</td>
				</tr>';
			}
	?>
</div>
</div>
	
<script type="text/javascript">
	$(document).ready(function() {

	<?php 
		if(isset($_REQUEST['anggota_id'])) {
			echo 'var anggota_id = "'.$_REQUEST['anggota_id'].'";';
		} else {
			echo 'var anggota_id = "";';
		}
		echo '$("#anggota_id").val(anggota_id);';
	?>

		$('#anggota_id').combogrid({
			panelWidth:300,
			url: '<?php echo site_url('lap_shu_anggota/list_anggota'); ?>' ,
			idField:'id',
			valueField:'id',
			textField:'id_nama',
			mode:'remote',
			fitColumns:true,
			columns:[[
				{field:'photo',title:'Photo',align:'center',width:5},
				{field:'id',title:'ID', hidden: true},
				{field:'id_nama', title:'IDNama', hidden: true},
				{field:'kode_anggota', title:'ID', align:'center', width:15},
				{field:'nama',title:'Nama Anggota',align:'left',width:20}
			]]
		});




}); // ready

function clearSearch(){
	window.location.href = '<?php echo site_url("lap_kas_anggota"); ?>';
}

function cetak () {
	<?php 
		if(isset($_REQUEST['anggota_id'])) {
			echo 'var anggota_id = "'.$_REQUEST['anggota_id'].'";';
		} else {
			echo 'var anggota_id = $("#anggota_id").val();';
		}
	?>
	var win = window.open('<?php echo site_url("lap_kas_anggota/cetak_laporan/?anggota_id=' + anggota_id +'"); ?>');
	if (win) {
		win.focus();
	} else {
		alert('Popup jangan di block');
	}
	//$('#fmCari').attr('action', '<?php echo site_url('lap_kas_anggota/cetak_laporan'); ?>');
	//$('#fmCari').submit();
}

function doSearch() {
	$('#fmCari').submit();
}
</script>