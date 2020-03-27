<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Ubah Pic-SABILAL MUHTADIN</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>icon.ico" type="image/x-icon" />
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- bootstrap 3.0.2 -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- font Awesome -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/AdminLTE.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/custome.css" rel="stylesheet" type="text/css" />
	<script src="http://code.jquery.com/jquery-2.2.1.min.js"></script>
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

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
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
					<h3 class="box-title">Ubah Pic</h3>
				</div>
				<div class="box-body">
					<?php if($tersimpan == 'Y') { ?>
					<div class="box-body">
						<div class="alert alert-success alert-dismissable">
							<i class="fa fa-check"></i>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							Profile pic tersimpan.
						</div>
					</div>
					<?php } ?>

					<?php if($tersimpan == 'N') { ?>
					<div class="box-body">
						<div class="alert alert-danger alert-dismissable">
							<i class="fa fa-warning"></i>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							Profile pic tidak berhasil disimpan.<br>
							<?php echo $error; ?>
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<?php
						echo '<div class="">';
						$photo_w = 3 * 20;
						$photo_h = 4 * 20;
						if($row->file_pic == '') {
							$photo ='<img src="'.base_url().'assets/theme_admin/img/photo.jpg" alt="default" width="'.$photo_w.'" height="'.$photo_h.'" />';
						} else {
							$photo= '<img src="'.base_url().'uploads/anggota/' . $row->file_pic . '" alt="Foto" width="'.$photo_w.'" height="'.$photo_h.'" />';
						}
						echo $photo;
						echo '</div>';

						echo form_open_multipart('');

						echo form_upload('userfile');

						// submit
						$data = array(
							'name' 		=> 'submit',
							'id' 			=> 'submit',
							'class' 		=> 'btn btn-primary',
							'value'		=> 'true',
							'type'	 	=> 'submit',
							'content' 	=> 'Ubah Pic'
							);
						echo '<br>';
						echo form_button($data);

						echo form_close();

						?>
					</div>
				</div><!-- /.box-body -->
			</div>
		</div>
	</div>

</div>


	<!-- jQuery 2.0.2 -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/jquery.min.js"></script>
	<!-- Bootstrap -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/bootstrap.min.js" type="text/javascript"></script>


<script type="text/javascript">

</script>
<script>
$(document).ready(function(){
$(".preloader").fadeOut();
})
</script>
</body>
</html>