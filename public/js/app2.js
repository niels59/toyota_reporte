$(document).ready(function() {
    //alert('hola');
    $('#rpta1').hide();
    $('#rpta2').hide();
    $('#rspta2').hide();
    $('#btn_general').on({
        click:function(e){
            //alert('alert');
            //console.log($(this).prop('href'));
            $.post($(this).prop('href'), {}, function(resp) {
                $('#rpta1').html('');
                $('#rpta1').show().append(resp);				
                //$('#gtableexcel').val(resp);
                $('#gtableexcel').val($("<div>").append( $("#rpta1").eq(0).clone()).html());
                //$('#gptableexcel').val(resp);
                $('#gptableexcel').val($("<div>").append( $("#rpta1").eq(0).clone()).html());
                
            });
			
            e.preventDefault();
        }
    });
	
    $('#btn_resumen').on({
        click:function(e){
            //alert('alert');
            //console.log($(this).prop('href'));
            $.post($(this).prop('href'), {}, function(resp) {
                $('#rpta1').html('').show().append(resp);
                $('#gtableexcel').val($("<div>").append( $("#rpta1").eq(0).clone()).html());
                $('#gptableexcel').val($("<div>").append( $("#rpta1").eq(0).clone()).html());
                                               
            });
                               
            e.preventDefault();
        }
    });


    //--- para base total
    $('#select_base,#select_periodo').on({
        change:function(e){
            //alert('hola');
            if($('#select_base').val()!=0 && $('#select_periodo').val()!=0){
                //alert(base_url);	
                $.post(base_url+'home/get_base', {
                    'base':$('#select_base').val(),
                    'periodo':$('#select_periodo').val()
                }, function(resp) {
                    $('#rspta2').show().html(resp);
                    $('#tableexcel').val($("<div>").append( $("#table_excel1").eq(0).clone()).html());
                    $('#name_excel').val($('#select_base').val());
                    $('#ptableexcel').val($("<div>").append( $("#table_excel1").eq(0).clone()).html());
                    $('#pname_excel').val($('#select_base').val());
                });
					
            }else{
                $('#rspta2').hide();
            }
        }
    });//fin de on para importacion de visor de excel

    //------para exportar tabla html a excel

    $('#toexcel').on({
        click:function(e){
            //alert('hola');		
            if($('#tableexcel').val()!=0){				
                $('#form_excel').submit();
            }
            e.preventDefault();
        }
    })//prueba excel

    $('#ptoexcel').on({
        click:function(e){
            //alert('hola');		  
            if($('#tableexcel').val()!=0){				
                $('#print_excel').submit();
            }
            e.preventDefault();
        }
    })//prueba excel

    //--- General-Resumen
    $('#gtoexcel').on({
        click:function(e){
            //alert('hola');		
            if($('#gtableexcel').val()!=0){			º	
                $('#gform_excel').submit();
            }
            e.preventDefault();
        }
    })//prueba excel

    $('#gptoexcel').on({
        click:function(e){
            //alert('hola');		
            if($('#gtableexcel').val()!=0){				
                $('#gprint_excel').submit();
            }
            e.preventDefault();
        }
    })//prueba excel


});
