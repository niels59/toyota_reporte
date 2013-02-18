//$( '#form_login' ).parsley();
$(document).ready(function() {
    $('#rpt2').hide();
    // cambios para reporte acumalado-ranking
    $('#acu_tableexcel').val(0);
    
    $('#nick').on({
        'change':function(){
            $(this).val($(this).val().toUpperCase());
        }
    },{
        'blur':function(){
            $(this).val($(this).val().toUpperCase());
        }
    },{	
        'keyup':function(){
            $(this).val($(this).val().toUpperCase());		
        }
    });//fin del on

    $('#select_acumulado1').on({
        change:function()
        {
            elegido = $(this).val();
            $.post(base_url + 'home/get_consecionarios',{
                'elegido':elegido
            },function(resp){
                $('#select_acumulado2').empty();
                $('#select_acumulado2').append(resp);
            });
        }
    });

    $('#select_ranking1').on({
        change:function()
        {
            elegido = $(this).val();
            $.post(base_url + 'home/get_consecionarios2',{
                'elegido':elegido
            },function(resp){
                $('#select_ranking2').empty();
                $('#select_ranking2').append(resp);
            });
        }
    });

    $('#select_acumulado3,#select_acumulado2,#select_acumulado1').on({
        change:function()
        {	//console.log($('#select_acumulado2').val());
			
            if($('#select_acumulado1').val()!= 0  && $('#select_acumulado2').val()!= 0 && $('#select_acumulado3').val()!= 0)
            {
                elegido1 = $('#select_acumulado1').val();
                elegido2 = $('#select_acumulado2').val();
                elegido3 = $('#select_acumulado3').val();
                dato = {
                    'elegido1':elegido1,
                    'elegido2':elegido2,
                    'elegido3':elegido3
                };
                // i- nbaltodanov 17-02-2013
                $.post(base_url + 'home/get_reporte1',dato,function(resp){
                    //$('#rpt1').empty();
                    $('#rpt1').show().html(resp);// cambios
                //alert(dato);
                }).complete(function(){
                    //para reporte Acumulado y Ranking
                    $('#acu_tableexcel').val($("<div>").append( $("#rpt1").eq(0).clone()).html());
                    $('#acu_ptableexcel').val($("<div>").append( $("#rpt1").eq(0).clone()).html());
                    $('#acu_name_excel').val($('#select_acumulado2').val()+'-'+$('#select_acumulado3').val());
                });
                // f- nbaltodanov
            }
            //cambios 	
            if($('#select_acumulado1').val()== 0  && $('#select_acumulado2').val()== 0 && $('#select_acumulado3').val()== 0){
                $('#rpt1').hide().empty();// cambios
            }			
	

        }
    });

    $('#select_ranking2,#select_ranking1').on({
        change:function()
        {              //console.log($('#select_acumulado2').val());
							   
            if($('#select_ranking1').val()!= 0  && $('#select_ranking2').val()!= 0 )
            {
                elegido1 = $('#select_ranking1').val();
                elegido2 = $('#select_ranking2').val();                                                 
                dato = {
                    'elegido1':elegido1,
                    'elegido2':elegido2
                };

                $.post(base_url + 'home/get_reporte2',dato,function(resp){
                    //$('#rpt2').empty();
                    $('#rpt2').show().html(resp);// cambios
                //alert(dato);
                }).complete(function(){
                    //para reporte Acumulado y Ranking
                    $('#acu_tableexcel').val($("<div>").append( $("#rpt2").eq(0).clone()).html());
                    $('#acu_ptableexcel').val($("<div>").append( $("#rpt2").eq(0).clone()).html());
                    $('#acu_name_excel').val($('#select_ranking2').val());
                });
            }
            //cambios           
            if($('#select_ranking1').val()== 0  && $('#select_ranking2').val()== 0){
                $('#rpt2').hide().empty();// cambios
            }                                             
        }
    });

    $('#select_resumen').on({
        change:function()
        {              //console.log($('#select_acumulado2').val());
                                               
            if($('#select_resumen').val()!= 0 )
            {
                elegido1 = $('#select_resumen').val();
                                                                                                                             
                dato = {
                    'elegido1':elegido1
                };

                $.post(base_url + 'home/get_resumen',dato,function(resp){
                    $('#rpt4').empty();
                    $('#rpt4').show().html(resp);// cambios
                //alert(dato);
                });
            }
            //cambios           
            if($('#select_resumen').val()== 0){
                $('#rpt4').hide().empty();// cambios
            }                                             
                

        }
    });
    //para reporte Acumulado y Ranking
    //acu_form_excel
    $('#btn_acu_tableexcel').on({
        click:function(e){
            if($('#acu_tableexcel').val()!=0){
                $('#rep_form_excel').submit();
               
            }
            e.preventDefault();
        }
    });
    
        $('#btn_acu_ptableexcel').on({
        click:function(e){
            if($('#acu_ptableexcel').val()!=0){
                $('#rep_gprint_excel').submit();
               
            }
            e.preventDefault();
        }
    });
    

	
});// fin del onready