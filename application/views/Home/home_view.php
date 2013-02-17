<!doctype html>
<html>
	<head>

		<meta charset="utf-8">
		<?php $this->load->view('includes/head2'); ?>
		<title><?php echo $titulo; ?> </title>
	</head>
	<body>
		<div class="container">
			<div class="page-header" align="center">
				  <h1><?php echo $titulo; ?> <small><?php echo $subtext; ?></small></h1>
			</div>
			
			<?php echo form_open('Home/home/procesos',array('class'=>'form-signin','id'=>'form_login','name'=>'form_login','data-validate'=>'parsley'));?>
				<div class="control-group">
					<label for="nick" class="control-label">Usuario</label>
					<div class="controls">
				        <?php echo form_error('nick'); ?>
				        <input type="text" name="nick" id="nick" class="input-block-level" placeholder="Nick" value="<?php echo set_value('nick'); ?>" data-required="true">
					</div>
				</div>
				<div class="control-group">
					<label for="contrasena" class="control-label">Contraseña</label>
					<div class="controls">
						<?php echo form_error('contrasena'); ?>
				       <input type="password" name="contrasena" id="contrasena" class="input-block-level" placeholder="Password" data-required="true" data-error-message="Este campo es requerido" data-trigger="keyup">
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">Ingresar</button>
					</div>
				</div>
				  
				  
			<?php echo form_close(); ?>
		</div>
		
	</body>
</html>