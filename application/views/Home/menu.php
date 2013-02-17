<!doctype html>
<html>
    <head>
       
       <?php if($this->simple_sessions->check_sess('nick')){ ?> 
    	<?php $this->load->view('includes/head1'); ?>
        <script type="text/javascript">
            var disable=<?php echo $this->simple_sessions->get_value('idperfil');?>;
            $(document).ready(function() {
                if(disable==2){
                $('#base').prop('href','#');
                $('#resumen').prop('href','#');
            }
            });
            
        </script>   
        <meta charset=utf-8>
        <title><?php echo $titulo; ?> </title>
    </head>
    <body>
        <div class="page-header" align="center">
            <h1>Bienvenido <?php echo $this->simple_sessions->get_value('nick'); ?></h1>
                <small><?php echo $subtitulo;?></small>
        </div>

        <div class="container">      
                <ul class="thumbnails">
                <li class="span4">
                    
                    <?php echo anchor('Home/home/reporte', 'Reporte',array('class'=>'btn btn-large btn-block btn-primary')); ?>
                    <!-- <h3>Basic marketing site</h3>
                    <p>Featuring a hero unit for a primary message and three supporting elements.</p> -->
                </li>
                <?php if($this->simple_sessions->get_value('idperfil')==1){?>
                <li class="span4">
                    <?php echo anchor('Home/home/resumen', 'Resumen',array('class'=>'btn btn-large btn-block btn-primary')); ?>
                    <!-- <h3>Fluid layout</h3>
                    <p>Uses our new responsive, fluid grid system to create seamless liquid layout.</p> -->
                </li>
                <li class="span4">
                   <?php echo anchor('Home/home/base', 'Base',array('class'=>'btn btn-large btn-block btn-primary')); ?>
                    <!-- <h3>Starter template</h3>
                    <p>A barebones HTML document with all the Bootstrap CSS and javascript included.</p> -->
                </li>
                <?php }else{ ?>
                <li class="span4">
                    <?php echo anchor('', 'Resumen',array('class'=>'btn btn-large btn-block btn-primary','disabled'=>'true','id'=>'resumen')); ?>
                    <!-- <h3>Fluid layout</h3>
                    <p>Uses our new responsive, fluid grid system to create seamless liquid layout.</p> -->
                </li>
                <li class="span4">
                   <?php echo anchor('', 'Base',array('class'=>'btn btn-large btn-block btn-primary','disabled'=>'true','id'=>'base')); ?>
                    <!-- <h3>Starter template</h3>
                    <p>A barebones HTML document with all the Bootstrap CSS and javascript included.</p> -->
                </li>
                <?php } ?>
            </ul>        
        	<div class="container">
                   <div class="row-fluid">
                        <div class="span4"></div>
                        <div class="span4"></div>
                        <div class="span4">
                            <div align="right">
                                <?php echo anchor('Home/home/salir', 'Salir',array('class'=>'btn btn-danger')); ?>
                            </div>
                        </div>
                        
                   </div>
            </div>
        </div>
        
    </body>
</html>
<?php }else{

    redirect('Home/home', 'refresh');
} ?>