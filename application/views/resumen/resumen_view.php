<!doctype html>
<html>
	<head>
		<?php $this->load->view('includes/head4'); ?>
		<meta charset="utf-8">
		<title> <?php echo $titulo; ?> </title>
	</head>
	<body>
		<div class="container" >
			<header class="page-header" align="center">
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
							<?php echo anchor('Home/home/resumen_general','General', array('class'=>'btn btn-large btn-block btn-primary','id'=>'btn_general')); ?>
							<!-- <a class="thumbnail" href="#">
								<img src="http://placehold.it/360x240" alt="">
							</a>
							<h3>Basic marketing site</h3>
							<p>Featuring a hero unit for a primary message and three supporting elements.</p> -->
						</li>
						<li class="span6">
							<?php echo anchor('Home/home/resumen_resumen','Resumen', array('class'=>'btn btn-large btn-block btn-primary','id'=>'btn_resumen')); ?>
							<!-- <a class="thumbnail" href="#">
								<img src="http://placehold.it/360x240" alt="">
							</a>
							<h3>Fluid layout</h3>
							<p>Uses our new responsive, fluid grid system to create seamless liquid layout.</p> -->
						</li>
						
					</ul>
				</article>					   
		</section>
        <section>
				<!-- <pre> -->
				<?php echo $excel1; ?>
				 <div class="container">
	 	<?php echo anchor('Home/home/export_excel', '<button class="btn btn-success">Exportar a Excel</button>', array('id'=>'gtoexcel')); ?>
	 	<?php echo anchor('Home/home/print_excel', '<button class="btn btn-success">Imprimir a Excel</button>', array('id'=>'gptoexcel')); ?>
	 	<!-- <button id="toexcel">Exportar a Excel</button> -->
	 </div>
				<div id="rpta2">
					

				</div>
				<!-- </pre> -->
			</section>				
	 </div>
	 <div class="container">
			<section>
				<div id="rpta1" style="overflow-top:scroll; overflow-x:scroll; overflow-y:hidden; white-space:nowrap;"></div>
                
                <!--<div id="rpt4" class="container" style="width:100%"></div>-->
			</section>
		</div>
	<footer>
		<?php echo form_open('Home/home/gexport_excel',array('id'=>'gform_excel')); ?>
			<input type="hidden" name="gname_excel" id="gname_excel" value="0">
			<input type="hidden" name="gtableexcel" id="gtableexcel" value="0">

		<?php echo form_close(); ?>
		<?php echo form_open('Home/home/gprint_excel',array('id'=>'gprint_excel')); ?>
			<input type="hidden" name="gpname_excel" id="gpname_excel" value="0">
			<input type="hidden" name="gptableexcel" id="gptableexcel" value="0">

		<?php echo form_close();?>
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
	</footer>			
	</body>
</html>