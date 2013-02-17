<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<?php $this->load->view('includes/head3'); ?>

	<script type="text/javascript">
		$(document).ready(function() {
			$('#rpt1').hide();// cambios
			$('#acumulado').on({
				'click':function(e){
					$('#rpt1').html('');
					$('#rpt2').html('');
					//i-nbaltodanov
					$('#select_acumulado1').prop('selectedIndex',0);
					$('#select_acumulado2').prop('selectedIndex',0);
					$('#select_acumulado3').prop('selectedIndex',0);
					//f-nbaltodanov					
					$('#divranking').hide();
					$('#divacumulado').fadeOut('slow').fadeIn('slow');
					$('#divranking').hide();
					$('#rpt1').hide().empty();//cambios
				}
			});//fin del on

			$('#ranking').on({
				'click':function(e){
					$('#rpt1').html('');
					$('#rpt2').html('');

					//i-nbaltodanov
					$('#select_ranking1').prop('selectedIndex',0);
					$('#select_ranking2').prop('selectedIndex',0);
					//f-nbaltodanov
					$('#divacumulado').hide();
					$('#divranking').fadeOut('slow').fadeIn('slow');
					$('#divacumulado').hide();
					//i-nbaltodanov
					$('#rpt1').hide().empty();//cambios
					//f-nbaltodanov
					
				}
			});//fin del on

		});// fin del onready
	</script>	
	<title><?php echo $titulo; ?></title>
</head>
<body>
	<div class="container">
		<header class="page-header">
			<div align="center">
				<h2> <?php echo $subtitulo ?> </h2>
			</div>
		</header>
	</div>
	<div class="container">
		<ul class="thumbnails">
			<li class="span6">
						 <a href="#" class="thumbnail" id="acumulado"><button class="btn btn-large btn-block btn-primary" type="button">Acumulado</button></a>
			
			</li>
			<li class="span6">
				 <a href="#" class="thumbnail" id="ranking"><button class="btn btn-large btn-block btn-primary" type="button">Ranking</button></a>	
				
			</li>
			
		</ul>
	</div>
	<div class="container" id="rspta">
		<div class="" id="divacumulado" align="center" style="display: none;">
			<ul class="thumbnails">
				<li class="span4">
					<select name="select_acumulado1" id="select_acumulado1" class="span4">
						<?php echo $cmb_grupo; ?>
					</select>
				</li>
				<li class="span4">
					<select name="select_acumulado2" id="select_acumulado2" class="span4">
						<option value="0">Seleccione consecionario</option>
						<!--<?php echo $cmb_consecionario; ?>-->
					</select>
				</li>
				<li class="span4">
					<select name="select_acumulado3" id="select_acumulado3" class="span4">
						<?php echo $cmb_periodo; ?>
					</select>
				</li>
			</ul>
		</div>
		<div class="" id="divranking" align="center" style="display: none;">
			<ul class="thumbnails">
				<li class="span6">
					<select name="select_ranking1" id="select_ranking1" class="span4">
						<?php echo $cmb_grupo; ?>
					</select>
					
				</li>
				<li class="span6">
					<select name="select_ranking2" id="select_ranking2" class="span4">
						<option value="0">Seleccione consecionario</option>
						<!--<?php echo $cmb_consecionario; ?>-->
					</select>
				</li>
			
			</ul>
			
		</div>
	</div>

	<div id = "rpt1" class="container">
	</div>
	<div id = "rpt2" class="container">
	</div>
    <div id = "rpt4" class="container"></div>
	<footer>
        
		<div class="container">
			<div class="row-fluid">
                        <div class="span4"></div>
                        <div class="span4"></div>
                        <div class="span4">
                            <div align="right">
                               <?php echo anchor('Home/home/volver', 'Volver',array('class'=>'btn btn-danger')); ?>
                            </div>
                        </div>
                        
             </div>
		</div>
	</footer>
</body>
</html>