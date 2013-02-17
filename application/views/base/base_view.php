<!DOCTYPE html>
<html>
<head>
	<?php $this->load->view('includes/head4'); ?>
	<meta charset="utf-8">
	<title><?php echo $titulo ?> </title>
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
			<section>
					<article>
						<ul class="thumbnails">
							<li class="span6">
								<div class="control-group info">	
									 <?php
										$string1='id="select_base" class="span6"'; 
										echo form_dropdown('select_base', $tipo_base, '0',$string1);  ?>
								</div>	
							</li>
							<li class="span6">
								<div class="control-group info">
									<select name="select_periodo" id="select_periodo" class="span6">
									<?php echo $tipo_trimestre;
										/*$string2='class="span4"';
										echo form_dropdown('Trimestre', $tipo_trimestre, '0',$string2);*/  ?>
									</select>	
								</div>	
							</li>
							<!-- <li class="span4">
								<div class="control-group info">	 
										<?php
										/*$string3='class="span4"';
										echo form_dropdown('name', $tipo_anio, '0',$string3);  */?>
								</div>	
							</li> -->
							
						</ul>
					</article>					   
			</section>					
	 </div>
     	 <div class="container">
	 	<?php echo anchor('Home/home/export_excel', '<button class="btn btn-success">Exportar a Excel</button>', array('id'=>'toexcel')); ?>
	 	<?php echo anchor('Home/home/print_excel', '<button class="btn btn-success">Imprimir a Excel</button>', array('id'=>'ptoexcel')); ?>
	 	<!-- <button id="toexcel">Exportar a Excel</button> -->
	 </div>
	<div class="container" id="rspta2">
		
	</div> 	
	<footer>
		<div class="container">
			<div class="row-fluid">
                        <div class="span4"></div>
                        <div class="span4"></div>
                        <div class="span4">
                            <div align="right">
                              <div align="right">
                               <?php echo anchor('Home/home/volver', 'Volver',array('class'=>'btn btn-danger')); ?>
                            </div>
                            </div>
                        </div>
                        
             </div>
		</div>
		<?php echo form_open('Home/home/export_excel',array('id'=>'form_excel')); ?>
			<input type="hidden" name="name_excel" id="name_excel" value="0">
			<input type="hidden" name="tableexcel" id="tableexcel" value="0">

		<?php echo form_close(); ?>
		<?php echo form_open('Home/home/print_excel',array('id'=>'print_excel')); ?>
			<input type="hidden" name="pname_excel" id="pname_excel" value="0">
			<input type="hidden" name="ptableexcel" id="ptableexcel" value="0">

		<?php echo form_close(); ?>
	</footer>
</body>
</html>