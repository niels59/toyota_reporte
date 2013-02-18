<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        //$this->simple_sessions->destroy_sess();
        $this->load->library('form_validation');
        $this->load->library('table');
        $this->load->model('Home/home_model', 'orm');
    }

    public function index() { //$this->simple_sessions->destroy_sess();
        $data['titulo'] = 'Bienvenid@ a Reportes TOYOTA';
        $data['subtext'] = 'Ingrese sus datos';
        $this->load->view('Home/home_view', $data);
    }

    public function procesos() {
        $this->form_validation->set_rules('nick', 'nick', 'trim|required|xss_clean|upper');
        $this->form_validation->set_rules('contrasena', 'contrasena', 'trim|required|min_length[5]|xss_clean|md5');
        $this->form_validation->set_error_delimiters('<div class="label label-important">', '</div>');
        $this->form_validation->set_message('required', 'Este campo es requerido');
        if ($this->form_validation->run() === FALSE) {
            $this->index();
        } else {
            $this->login();
        }
    }

    public function login() {
        if ($this->orm->login_usuario()) {
            $dato['titulo'] = 'Menu de Reportes';
            $dato['subtitulo'] = 'Eliga que tipo de Reporte desea ver';
            $this->load->view('Home/menu', $dato);
            if (!$this->simple_sessions->check_sess('nick')) {
                $this->salir();
            }
        } else {
            $this->simple_sessions->destroy_sess();
            $this->index();
        }
    }

    public function salir() {
        $this->simple_sessions->destroy_sess();
        redirect(base_url(), 'refresh');
    }

    public function reporte() {
        if (!$this->simple_sessions->check_sess('nick')) {
            //$this->simple_sessions->destroy_sess();
            $this->salir();
        }
        $data['titulo'] = 'Reporte-General';
        $data['subtitulo'] = 'Reporte General';
        $data['cmb_grupo'] = $this->get_grupos();
        $data['cmb_periodo'] = $this->get_periodos();
        $this->load->view('reporte/reporte_view', $data);
    }

    public function resumen() {
        if (!$this->simple_sessions->check_sess('nick')) {
            $this->salir();
        }
        $data['titulo'] = "Resumen-Admin";
        $data['subtitulo'] = "Bienvenid@ Resumen-Admin";
        $this->load->view('resumen/resumen_view', $data);
    }

    public function base() {
        if (!$this->simple_sessions->check_sess('nick')) {
            $this->salir();
        }
        $data['titulo'] = 'Bases de Datos';
        $data['subtitulo'] = 'Aqui podra visualizar la data';
        $data['tipo_base'] = array('0' => 'Seleccione tipo de base',
            '1' => 'Lima',
            '2' => 'Provincias');
        $data['tipo_trimestre'] = $this->get_periodos();
        /* array('0'=>'Seleccione tipo de periodo',
          '1'=>'Trimestre I',
          '2'=>'Trimestre II',
          '3'=>'Trimestre III',
          '4'=>'Trimestre IV' 	);//vendra de bd ??
          /*$data['tipo_anio']=array('0'=>'Seleccione Año',
          '1'=>'2012',
          '2'=>'2013'
          );//vendra de bd años validos!! */
        $this->load->view('base/base_view', $data);
    }

    //function que visualiza hojas excel

    public function get_base() { // 36=>lima O 37=>provincias hojas excel 
        set_time_limit(0);
        $periodo = $this->input->post('periodo');
        require_once BASEPATH . 'libraries/excel_reader2.php';

        $excel_reader = new Spreadsheet_Excel_Reader('xls/' . $periodo . '.xls');

        $data1 = $excel_reader->dump($row_numbers = false, $col_letters = false, $sheet = 0, $table_class = 'ExcelTable2007', $id = 'table_excel1');
        $data2 = $excel_reader->dump($row_numbers = false, $col_letters = false, $sheet = 1, $table_class = 'ExcelTable2007', $id = 'table_excel1');
        $data = '';

        switch ($this->input->post('base')) {
            case '1':
                $data = $data1;
                /* $excel_reader->dump($row_numbers=false,$col_letters=false,$sheet=36,$table_class='ExcelTable2007',$id='table_excel1'); */
                break;
            case '2':
                $data = $data2;
                /* $excel_reader->dump($row_numbers=false,$col_letters=false,$sheet=37,$table_class='ExcelTable2007',$id='table_excel1'); */
                break;
            default:
                # code...
                break;
        }
        $this->output->set_content_type('text/html')->set_output($data);
    }

    public function export_excel() {
        $name_excel = '';
        if ($this->input->post('name_excel') == 1) {
            $name_excel = 'BASE_LIMA';
        } else {
            $name_excel = 'BASE_PPROVINCIA';
        }
        $data['excel_file'] = $this->input->post('tableexcel');
        $data['name_excel'] = $name_excel;
        //print_r($_POST);
        $this->load->view('Home/export_excel_view', $data);
    }

    public function print_excel() {
        $name_excel = '';
        if ($this->input->post('pname_excel') == 1) {
            $name_excel = 'BASE_LIMA';
        } else {
            $name_excel = 'BASE_PPROVINCIA';
        }
        $data['excel_file'] = $this->input->post('ptableexcel');
        $data['name_excel'] = $name_excel;
        //print_r($_POST);
        $this->load->view('Home/print_excel_view', $data);
    }

    //para resumen-general
    public function gexport_excel() {
        $name_excel = '';
        if ($this->input->post('gname_excel') == 0) {
            $name_excel = 'GENERAL';
        } else {
            $name_excel = 'BASE_PPROVINCIA';
        }
        $data['excel_file'] = $this->input->post('gtableexcel');
        $data['name_excel'] = $name_excel;
        //print_r($_POST);
        $this->load->view('Home/export_excel_view', $data);
    }

    public function gprint_excel() {
        $name_excel = '';
        if ($this->input->post('gpname_excel') == 0) {
            $name_excel = 'GENERAL';
        } else {
            $name_excel = 'BASE_PPROVINCIA';
        }
        $data['excel_file'] = $this->input->post('gptableexcel');
        $data['name_excel'] = $name_excel;
        //print_r($_POST);
        $this->load->view('Home/print_excel_view', $data);
    }
    // reporte acumulado-ranking
    
    public function acu_export_excel(){
       
       
        $data['excel_file'] = $this->input->post('acu_tableexcel');
        
        $data['name_excel'] = $this->input->post('acu_name_excel');
        //print_r($_POST);
        $this->load->view('Home/export_excel_view', $data);
        
    }
    public function rnk_print_excel(){
       
        $data['excel_file'] = $this->input->post('acu_ptableexcel');
        $data['name_excel'] = $this->input->post('acu_pname_excel');
        //print_r($_POST);
        $this->load->view('Home/print_excel_view', $data);
    }
    //fin reporte acumulado ranking
    
    public function volver() {
        if (!$this->simple_sessions->check_sess('nick')) {
            //$this->simple_sessions->destroy_sess();
            $this->salir();
        }
        $dato['titulo'] = 'Menu de Reportes';
        $dato['subtitulo'] = 'Eliga que tipo de Reporte desea ver';
        $this->load->view('Home/menu', $dato);
        //redirect('Home/home/', 'refresh');
    }

    public function get_grupos() {
        $str1 = "<option value='0' id='0'>Seleccione grupo</option>";
        $str = $this->orm->get_grupos();
        foreach ($str as $row) {
            $str1.="<option value='" . $row->NOMBRE . "' id='" . $row->VALOR . "'>" . $row->NOMBRE . "</option>";
        }
        return $str1;
    }

    public function get_consecionarios() {
        $str1 = "<option value='0' id='0'>Seleccione consecionario</option>";
        $str = $this->orm->get_consecionarios();
        foreach ($str as $row) {
            $str1.="<option value='" . $row->NOMBRE . "' id='" . $row->VALOR . "'>" . $row->NOMBRE . "</option>";
        }
        //return $str1;
        print_r($str1);
    }

    public function get_consecionarios2() {
        $str1 = "<option value='0' id='0'>Seleccione consecionario</option>";
        $str1.= "<option value='1' id='1'>Todos</option>";
        $str = $this->orm->get_consecionarios();
        foreach ($str as $row) {
            $str1.="<option value='" . $row->NOMBRE . "' id='" . $row->VALOR . "'>" . $row->NOMBRE . "</option>";
        }
        //return $str1;
        print_r($str1);
    }

    public function get_periodos() {
        $str1 = "<option value='0' id='0'>Seleccione periodo</option>";
        $str = $this->orm->get_periodos();
        foreach ($str as $row) {
            $str1.="<option value='" . $row->ANIO . $row->TRIMESTRE . "' id='" . $row->ANIO . $row->TRIMESTRE . "'>" . $row->ANIO . " - " . $row->TRIMESTRE . "</option>";
        }
        return $str1;
    }

    public function get_reporte1() {
        $gettabledata = $this->orm->get_reporte1();
        $gettabledata2 = $this->orm->get_reporte1_verificacion2();

        $contador = 0;
        $row_span = 0;

        $const = 0;
        $atrib1 = 0;
        $atrib2 = 0;
        $atrib3 = 0;
        $atrib4 = 0;
        $atrib5 = 0;
        $atrib6 = 0;
        $atrib7 = 0;
        $atrib8 = 0;
        $atrib9 = 0;
        $atrib10 = 0;
        $atrib11 = 0;
        $atrib12 = 0;
        $atrib13 = 0;
        $atrib14 = 0;
        $csiindex = 0;
        $dliindex = 0;
        $refferralindex = 0;
        $expcompra = 0;

        $atrib1_ = 0;
        $atrib2_ = 0;
        $atrib3_ = 0;
        $atrib4_ = 0;
        $atrib5_ = 0;
        $atrib6_ = 0;
        $atrib7_ = 0;
        $atrib8_ = 0;
        $atrib9_ = 0;
        $atrib10_ = 0;
        $atrib11_ = 0;
        $atrib12_ = 0;
        $atrib13_ = 0;
        $atrib14_ = 0;
        $csiindex_ = 0;
        $dliindex_ = 0;
        $refferralindex_ = 0;
        $expcompra_ = 0;

        $vendedor_aux = "";

//<img src='". base_url()."public/img/ATRIB1.jpg' style='image-orientation: -90;'
        $str="<meta charset='utf-8'>";
        $str.= "
		<table width='100%' id='trank' class='table_rpt1'>
        <thead>
        <tr>
        <th style='text-align:center;font-size:10px;background-color:#7f7f7f;color:#ffffff;border: solid 1px #000000;'>IT</th>
        <th style='text-align:center;background-color:#7f7f7f;color:#ffffff;border: solid 1px #000000;'>CLIENTE</th>
        <th style='text-align:center;background-color:#7f7f7f;color:#ffffff;border: solid 1px #000000;'>VENDEDOR</th>
        <th style='background-color:#ffffff;border: solid 1px #000000;'>CORTESIA Y AMABILIDAD</th>
        <th style='text-align:center;background-color:#ffffff;border: solid 1px #000000;'>EXPLICACION COMPLETA DE<br/>LAS CARACTERISTICAS DEL<br/>VEHICULO</th>
        <th style='text-align:center;border: solid 1px #000000;'>EXPLICACION COMPLETA DE<br/>PROGRAMAS DE<br/>FINANCIAMIENTO</th>
        <th style='text-align:center;border: solid 1px #000000;'>CALIFICACION FINAL DEL<br/>DESEMPEÑO DEL ASESOR<br/>DURANTE EL PROCESO DE VENTA</th>
        <th style='text-align:center;border: solid 1px #000000;'>LIMPIEZA DEL VEHICULO</th>
		<th style='text-align:center;border: solid 1px #000000;'>EXPLICACION DE CARACTERISTICAS<br/>Y CONTROLES DEL VEHICULO</th>
		<th style='text-align:center;border: solid 1px #000000;'>EXPLICACION DEL PROGRAMA DE<br/>MANTENIMIENTO Y GARANTIA</th>
		<th style='text-align:center;border: solid 1px #000000;'>INFORMACION RECIBIDA SOBRE<br/>LAS FACILIDADES DE SERVICIO Y<br/> REPUESTOS, ASI COMO EL<br/>HORARIO DE ATENCION DEL<br/>CONCESIONARIO</th>
		<th style='text-align:center;border: solid 1px #000000;'>ENTREGA DEL VEHICULO EN LA<br/>FECHA ACORDADA</th>
		<th style='text-align:center;border: solid 1px #000000;'>CUMPLIMIENTO DE COMPROMISOS<br/>HECHOS DURANTE LA VENTA</th>
		<th style='text-align:center;border: solid 1px #000000;'>SEGUIMIENTO DE LA COMPRA</th>
		<th style='text-align:center;border: solid 1px #000000;'>FACILIDAD / CONVENIENCIA DEL<br/>ESTACIONAMIENTO EN EL<br/>CONCESIONARIO</th>
		<th style='text-align:center;border: solid 1px #000000;'>LIMPIEZA Y APARIENCIA DE LAS<br/>INSTALACIONES DEL<br/>CONCESIONARIO</th>
		<th style='text-align:center;border: solid 1px #000000;border-top: solid 1px #000000;border-right: solid 2px #ff0000;border-bottom: solid 1px #000000;'>COMODIDAD DE LAS<br/>INSTALACIONES DEL<br/>CONCESIONARIO</th>
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff;border-top: solid 2px #ff0000;border-right: solid 1px #000000;border-bottom: solid 1px #000000;'>CSI INDEX</th>
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff;border-top: solid 2px #ff0000;border-right: solid 2px #ff0000;border-bottom: solid 1px #000000;'>CSI PROMEDIO</th>
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff;border: solid 1px #000000;'>DLI INDEX</th>
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff;border: solid 1px #000000;'>REFFERRAL INDEX</th>
		<th style='text-align:center;border: solid 1px #000000;'>  1  </th>
		<th style='text-align:center;border: solid 1px #000000;'>  2  </th>
		<th style='text-align:center;border: solid 1px #000000;'>  3  </th>
		<th style='text-align:center;border: solid 1px #000000;'>  4  </th>
		<th style='text-align:center;border: solid 1px #000000;'>  5  </th>
		<th style='text-align:center;border: solid 1px #000000;'>  #  </th>
		<th style='text-align:center;border: solid 1px #000000;'> CSI </th>
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff;border: solid 1px #000000;'>AUTO COMPRADO</th>
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff;border: solid 1px #000000;'>EXPERIENCIA DE COMPRA</th>
        </tr>
        </thead><tbody>";

        if (count($gettabledata->result()) > 0) {
            foreach ($gettabledata->result() as $row) {

                $contador = $contador + 1;
                $vendedor_aux = $row->VENDEDOR;

                $str.= "<tr>";
                $str.= "<td style='border: solid 1px #000000;'>" . $contador . "</td>";
                $str.= "<td style='border: solid 1px #000000;'>" . $row->CLIENTE . "</td>";
                $str.= "<td style='border: solid 1px #000000;'>" . $row->VENDEDOR . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P3C > 0 && $row->P3C < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P3C . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P3E > 0 && $row->P3E < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P3E . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P3G > 0 && $row->P3G < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P3G . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P3K > 0 && $row->P3K < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P3K . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P5A > 0 && $row->P5A < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P5A . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P5C > 0 && $row->P5C < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P5C . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P5D > 0 && $row->P5D < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P5D . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P5E > 0 && $row->P5E < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P5E . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P6 > 0 && $row->P6 < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P6 . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P8 > 0 && $row->P8 < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P8 . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P9 > 0 && $row->P9 < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P9 . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P11A > 0 && $row->P11A < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P11A . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;";
                if ($row->P11B > 0 && $row->P11B < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P11B . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;border-top: solid 1px #000000;border-right: solid 2px #ff0000;border-bottom: solid 1px #000000;";
                if ($row->P11C > 0 && $row->P11C < 3) {
                    $str.= "background-color:#ff0000;color:#ffffff";
                }
                $str.= "'>" . $row->P11C . "</td>";
                $str.= "<td style='text-align:center;border-right: solid 1px #000000;border-bottom: solid 1px #000000;'>" . $row->CSIINDEX . "</td>";
                $str.= "<td style='text-align:center;border-right: solid 2px #ff0000;border-bottom: solid 1px #000000;'>" . $row->CSIPROMEDIO . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->DLIINDEX . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->REFFERRALINDEX . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;width:40px'>" . $row->CAL1 . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->CAL2 . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->CAL3 . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->CAL4 . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->CAL5 . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->NRO . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->CSIINDEX_2 . "</td>";
                $str.= "<td style='text-align:center;background-color:#d7e4bc;border: solid 1px #000000;'>" . $row->MODELO_AUTO . "</td>";
                $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->P12 . "</td>";

                $str.= "</tr>";
            }
            $str.="<tr style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff;font-size:16px;'>";
            $str.="<td colspan=3 style='border: solid 1px #000000;font-weight:bold;'><div>INDICE POR ATRIBUTO</div></td>";

            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBA</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBB</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBC</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBD</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBE</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBF</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBG</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBH</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBI</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBJ</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBK</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBL</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>ATRIBM</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;border: solid 1px #000000;border-top: solid 1px #000000;border-right: solid 2px #ff0000;border-bottom: solid 1px #000000;'>ATRIBN</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;border-bottom: solid 2px #ff0000;' colspan=2>CSIINDEX</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>DLIINDEX</td>";
            $str.= "<td style='text-align:center;border: solid 1px #000000;font-weight:bold;'>REFFERRALINDEX</td>";
            $str.="<tr><td colspan=30><div><br/><br/></div></td></tr>";

            if (count($gettabledata2->result()) > 0) {
                foreach ($gettabledata2->result() as $row2) {

                    switch ($row2->CAL) {
                        case 1: $const = -50;
                            break;
                        case 2: $const = 0;
                            break;
                        case 3: $const = 50;
                            break;
                        case 4: $const = 80;
                            break;
                        case 5: $const = 100;
                            break;
                        default:
                            break;
                    }
                    $atrib1_ = $atrib1_ + $row2->ATRIB1;
                    $atrib2_ = $atrib2_ + $row2->ATRIB2;
                    $atrib3_ = $atrib3_ + $row2->ATRIB3;
                    $atrib4_ = $atrib4_ + $row2->ATRIB4;
                    $atrib5_ = $atrib5_ + $row2->ATRIB5;
                    $atrib6_ = $atrib6_ + $row2->ATRIB6;
                    $atrib7_ = $atrib7_ + $row2->ATRIB7;
                    $atrib8_ = $atrib8_ + $row2->ATRIB8;
                    $atrib9_ = $atrib9_ + $row2->ATRIB9;
                    $atrib10_ = $atrib10_ + $row2->ATRIB10;
                    $atrib11_ = $atrib11_ + $row2->ATRIB11;
                    $atrib12_ = $atrib12_ + $row2->ATRIB12;
                    $atrib13_ = $atrib13_ + $row2->ATRIB13;
                    $atrib14_ = $atrib14_ + $row2->ATRIB14;
                    $csiindex_ = $csiindex_ + $row2->CSIINDEX;
                    $dliindex_ = $dliindex_ + $row2->DLIINDEX;
                    $refferralindex_ = $refferralindex_ + $row2->REFFERRALINDEX;
                    $expcompra_ = $expcompra_ + $row2->EXPCOMPRA;

                    $atrib1 = $atrib1 + $row2->ATRIB1 * $const;
                    $atrib2 = $atrib2 + $row2->ATRIB2 * $const;
                    $atrib3 = $atrib3 + $row2->ATRIB3 * $const;
                    $atrib4 = $atrib4 + $row2->ATRIB4 * $const;
                    $atrib5 = $atrib5 + $row2->ATRIB5 * $const;
                    $atrib6 = $atrib6 + $row2->ATRIB6 * $const;
                    $atrib7 = $atrib7 + $row2->ATRIB7 * $const;
                    $atrib8 = $atrib8 + $row2->ATRIB8 * $const;
                    $atrib9 = $atrib9 + $row2->ATRIB9 * $const;
                    $atrib10 = $atrib10 + $row2->ATRIB10 * $const;
                    $atrib11 = $atrib11 + $row2->ATRIB11 * $const;
                    $atrib12 = $atrib12 + $row2->ATRIB12 * $const;
                    $atrib13 = $atrib13 + $row2->ATRIB13 * $const;
                    $atrib14 = $atrib14 + $row2->ATRIB14 * $const;
                    $csiindex = $csiindex + $row2->CSIINDEX * $const;
                    $dliindex = $dliindex + $row2->DLIINDEX * $const;
                    $refferralindex = $refferralindex + $row2->REFFERRALINDEX * $const;
                    $expcompra = $expcompra + $row2->EXPCOMPRA * $const;


                    $str.= "<tr><td colspan=3 style='text-align:right'>" . $row2->CAL . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB1 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB2 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB3 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB4 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB5 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB6 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB7 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB8 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB9 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB10 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB11 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB12 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB13 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->ATRIB14 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->CSIINDEX . "</td>";
                    $str.= "<td style='text-align:center'></td>";
                    $str.= "<td style='text-align:center'>" . $row2->DLIINDEX . "</td>";
                    $str.= "<td style='text-align:center'>" . $row2->REFFERRALINDEX . "</td>";
                    $str.= "<td></td>";
                    $str.= "<td></td>";
                    $str.= "<td></td>";
                    $str.= "<td></td>";
                    $str.= "<td></td>";
                    $str.= "<td></td>";
                    $str.= "<td></td>";
                    $str.= "<td></td>";
                    $str.= "<td style='text-align:center'>" . $row2->EXPCOMPRA . "</td></tr>";
                }
            }

            $str.="<tr><td colspan=30><div><br/><br/></div></td></tr>";

            $str.= "<tr><td colspan=3 style='text-align:rigth'></td>";
            $str.= "<td style='text-align:center'>" . $atrib1_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib2_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib3_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib4_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib5_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib6_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib7_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib8_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib9_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib10_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib11_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib12_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib13_ . "</td>";
            $str.= "<td style='text-align:center'>" . $atrib14_ . "</td>";
            $str.= "<td style='text-align:center'>" . $csiindex_ . "</td>";
            $str.= "<td style='text-align:center'></td>";
            $str.= "<td style='text-align:center'>" . $dliindex_ . "</td>";
            $str.= "<td style='text-align:center'>" . $refferralindex_ . "</td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td style='text-align:center'>" . $expcompra_ . "</td></tr>";


            //$valatrib1="";
            $val1 = round($atrib1 / $atrib1_, 1);
            if (strpos((string) $val1, ".") == 0) {
                $val1 = (string) $val1 . ".0";
            }
            $val2 = round($atrib2 / $atrib2_, 1);
            if (strpos((string) $val2, ".") == 0) {
                $val2 = (string) $val2 . ".0";
            }
            $val3 = round($atrib3 / $atrib3_, 1);
            if (strpos((string) $val3, ".") == 0) {
                $val3 = (string) $val3 . ".0";
            }
            $val4 = round($atrib4 / $atrib4_, 1);
            if (strpos((string) $val4, ".") == 0) {
                $val4 = (string) $val4 . ".0";
            }
            $val5 = round($atrib5 / $atrib5_, 1);
            if (strpos((string) $val5, ".") == 0) {
                $val5 = (string) $val5 . ".0";
            }
            $val6 = round($atrib6 / $atrib6_, 1);
            if (strpos((string) $val6, ".") == 0) {
                $val6 = (string) $val6 . ".0";
            }
            $val7 = round($atrib7 / $atrib7_, 1);
            if (strpos((string) $val7, ".") == 0) {
                $val7 = (string) $val7 . ".0";
            }
            $val8 = round($atrib8 / $atrib8_, 1);
            if (strpos((string) $val8, ".") == 0) {
                $val8 = (string) $val8 . ".0";
            }
            $val9 = round($atrib9 / $atrib9_, 1);
            if (strpos((string) $val9, ".") == 0) {
                $val9 = (string) $val9 . ".0";
            }
            $val10 = round($atrib10 / $atrib10_, 1);
            if (strpos((string) $val10, ".") == 0) {
                $val10 = (string) $val10 . ".0";
            }
            $val11 = round($atrib11 / $atrib11_, 1);
            if (strpos((string) $val11, ".") == 0) {
                $val11 = (string) $val11 . ".0";
            }
            $val12 = round($atrib12 / $atrib12_, 1);
            if (strpos((string) $val12, ".") == 0) {
                $val12 = (string) $val12 . ".0";
            }
            $val13 = round($atrib13 / $atrib13_, 1);
            if (strpos((string) $val13, ".") == 0) {
                $val13 = (string) $val13 . ".0";
            }
            $val14 = round($atrib14 / $atrib14_, 1);
            if (strpos((string) $val14, ".") == 0) {
                $val14 = (string) $val14 . ".0";
            }
            $val15 = round($csiindex / $csiindex_, 1);
            if (strpos((string) $val15, ".") == 0) {
                $val15 = (string) $val15 . ".0";
            }
            $val16 = round($dliindex / $dliindex_, 1);
            if (strpos((string) $val16, ".") == 0) {
                $val16 = (string) $val16 . ".0";
            }
            $val17 = round($refferralindex / $refferralindex_, 1);
            if (strpos((string) $val17, ".") == 0) {
                $val17 = (string) $val17 . ".0";
            }
            $val18 = round($expcompra / $expcompra_, 1);
            if (strpos((string) $val18, ".") == 0) {
                $val18 = (string) $val18 . ".0";
            }

            $str.= "<tr><td colspan=3 style='text-align:rigth'></td>";
            $str.= "<td style='text-align:center'>" . $val1 . "</td>";
            $str.= "<td style='text-align:center'>" . $val2 . "</td>";
            $str.= "<td style='text-align:center'>" . $val3 . "</td>";
            $str.= "<td style='text-align:center'>" . $val4 . "</td>";
            $str.= "<td style='text-align:center'>" . $val5 . "</td>";
            $str.= "<td style='text-align:center'>" . $val6 . "</td>";
            $str.= "<td style='text-align:center'>" . $val7 . "</td>";
            $str.= "<td style='text-align:center'>" . $val8 . "</td>";
            $str.= "<td style='text-align:center'>" . $val9 . "</td>";
            $str.= "<td style='text-align:center'>" . $val10 . "</td>";
            $str.= "<td style='text-align:center'>" . $val11 . "</td>";
            $str.= "<td style='text-align:center'>" . $val12 . "</td>";
            $str.= "<td style='text-align:center'>" . $val13 . "</td>";
            $str.= "<td style='text-align:center'>" . $val14 . "</td>";
            $str.= "<td style='text-align:center'>" . $val15 . "</td>";
            $str.= "<td style='text-align:center'></td>";
            $str.= "<td style='text-align:center'>" . $val16 . "</td>";
            $str.= "<td style='text-align:center'>" . $val17 . "</td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td></td>";
            $str.= "<td style='text-align:center'>" . $val18 . "</td></tr>";


            $str.= "</tbody></table>";

            $str = str_replace('ATRIBA', $val1, $str);
            $str = str_replace('ATRIBB', $val2, $str);
            $str = str_replace('ATRIBC', $val3, $str);
            $str = str_replace('ATRIBD', $val4, $str);
            $str = str_replace('ATRIBE', $val5, $str);
            $str = str_replace('ATRIBF', $val6, $str);
            $str = str_replace('ATRIBG', $val7, $str);
            $str = str_replace('ATRIBH', $val8, $str);
            $str = str_replace('ATRIBI', $val9, $str);
            $str = str_replace('ATRIBJ', $val10, $str);
            $str = str_replace('ATRIBK', $val11, $str);
            $str = str_replace('ATRIBL', $val12, $str);
            $str = str_replace('ATRIBM', $val13, $str);
            $str = str_replace('ATRIBN', $val14, $str);
            $str = str_replace('CSIINDEX', $val15, $str);
            $str = str_replace('DLIINDEX', $val16, $str);
            $str = str_replace('REFFERRALINDEX', $val17, $str);

            $this->output->set_content_type('text/html')->set_output(trim($str));
        }
    }

    public function get_reporte2() {
        $gettabledata2 = $this->orm->get_reporte2_periodo();
        $gettabledata3 = $this->orm->get_reporte2_total();
        $gettabledata = $this->orm->get_reporte2();
        $contador = 0;
        $contador2 = 0;
        $contador3 = 0;
        $csipromedio = 0;
        $nroencuestas = 0;
        $nroencuestas1 = 0;
        $nroencuestas2 = 0;
        $nroencuestas3 = 0;
        $nroencuestas4 = 0;
        $consecionario = "";
        $colorcelda = "#008000";
        $csipromediomayorrank = 0;
        $etiquetaflt = "";
        $val1 = 0;
        $val2 = "";
        $array_csprom = array();
        $str="<meta charset='utf-8'>";
        $str.= "<table width='100%' class='table_rpt1'>";


        if (count($gettabledata->result()) > 0) {
            foreach ($gettabledata->result() as $row) {
                $contador3 = $contador3 + 1;
                //i-cabecera de cada cuadro	
                if ($consecionario != $row->CONCESIONARIO) {
                    $consecionario = $row->CONCESIONARIO;

                    //total de cada consecionario
                    if ($contador3 > 1) {
                        if (count($gettabledata3->result()) > 0) {
                            $str.= "<tr><td style='text-align:center;background-color:#7f7f7f;color:#ffffff;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;border-left: solid 2px #000000;'>TOTAL</td>";
                            $contador2 = 0;
                            $csipromedio = 0;
                            foreach ($gettabledata3->result() as $row3) {
                                if ($consecionario == $row3->CONCESIONARIO) {
                                    $contador2 = $contador2 + 1;
                                    switch ($contador2) {
                                        case 1: $str.= "<td style='text-align:center;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $nroencuestas1 . "</td>";
                                            $csipromedio = $csipromedio + $nroencuestas1 * $row3->CSIPROMEDIO;
                                            break;
                                        case 2: $str.= "<td style='text-align:center;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $nroencuestas2 . "</td>";
                                            $csipromedio = $csipromedio + $nroencuestas2 * $row3->CSIPROMEDIO;
                                            break;
                                        case 3: $str.= "<td style='text-align:center;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $nroencuestas3 . "</td>";
                                            $csipromedio = $csipromedio + $nroencuestas3 * $row3->CSIPROMEDIO;
                                            break;

                                        case 4: $str.= "<td style='text-align:center;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $nroencuestas4 . "</td>";
                                            $csipromedio = $csipromedio + $nroencuestas4 * $row3->CSIPROMEDIO;
                                            break;
                                        default:
                                            break;
                                    }
                                    $str.= "<td style='text-align:center;background-color:#7f7f7f;color:#ffffff;font-weight:bold;border-top: solid 2px #000000;border-bottom: solid 2px #000000;border-right: solid 1px #000000;'>" . $row3->CSIPROMEDIO . "</td>";
                                }
                            }
                        }

                        $val1 = round($csipromedio / ($nroencuestas1 + $nroencuestas2 + $nroencuestas3 + $nroencuestas4), 1);
                        if (strpos((string) $val1, ".") == 0) {
                            $val1 = (string) $val1 . ".0";
                        }

                        $str.= "<td style='width:110px;border-left: solid 2px #000000;' colspan=3></td>";
                        $str.= "<td style='text-align:center;font-weight:bold;border-left: solid 2px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000'>" . ($nroencuestas1 + $nroencuestas2 + $nroencuestas3 + $nroencuestas4) . "</td>";
                        $str.= "<td style='text-align:center;background-color:#7f7f7f;color:#ffffff;font-weight:bold;border-right: solid 2px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;border-left: solid 1px #000000;'>" . $val1 . "</td></tr>";

                        $nroencuestas1 = 0;
                        $nroencuestas2 = 0;
                        $nroencuestas3 = 0;
                        $nroencuestas4 = 0;
                        $csipromediomayorrank = 0;

                        $str.= "<tr><td colspan=14><br/><br/></td>";
                    }
                    //total de cada consecionario	

                    $str.="
							<tr>                       
								<td height='40px' style='text-align:center;background-color:#538ed5;color:#ffffff;font-weight:bold;border:1px;border: solid 1px #000000;' colspan=9>TITULO</td>
								<td colspan=5></td>
							</tr>
							<tr>  
							<td rowspan=2 style='text-align:center;background-color:#7f7f7f;color:#ffffff;font-weight:bold;border: solid 1px #000000;'>ASESOR DE VENTAS</td>";

                    if (count($gettabledata2->result()) > 0) {
                        foreach ($gettabledata2->result() as $row2) {
                            $str.= "<td style='text-align:center;background-color:#7f7f7f;color:#ffffff;font-weight:bold;border: solid 1px #000000;' colspan=2>" . $row2->ANIO . "-" . $row2->TRIMESTRE . "</td>";
                            $etiquetaflt = $row2->ANIO . "-" . $row2->TRIMESTRE;
                        }
                    }

                    $str.="<td style='width:110px' colspan=3></td><td style='text-align:center;background-color:#a5a5a5;color:#ffffff;font-weight:bold;border: solid 1px #000000;' colspan=2>Promedio<br/>Acumulado</td></tr>
							<tr><td style='text-align:center;font-weight:bold;border: solid 1px #000000;'>Nro<br/>Encuesta</td>
							   <td style='text-align:center;background-color:#ff0000;color:#ffffff;font-weight:bold;border-top: solid 2px #ff0000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;'>CS Prom</td>
							   <td style='text-align:center;font-weight:bold;border: solid 1px #000000;'>Nro<br/>Encuesta</td>
							   <td style='text-align:center;background-color:#ff0000;color:#ffffff;font-weight:bold;border-top: solid 2px #ff0000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;'>CS Prom</td>
							   <td style='text-align:center;font-weight:bold;border: solid 1px #000000;'>Nro<br/>Encuesta</td>
							   <td style='text-align:center;background-color:#ff0000;color:#ffffff;font-weight:bold;border-top: solid 2px #ff0000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;'>CS Prom</td>
							   <td style='text-align:center;font-weight:bold;border: solid 1px #000000;'>Nro<br/>Encuesta</td>
							   <td style='text-align:center;background-color:#ff0000;color:#ffffff;font-weight:bold;border-top: solid 2px #ff0000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;;'>CS Prom</td>
							   <td style='width:110px' colspan=3></td>
							   <td style='text-align:center;font-weight:bold;border: solid 1px #000000;'>Nro<br/>Encuesta</td>
							   <td style='text-align:center;background-color:#ff0000;color:#ffffff;font-weight:bold;border-top: solid 2px #ff0000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;'>CS Prom</td>
							</tr>";

                    $str = str_replace('TITULO', $consecionario, $str);

                    $csipromedio = 0;
                    $nroencuestas = 0;
                    $contador2 = 0;
                }
                //f-cabecera de cada cuadro	

                if ($contador == 0) {
                    $str.= "<tr><td style='font-weight:bold;border: solid 1px #000000;'>" . $row->VENDEDOR . "</td>";
                    $nroencuestas = 0;
                    $csipromedio = 0;
                }


                $colorcelda = "#008000";
                if ($row->CSIPROMEDIO < 90) {
                    $colorcelda = "#ff0000";
                }

                if ($row->NRO_ENCUESTA > 0) {
                    $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $row->NRO_ENCUESTA . "</td>";
                    $val2 = "";
                    if ($contador == 3 && $row->FLAG > 0) {
                        $val2 = "background-color:#ffff00;";
                    }
                    $str.= "<td style='text-align:center;color:" . $colorcelda . ";font-weight:bold;border: solid 1px #000000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;" . $val2 . "'>" . $row->CSIPROMEDIO . "</td>";
                } else {
                    $str.= "<td style='text-align:center;border: solid 1px #000000;'></td>";
                    $str.= "<td style='text-align:center;background-color:#a5a5a5;color:" . $colorcelda . ";font-weight:bold;border: solid 1px #000000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;'></td>";
                }

                $nroencuestas = $nroencuestas + $row->NRO_ENCUESTA;
                $csipromedio = $csipromedio + $row->NRO_ENCUESTA * $row->CSIPROMEDIO;

                switch ($contador) {
                    case 1: $nroencuestas1 = $nroencuestas1 + $row->NRO_ENCUESTA;
                        break;
                    case 2: $nroencuestas2 = $nroencuestas2 + $row->NRO_ENCUESTA;
                        break;
                    case 3: $nroencuestas3 = $nroencuestas3 + $row->NRO_ENCUESTA;
                        break;
                    case 4: $nroencuestas4 = $nroencuestas4 + $row->NRO_ENCUESTA;
                        break;
                    default:
                        break;
                }
                $contador = $contador + 1;

                if ($contador == 4) {
                    /*
                      if($row->CSIPROMEDIO>$csipromediomayor && $nroencuestas4>2 && $row->CSIPROMEDIO>=90)
                      {
                      $csipromediomayor = $row->CSIPROMEDIO;
                      $str.= "<td colspan='3'><p style='font-size:8px'><img src='". base_url()."public/img/Flecha.jpg'>Primer lugar 2012-4<br/> (con más de 2 encuestas)</p></td>";
                      }
                      else
                      {
                      $str.= "<td colspan='3'></td>";
                      } */

                    if ($row->FLAG > 0) {
                        $str.= "<td><img src='" . base_url() . "public/img/Flecha.jpg'></td><td colspan='2'><p style='padding-top:2px;width: 80px;line-height: 100%;text-align:center;font-size:8px;background-color:#ffff00;border:1px solid;border-color:#000000;'>Primer lugar<br/>" . $etiquetaflt . "<br/>(con más de 2<br/>encuestas)</p></td>";
                    } else {
                        $str.= "<td colspan='3'></td>";
                    }

                    $str.= "<td style='text-align:center;border: solid 1px #000000;'>" . $nroencuestas . "</td>";
                    $colorcelda = "#008000";
                    if (round($csipromedio / $nroencuestas, 1) < 90) {
                        $colorcelda = "#ff0000";
                    }

                    $val1 = round($csipromedio / $nroencuestas, 1);
                    if (strpos((string) $val1, ".") == 0) {
                        $val1 = (string) $val1 . ".0";
                    }



                    if ($val1 > $csipromediomayorrank && $nroencuestas > 12 && $val1 >= 90) {
                        $csipromediomayorrank = $val1;
                        $str.= "<td style='text-align:center;color:" . $colorcelda . ";font-weight:bold;border: solid 1px #000000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;background-color:#ffff00;'>" . $val1 . "</td>";
                        $str.= "<td><img src='" . base_url() . "public/img/Flecha.jpg'></td><td colspan='2'><p style='padding-top:2px;width: 80px;line-height: 100%;text-align:center;font-size:8px;background-color:#ffff00;border:1px solid;border-color:#000000;'>Primer lugar<br/>" . $etiquetaflt . "<br/>(con más de 12<br/>encuestas)</p></td>";
                    } else {
                        $str.= "<td style='text-align:center;color:" . $colorcelda . ";font-weight:bold;border: solid 1px #000000;border-left: solid 2px #ff0000;border-right: solid 2px #ff0000;'>" . $val1 . "</td>";
                        $str.= "<td colspan='3'></td>";
                    }
                    $str.= "</tr>";
                    $contador = 0;
                }
                 //total de cada consecionario-para el ultimo por grupo
                if ($contador3 == count($gettabledata->result())) {
                    if (count($gettabledata3->result()) > 0) {
                        $str.= "<tr ><td style='text-align:center;background-color:#7f7f7f;color:#ffffff;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;border-left: solid 2px #000000;'>TOTAL</td>";
                        $contador2 = 0;
                        $csipromedio = 0;
                        foreach ($gettabledata3->result() as $row3) {
                            if ($consecionario == $row3->CONCESIONARIO) {
                                $contador2 = $contador2 + 1;
                                switch ($contador2) {
                                    case 1: $str.= "<td style='text-align:center;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $nroencuestas1 . "</td>";
                                        $csipromedio = $csipromedio + $nroencuestas1 * $row3->CSIPROMEDIO;
                                        break;
                                    case 2: $str.= "<td style='text-align:center;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $nroencuestas2 . "</td>";
                                        $csipromedio = $csipromedio + $nroencuestas2 * $row3->CSIPROMEDIO;
                                        break;
                                    case 3: $str.= "<td style='text-align:center;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $nroencuestas3 . "</td>";
                                        $csipromedio = $csipromedio + $nroencuestas3 * $row3->CSIPROMEDIO;
                                        break;
                                    case 4: $str.= "<td style='text-align:center;font-weight:bold;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $nroencuestas4 . "</td>";
                                        $csipromedio = $csipromedio + $nroencuestas4 * $row3->CSIPROMEDIO;
                                        break;
                                    default:
                                        break;
                                }
                                $str.= "<td style='text-align:center;background-color:#7f7f7f;color:#ffffff;font-weight:bold;border-top: solid 2px #ff0000;border: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000;'>" . $row3->CSIPROMEDIO . "</td>";
                            }
                        }
                    }

                    $val1 = round($csipromedio / ($nroencuestas1 + $nroencuestas2 + $nroencuestas3 + $nroencuestas4), 1);
                    if (strpos((string) $val1, ".") == 0) {
                        $val1 = (string) $val1 . ".0";
                    }

                    $str.= "<td style='width:110px;border-left: solid 2px #000000;' colspan=3></td>";
                    $str.= "<td style='text-align:center;font-weight:bold;border-left: solid 2px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000'>" . ($nroencuestas1 + $nroencuestas2 + $nroencuestas3 + $nroencuestas4) . "</td>";
                    $str.= "<td style='text-align:center;background-color:#7f7f7f;color:#ffffff;font-weight:border-right: solid 2px #000000;border-left: solid 1px #000000;border-top: solid 2px #000000;border-bottom: solid 2px #000000'>" . $val1 . "</td></tr>";

                    $nroencuestas1 = 0;
                    $nroencuestas2 = 0;
                    $nroencuestas3 = 0;
                    $nroencuestas4 = 0;
                    $csipromediomayorrank = 0;
                }
                //total de cada consecionario
            }
        }
        $str.= "</table>";

        $this->output->set_content_type('text/html')->set_output($str);
    }

    /* i nbaltodanov 13022013 */

    public function redondear_decimal($val) {
        $new = '';
        if (strpos((string) $val, ".") == 0) {
            $new = (string) $val . ".0";
        }
        return $new;
    }

    public function resumen_general() {
        $query = $this->orm->resumen_general();
        //print_r($query->result_array());
        $tablerow = array();
        $tablecol = array();
        $str1="<meta charset='utf-8'>";
        $str1 .= "<table cellpadding='0' cellspacing='0' border='1' id='tableexcel'><thead>
		<tr><th colspan='26' style='background-color: #ff7575; padding: 10px; width: 100%; font-size: 25px;'>CSI POR CONCESIONARIO</th></tr>
		<tr>
		<th class='suma'>CALIFICACION</th>";
        $str2 = "<tbody><tr><td>1</td>";
        $str3 = "<tr><td>2</td>";
        $str4 = "<tr><td>3</td>";
        $str5 = "<tr><td>4</td>";
        $str6 = "<tr><td>5</td>";
        $str7 = "<tr><td>#DATOS</td>";
        $str8 = "<tr><td style='background-color:#808080; color:white;'>CSI</td>";
        $str9 = "<tr><td style='background-color:#808080; color:white;'>DLIINDEX</td>";
        $str10 = "<tr><td style='background-color:#808080; color:white;'>REFFERRALINDEX</td>";
        $str11 = "<th>TOTAL</th>";
        $sum1 = $sum2 = $sum3 = $sum4 = $sum5 = $sum6 = $sum7 = $sum8 = $sum9 = $sum10 = 0;
        foreach ($query->result() as $row) {
            $sum1+=$row->CAL1;
            $sum2+=$row->CAL2;
            $sum3+=$row->CAL3;
            $sum4+=$row->CAL4;
            $sum5+=$row->CAL5;
            // datos
            $sum6+=$row->NRO;
            //CSI
            $sum7+=$row->CSI;
            //DLIINDEX
            $sum8+=$row->DLIINDEX;
            //REFFERRALINDEX
            $sum9+=$row->REFFERRALINDEX;

            $str1.='<th style="background-color:#808080; color:white;">' . $row->CONCESIONARIO . '</th>';
            $str2.='<td >' . round($row->CAL1, 1) . '</td>';
            $str3.='<td >' . round($row->CAL2, 1) . '</td>';
            $str4.='<td >' . round($row->CAL3, 1) . '</td>';
            $str5.='<td >' . round($row->CAL4, 1) . '</td>';
            $str6.='<td >' . round($row->CAL5, 1) . '</td>';
            $str7.='<td >' . round($row->NRO, 1) . '</td>';
            // tfood
            $str8.='<td style="background-color:#808080; color:white;" >' . round($row->CSI, 1) . '</td>';
            $str9.='<td style="background-color:#808080; color:white;" >' . round($row->DLIINDEX, 1) . '</td>';
            $str10.='<td style="background-color:#808080; color:white;" >' . round($row->REFFERRALINDEX, 1) . '</td>';
        }
        $str1.='<th style="background-color:#808080; color:white;">TOTAL</th></tr></thead>';
        $str2.='<td style="background-color:#808080; color:white;">' . round($sum1, 1) . '</td></tr>';
        $str3.='<td style="background-color:#808080; color:white;">' . round($sum2, 1) . '</td></tr>';
        $str4.='<td style="background-color:#808080; color:white;">' . round($sum3, 1) . '</td></tr>';
        $str5.='<td style="background-color:#808080; color:white;">' . round($sum4, 1) . '</td></tr>';
        $str6.='<td style="background-color:#808080; color:white;">' . round($sum5, 1) . '</td></tr>';
        $str7.='<td style="background-color:#808080; color:white;">' . round($sum6, 1) . '</td></tr>';
        $str8.='<td style="background-color:#808080; color:white;">' . round($sum7, 1) . '</td></tr>';
        $str9.='<td style="background-color:#808080; color:white;">' . round($sum8, 1) . '</td></tr>';
        $str10.='<td style="background-color:#808080; color:white;">' . round($sum9, 1) . '</td></tr></tbody></table>';
        $str1.=$str2 . $str3 . $str4 . $str5 . $str6 . $str7 . $str8 . $str9 . $str10;
        //$table=$this->table->generate();
        $this->output->set_content_type('text/html')->set_output($str1 . $this->resumen_general2());
        //$this->resumen_resumen();
    }

    // resumen_resumen


    public function resumen_general2() {
        $query = $this->orm->resumen_resumen();
       	$str_head="<meta charset='utf-8'>";
        $str_head .= '<table border="1" cellpadding="0" cellspacing="0"  id="tableexcel1">
	    <thead><tr><th colspan="18" style="background-color: #ff7575; padding: 10px; width: 100%; font-size: 25px;">CSI POR ATRIBUTO</th><tr>';
        $thead = array('CALIFICACION', 'CORTESIA Y AMABILIDAD', 'EXPLICACION COMPLETA DE LAS CARACTERISTICAS DEL VEHICULO', 'EXPLICACION COMPLETA DE PROGRAMAS DE FINANCIAMIENTO', 'CALIFICACION FINAL DEL DESEMPEÑO DEL ASESOR DURANTE EL PROCESO DE VENTA', 'LIMPIEZA DEL VEHICULO', 'EXPLICACION DE CARACTERISTICAS Y CONTROLES DEL VEHICULO', 'EXPLICACION DEL PROGRAMA DE MANTENIMIENTO Y GARANTIA', 'INFORMACION RECIBIDA SOBRE LAS FACILIDADES DE SERVICIO Y REPUESTOS, ASI COMO EL HORARIO DE ATENCION DEL CONCESIONARIO', 'ENTREGA DEL VEHICULO EN LA FECHA ACORDADA', 'CUMPLIMIENTO DE COMPROMISOS HECHOS DURANTE LA VENTA', 'SEGUIMIENTO DE LA COMPRA', 'FACILIDAD / CONVENIENCIA DEL ESTACIONAMIENTO EN EL CONCESIONARIO', 'LIMPIEZA Y APARIENCIA DE LAS INSTALACIONES DEL CONCESIONARIO', 'COMODIDAD DE LAS INSTALACIONES DEL CONCESIONARIO', 'CSI TOTAL', 'DLI INDEX', 'REFFERRAL INDEX');
        // head
        foreach ($thead as $key => $value) {
            if ($value == 'CALIFICACION') {
                $str_head.='<th>' . $value . '</th>';
            } else {
                $str_head.='<th class="suma">' . $value . '</th>';
            }
        }
        $str_head.='</thead>';
        $str_body = '<tbody>';
        $sum1 = $sum2 = $sum3 = $sum4 = $sum5 = $sum6 = $sum7 = $sum8 = $sum9 = $sum10 = $sum11 = $sum12 = $sum13 = $sum14 = $csitotal = $dliindex = $refferralindex = 0;
        $csi1 = $csi2 = $csi3 = $csi4 = $csi5 = $csi6 = $csi7 = $csi8 = $csi9 = $csi10 = $csi11 = $csi12 = $csi13 = $csi14 = $csi_csitotal = $csi_dliindex = $csi_refferralindex = 0;
        $csi_array = array();
        // sumas
        foreach ($query->result() as $row) {
            $sum1+=$row->ATRIB1;
            $sum2+=$row->ATRIB2;
            $sum3+=$row->ATRIB3;
            $sum4+=$row->ATRIB4;
            $sum5+=$row->ATRIB5;
            $sum6+=$row->ATRIB6;
            $sum7+=$row->ATRIB7;
            $sum8+=$row->ATRIB8;
            $sum9+=$row->ATRIB9;
            $sum10+=$row->ATRIB10;
            $sum11+=$row->ATRIB11;
            $sum12+=$row->ATRIB12;
            $sum13+=$row->ATRIB13;
            $sum14+=$row->ATRIB14;
            $csitotal+=$row->CSITOTAL;
            $dliindex+=$row->DLIINDEX;
            $refferralindex+=$row->REFFERRALINDEX;
            //tabla principal
            $csi_array['ATRIB1'][] = $row->ATRIB1;
            $csi_array['ATRIB2'][] = $row->ATRIB2;
            $csi_array['ATRIB3'][] = $row->ATRIB3;
            $csi_array['ATRIB4'][] = $row->ATRIB4;
            $csi_array['ATRIB5'][] = $row->ATRIB5;
            $csi_array['ATRIB6'][] = $row->ATRIB6;
            $csi_array['ATRIB7'][] = $row->ATRIB7;
            $csi_array['ATRIB8'][] = $row->ATRIB8;
            $csi_array['ATRIB9'][] = $row->ATRIB9;
            $csi_array['ATRIB10'][] = $row->ATRIB10;
            $csi_array['ATRIB11'][] = $row->ATRIB11;
            $csi_array['ATRIB12'][] = $row->ATRIB12;
            $csi_array['ATRIB13'][] = $row->ATRIB13;
            $csi_array['ATRIB14'][] = $row->ATRIB14;
            $csi_array['CSITOTAL'][] = $row->CSITOTAL;
            $csi_array['DLIINDEX'][] = $row->DLIINDEX;
            $csi_array['REFFERRALINDEX'][] = $row->REFFERRALINDEX;
        }
        foreach ($query->result() as $row1) {
            $str_body.='<tr><td>' . $row1->CAL . '</td><td>' . $row1->ATRIB1 . '</td><td>' . $row1->ATRIB2 . '</td><td>' . $row1->ATRIB3 . '</td>
            <td>' . $row1->ATRIB4 . '</td><td>' . $row1->ATRIB5 . '</td><td>' . $row1->ATRIB6 . '</td><td>' . $row1->ATRIB7 . '</td><td>' . $row1->ATRIB8 . '</td>
            <td>' . $row1->ATRIB9 . '</td><td>' . $row1->ATRIB10 . '</td><td>' . $row1->ATRIB11 . '</td><td>' . $row1->ATRIB12 . '</td><td>' . $row1->ATRIB13 . '</td><td>' . $row1->ATRIB14 . '</td>
            <td class="suma">' . $row1->CSITOTAL . '</td><td>' . $row1->DLIINDEX . '</td><td>' . $row1->REFFERRALINDEX . '</td></tr>';
        }
        $str_body.='</tbody>';
        $str_foot = '<tfood><tr>';
        $add = array('#DATOS', $sum1, $sum2, $sum3, $sum4, $sum5, $sum6, $sum7, $sum8, $sum9, $sum10, $sum11, $sum12, $sum13, $sum14, $csitotal, $dliindex, $refferralindex);
        foreach ($add as $llave => $valor) {
            if ($llave == 15) {
                $str_foot.='<td class="suma">' . $valor . '</td>';
            } else {
                $str_foot.='<td>' . $valor . '</td>';
            }
        }
        $str_foot.='</tr>';

        $str_csi = '<tr><td class="suma">CSI</td>';
        foreach ($csi_array as $row2) {
            $str_csi.='<td class="suma">' . $this->csi_over($row2) . '</td>';
        }
        $str_csi.='</tr></table>';
        //$this->output->set_content_type('text/html')->set_output($str_head.$str_body.$str_foot.$str_csi);
        return $str_head . $str_body . $str_foot . $str_csi;
    }

    // calcula csi por en array
    public function csi_over($data) {
        $sum = $data[0] + $data[1] + $data[2] + $data[3] + $data[4];
        $csi = round(($data[4] * 100 + $data[3] * 80 + $data[2] * 50 + $data[1] * 0 + $data[0] * -50) / ($sum), 1);
        $csi_n = (string) $csi;
        $new = '';
        if (strpos($csi_n, ".") == 0) {
            $new = $csi_n . ".0";
        } else {
            $new = $csi_n;
        }
        return $new;
    }

    //f modificacion 

    public function resumen_resumen() {
        $this->get_resumen();
    }

    // modificacion para calcular csi en hoja resumen
    public function generar_csi() {
        $query = $this->orm->resumen_resumen();
        $csi_array = array();
        foreach ($query->result() as $row) {
            $csi_array['ATRIB1'][] = $row->ATRIB1;
            $csi_array['ATRIB2'][] = $row->ATRIB2;
            $csi_array['ATRIB3'][] = $row->ATRIB3;
            $csi_array['ATRIB4'][] = $row->ATRIB4;
            $csi_array['ATRIB5'][] = $row->ATRIB5;
            $csi_array['ATRIB6'][] = $row->ATRIB6;
            $csi_array['ATRIB7'][] = $row->ATRIB7;
            $csi_array['ATRIB8'][] = $row->ATRIB8;
            $csi_array['ATRIB9'][] = $row->ATRIB9;
            $csi_array['ATRIB10'][] = $row->ATRIB10;
            $csi_array['ATRIB11'][] = $row->ATRIB11;
            $csi_array['ATRIB12'][] = $row->ATRIB12;
            $csi_array['ATRIB13'][] = $row->ATRIB13;
            $csi_array['ATRIB14'][] = $row->ATRIB14;
            $csi_array['CSITOTAL'][] = $row->CSITOTAL;
            $csi_array['DLIINDEX'][] = $row->DLIINDEX;
            $csi_array['REFFERRALINDEX'][] = $row->REFFERRALINDEX;
        }
        $str_csi = '<tr><td class="promedio" style="background-color:#7f7f7f;
        color:white;">PROMEDIO</td><td class="suma" style="background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center">2012-4</td>';
        foreach ($csi_array as $key => $row2) {
            //echo $key.' '; 					  
            if ($key == 'CSITOTAL' && $this->csi_over($row2) < 90) {

                $str_csi.='<td class="suma_roja" style=" background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center">' . $this->csi_over($row2) . '</td>';
            } else {
                //print_r($key);
                $str_csi.='<td class="suma" style=" background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center">' . $this->csi_over($row2) . '</td>';
            }
        }
        $str_csi.='</tr></table>';
        //$this->output->set_content_type('text/html')->set_output($str_csi);
        return $str_csi;
    }

    public function get_resumen() {
        $gettabledata = $this->orm->get_resumen();
        $contador = 0;
        $rowspan = 0;
        //<th style='text-align:center;background-color:#7f7f7f;color:#ffffff' rowspan=2>TRIMESTRE</th>
        //<th style='text-align:center;background-color:#7f7f7f;color:#ffffff' rowspan=2>ORDEN</th> 
        $str="<meta charset='utf-8'>";
        $str .="
		<table width='100%' border='1'   bordercolor='#141414'>
		<thead>
		<tr>				
		<th style='text-align:center;background-color:#7f7f7f;color:#ffffff' rowspan=2>CONCESIONARIO</th> 			
		<th style='text-align:center;background-color:#7f7f7f;color:#ffffff' rowspan=2>AÑO</th>		
		<th style='text-align:center;background-color:#F78181;color:black' colspan=4 height=45>ATENCION INICIAL DEL ASESOR DE VENTAS</th>
		<th style='text-align:center;background-color:#F78181;color:black' colspan=6>ENTREGA DEL VEHICULO</th>
		<th style='text-align:center;background-color:#F78181;color:black'>SEGUIM</th>
		<th style='text-align:center;background-color:#F78181;color:black' colspan=3>INSTALACIONES</th>		
		<tr>		
		<th style='text-align:center'>CORTESIA</br> Y </br>AMABILIDAD</th>
		<th style='text-align:center'>EXPLICACION</br> COMPLETA DE<br/>LAS <br/>CARACTERISTICAS<br/> DELVEHICULO</th>
        <th style='text-align:center'>EXPLICACION </br>COMPLETA DE<br/>PROGRAMAS DE<br/>FINANCIAMIENTO</th>
        <th style='text-align:center'>CALIFICACION </br> FINAL DEL<br/>DESEMPEÑO </br>DEL ASESOR<br/>DURANTE </br>EL PROCESO </br>DE VENTA</th>	
        <th style='text-align:center'>LIMPIEZA <br/>DEL <br/>VEHICULO</th>		
		<th style='text-align:center'>EXPLICACION DE<br/> CARACTERISTICAS<br/>Y CONTROLES <br/>DEL VEHICULO</th>
		<th style='text-align:center'>EXPLICACION DEL<br/> PROGRAMA DE<br/>MANTENIMIENTO <br/>Y GARANTIA</th>
		<th style='text-align:center'>INFORMACION <br/>RECIBIDA SOBRE<br/>LAS FACILIDADES <br/>DE SERVICIO Y<br/> REPUESTOS, <br/>ASI COMO EL<br/>HORARIO DE <br/>ATENCION DEL<br/>CONCESIONARIO</th>
		<th style='text-align:center'>ENTREGA DEL VEHICULO EN LA<br/>FECHA ACORDADA</th>
		<th style='text-align:center'>CUMPLIMIENTO DE COMPROMISOS<br/>HECHOS DURANTE LA VENTA</th>		
		<th style='text-align:center'>AGRADECIMIENTO POR LA COMPRA</th>			
		<th style='text-align:center'>FACILIDAD / CONVENIENCIA DEL<br/>ESTACIONAMIENTO EN EL<br/>CONCESIONARIO</th>		
		<th style='text-align:center'>LIMPIEZA Y APARIENCIA DE LAS<br/>INSTALACIONES DEL<br/>CONCESIONARIO</th>
		<th style='text-align:center'>COMODIDAD DE LAS<br/>INSTALACIONES DEL<br/>CONCESIONARIO</th>
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff' rowspan=2>CSI INDEX</th>      
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff' rowspan=2>DLI INDEX</th>
        <th style='text-align:center;background-color:#7f7f7f;height:18px;color:#ffffff' rowspan=2>REFFERRAL INDEX</th>
		</tr>	
		</thead>	
		<tbody>";

        if (count($gettabledata->result_array()) > 0) {  
            foreach ($gettabledata->result() as $row) {
                if ($row->TRIMESTRE == 4) {
                    $str.= "<tr clase='tr_red' style=' border-color:red;'>";
                } else {
                    $str.= "<tr>";
                }
                //class='suma'
                //$str.= "<td>" . $row->ORDEN."</td>";
                switch ($row->ORDEN) {
            				case '1':
            					$str.= "<td class='td_" . $row->ORDEN . "'style=' background-color:#ffff99'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '2':
            					$str.= "<td class='td_" . $row->ORDEN . "'style=' background-color:#ffff99'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '3':
            					$str.= "<td class='td_" . $row->ORDEN . "'style=' background-color:#ffff99'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '4':
            					$str.= "<td class='td_" . $row->ORDEN . "'style=' background-color:#ffff99'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '5':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#8db4e3'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;                
            				case '6':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#8db4e3'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '7':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#8db4e3'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '8':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#fac090'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '9':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#c2d69a'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           	case '10':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#c2d69a'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '11':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#c2d69a'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '12':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#4bacc6'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '13':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#4bacc6'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '14':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#8064a2'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;                
            				case '15':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#8064a2'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '16':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#fde9d9'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '17':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#ffc000'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '18':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#dbeef3'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           	case '19':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#d99795'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '20':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#d99795'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '21':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#d99795'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '22':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#d99795'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                            case '23':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#d99795'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;
                           case '24':
            					$str.= "<td class='td_" . $row->ORDEN . "'style='background-color:#d99795'>" . strtoupper($row->CONCESIONARIO) . "</td>";
            					break;                            
            				default:
            					# code...
            					break;
		              	}
                if ($row->TRIMESTRE == 4) {
                    //$str.= "<td>" . $row->ORDEN."</td>";  
                    			
                    //$str.= "<td class='td_" . $row->ORDEN . "'>" . strtoupper($row->CONCESIONARIO) . "</td>";
                    $str.= "<td class='suma' style='background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ANIO . "-" . $row->TRIMESTRE . "</td>";
                    //$str.="<td></td>";
                    //$str.= "<td>" . $row->TRIMESTRE."</td>";	


                    $str.= "<td style='background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB1 . "</td>";
                    $str.= "<td style='background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB2 . "</td>";
                    $str.= "<td class='suma' style='background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB3 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB4 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB5 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB6 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB7 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB8 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB9 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB10 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB11 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB12 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB13 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ATRIB14 . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->CSIINDEX . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->DLIINDEX . "</td>";
                    $str.= "<td class='suma' style=' background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->REFFERRALINDEX . "</td>";
                    $str.= "</tr>";
                } else {
                    //$str.= "<td>" . $row->ORDEN."</td>";
                    //$str.= "<td class='td_" . $row->ORDEN . "'>" . $row->CONCESIONARIO . "</td>";
                    $str.= "<td style='background-color:#7f7f7f; color:white; border-color:red; border-width:2px; text-align:center'>" . $row->ANIO . "-" . $row->TRIMESTRE . "</td>";
                    //$str.="<td></td>";
                    //$str.= "<td>" . $row->TRIMESTRE."</td>";					
                    $str.= "<td style='text-align:center'>" . $row->ATRIB1 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB2 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB3 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB4 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB5 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB6 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB7 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB8 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB9 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB10 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB11 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB12 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB13 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->ATRIB14 . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->CSIINDEX . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->DLIINDEX . "</td>";
                    $str.= "<td style='text-align:center'>" . $row->REFFERRALINDEX . "</td>";
                    $str.= "</tr>";
                }
            }
        }
        //"</tr></tr></body></tr></table>";

        $this->output->set_content_type('text/html')->set_output(trim($str . $this->generar_csi()));

        //$this->output->set_content_type('text/html')->set_output('ijiji');
    }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */