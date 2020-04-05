<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Transfer - SABILAL MUHTADIN</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>icon.ico" type="image/x-icon" />
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- bootstrap 3.0.2 -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- font Awesome -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/AdminLTE.css" rel="stylesheet" type="text/css" />

	
	<link href="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
	
	<!-- <?php foreach($js_files as $file) { ?>
		<script src="<?php echo $file; ?>"></script>
	<?php } ?> -->

	<link href="<?php echo base_url(); ?>assets/theme_admin/css/custome.css" rel="stylesheet" type="text/css" />

	<script src="http://code.jquery.com/jquery-2.2.1.min.js"></script>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
	<style type="text/css">
		.preloader {
		  position: fixed;
		  top: 0;
		  left: 0;
		  width: 100%;
		  height: 100%;
		  z-index: 9999;
		  background-color: #fff;
		}
		.preloader .loading {
		  position: absolute;
		  left: 50%;
		  top: 50%;
		  transform: translate(-50%,-50%);
		  font: 14px arial;
		}
	</style>

</head>
<body>
<div class="preloader">
	  <div class="loading">
	    <img src="<?php echo base_url(); ?>assets/loading.gif" width="80">
	    <p>Harap Tunggu</p>
	  </div> 
	</div>
<div class="container">

	<?php $this->load->view('themes/member_menu_v'); ?>

	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid box-primary">
				<div class="box-header">
					<h3 class="box-title">Transfer</h3>
				</div>
				<?php echo form_open(''); ?>
				<div class="box-body">

					<?php if($tersimpan == 'N') { ?>
					<div class="box-body">
						<div class="alert alert-danger alert-dismissable">
							<i class="fa fa-warning"></i>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							Transfer gagal terkirim, silahkan periksa kembali dan ulangi.
						</div>
					</div>
					<?php } ?>

					<div class="form-group">					
						<?php 
						$attr_form = 'id_anggota';
						$attr_form_label = 'Dari Rekening';
						$options = array(
							'Biasa'		=> 'Kamal',
							'Darurat'	=> 'Rais',
							'Barang'	=> 'Hanum'
							);
						echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
								<div>';
						echo form_dropdown($attr_form, $options, 'Hanum', 'id="'.$attr_form.'" class="form-control" style="width: 500px;"');
						echo '</div>';
						?>
					</div>

					<div class="form-group">
						<?php 
						$attr_form = 'jenis_id';
						$attr_form_label = 'Tabungan';
						$options = array(
							'Biasa'		=> 'Kamal',
							'Darurat'	=> 'Rais',
							'Barang'	=> 'Hanum'
							);
						echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
								<div>';
						echo form_dropdown($attr_form, $options, 'Hanum', 'id="'.$attr_form.'" class="form-control" style="width: 500px;"');
						echo '</div>';
						?>
					</div>

					<div class="form-group">
						<?php
						$data = array(
							'name'       => 'nominal',
							'id'			=> 'nominal',
							'class'		=> 'form-control',
							'value'      => '',
							'maxlength'  => '255',
							'style'      => 'width: 475px'
							);
						echo form_label('Nominal', 'nominal');
						echo form_input($data);
						echo form_error('nominal', '<p style="color: red;">', '</p>');
						?>
					</div>

					<div class="form-group">
						<?php 
						$attr_form = 'anggota_id';
						$attr_form_label = 'Ke Rekening';
						echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
								<div>';
						echo form_dropdown($attr_form, $anggota_id, set_value($attr_form, ''), 'id="'.$attr_form.'" class="form-control" style="width: 500px;"');
						echo '</div>';
						?>
					</div>

					<!-- <div class="form-group">
						<?php
						$attr_form = 'lama_ags';
						$attr_form_label = 'Tabungan';
						echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
								<div>';
						echo form_dropdown($attr_form, $lama_ags, set_value($attr_form, ''), 'id="'.$attr_form.'" class="form-control" style="width: 100px;" ');
						echo '</div><div id="div_lama_ags"></div>';
						?>
					</div> -->
					
				</div><!-- /.box-body -->
				<div class="box-footer">
					<?php
					// submit
					$data = array(
						'name' 		=> 'submit',
						'id' 		=> 'submit',
						'class' 	=> 'btn btn-primary',
						'value'		=> 'true',
						'type'	 	=> 'submit',
						'content' 	=> 'Transfer'
						);
					echo form_button($data);

					echo form_close();
					?>	
				</div>
				<?php echo form_close(); ?>
			</div><!-- box-primary -->
		</div><!-- col -->
	</div><!-- row -->

</div>


	<!-- jQuery 2.0.2 -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/jquery.min.js"></script>
	<!-- Bootstrap -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table-id-ID.js" type="text/javascript"></script>


<script type="text/javascript">
	$(function() {
		$('#nominal').on('change keyup paste', function() {
			var n = parseInt($(this).val().replace(/\D/g, ''), 10);
			$(this).val(number_format(n, 0, '', '.'));
		});
		$('#jenis').on('change', function() {
			oc_lama_ags();
		});
		oc_lama_ags();

		$('#jenis, #nominal, #lama_ags').on('change', function() {
			simulasikan();
		});


	});

	function simulasikan() {
		var jenis = $('#jenis').val();
		var var_nominal = $('#nominal').val();
		var var_lama_ags = $('#lama_ags').val();
		$.ajax({
			url: '<?php echo site_url('member/simulasi')?>',
			type: 'POST',
			dataType: 'html',
			data: {'nominal': var_nominal, 'lama_ags': var_lama_ags, 'jenis': jenis}
		})
		.done(function(result) {
			$('#div_simulasi').html(result);
			console.log("success");
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}

	function oc_lama_ags() {
		var jenis = $('#jenis').val();
		if(jenis == 'Darurat') {
			$('#lama_ags').hide();
			$('#div_lama_ags').html('<input value="1 bln" disabled="disabled" class="form-control" style="width: 35px;">');
			$('#div_lama_ags').show();
		} else {
			$('#div_lama_ags').html('');
			$('#div_lama_ags').hide;
			$('#lama_ags').show();
		}		
	}

</script>

<script>
$(document).ready(function(){
$(".preloader").fadeOut();
})
</script>
</body>
</html>