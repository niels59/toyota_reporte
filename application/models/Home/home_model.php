<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home_model extends CI_Model {
		public function __construct()
		{
			parent::__construct();
			$this->load->database();
		}
		public function login_usuario(){
			//print_r($this->input->post('contrasena'));
			$flag=false;
			$this->db->select('COUNT(1) AS RES,ID_PERFIL');
			$this->db->where(array('NICK'=>$this->input->post('nick'),'CONTRASENA'=>$this->input->post('contrasena')));
			$query=$this->db->get('USUARIO');		

			if ($query->num_rows()>0) {
				$row=$query->row();
				if ($row->RES!=0) {
					$flag=true;
					$data['nick']=$this->input->post('nick');
					$data['idperfil']=$row->ID_PERFIL;
					$this->simple_sessions->add_sess($data);
				}
			}
			return $flag;
		}
		//public function get

		public function get_grupos() {
			$str = "";
			$sql = "select DISTINCT UPPER(GRUPO) AS NOMBRE, UPPER(REPLACE(GRUPO,' ','_')) AS VALOR 
					from BASE
					order by 2";
	
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				return $query->result();
			}	
		
		}
		
		public function get_periodos() {
			$str = "";
			$sql = "select DISTINCT ANIO, TRIMESTRE
					from BASE 
					ORDER BY 1 desc, 2 desc";
	
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				return $query->result();
			}	
		   
    	}
		
		public function get_consecionarios() {
			$str = "";
			$sql = "select DISTINCT UPPER(CONCESIONARIO) AS NOMBRE, UPPER(REPLACE(CONCESIONARIO,' ','_')) AS VALOR
					from BASE 
					where GRUPO = '" . $this->input->post('elegido') .  "' 
					order by 1"; 
	
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				return $query->result();
			}	
		   
    	}
		
		public function get_reporte2_periodo() {
			$str = "";
			$sql = "SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1 ORDER BY 1,2"; 
	
			$query = $this->db->query($sql);
			return $query;
		   
    	}
		
		/*i-rbaezm-20130212-desarrollo del primer reporte*/
		public function get_reporte1() {
			$str = "";
			$sql = "SELECT A.ID, A.CLIENTE, A.VENDEDOR, A.P3C, A.P3E , A.P3G , A.P3K , A.P5A ,
                        A.P5C , A.P5D , A.P5E , A.P6 , A.P8 , A.P9 , A.P11A ,A.P11B , A.P11C ,
                        B.CSIINDEX, B.CSIPROMEDIO, B.DLIINDEX, B.REFFERRALINDEX, B.CAL1, B.CAL2, B.CAL3, B.CAL4,B.CAL5,
                        (B.CAL1 + B.CAL2 + B.CAL3 + B.CAL4 + B.CAL5) AS NRO,B.CSIINDEX AS CSIINDEX_2,
                        A.MODELO_AUTO,
                        A.P12 
FROM
(     SELECT ID,CLIENTE, VENDEDOR, P3C, P3E, P3G, P3K, P5A, P5C, P5D, P5E, P6, P8, P9, P11A, P11B, P11C, MODELO_AUTO, P12             
      FROM BASE
      WHERE GRUPO = '" . $this->input->post('elegido1') .  "'  AND CONCESIONARIO = '" . $this->input->post('elegido2') .  "'  AND 
                               ANIO = " . substr($this->input->post('elegido3'),0,4) .  " AND TRIMESTRE = " . substr($this->input->post('elegido3'),4,1) .  " ) AS A
INNER JOIN (
      SELECT CSIINDEX, CSIPROMEDIO, DLIINDEX, REFFERRALINDEX,ID, CAL1,CAL2,CAL3,CAL4,CAL5 
      FROM verificacion1
      WHERE GRUPO = '" . $this->input->post('elegido1') .  "'  AND CONCESIONARIO = '" . $this->input->post('elegido2') .  "'  AND 
                               ANIO = " . substr($this->input->post('elegido3'),0,4) .  " AND TRIMESTRE = " . substr($this->input->post('elegido3'),4,1) .  "
) AS B ON A.ID = B.ID
ORDER BY B.CSIPROMEDIO DESC, B.CSIINDEX DESC, A.CLIENTE"; 

	
			$query = $this->db->query($sql);
			return $query;
			
    	}
		
		public function resumen_general()
    	{
    		$sql="SELECT 
					D . *, E.DLIINDEX, E.REFFERRALINDEX
					FROM
					    (SELECT 
					        ORDEN,
					            CONCESIONARIO,
					            CAL1,
					            CAL2,
					            CAL3,
					            CAL4,
					            CAL5,
					            CAL1 + CAL2 + CAL3 + CAL4 + CAL5 AS NRO,
					            (CAL5 * 100 + CAL4 * 80 + CAL3 * 50 + CAL2 * 0 + CAL1 * - 50) / (CAL1 + CAL2 + CAL3 + CAL4 + CAL5) AS CSI
					    FROM
					        (SELECT 
					        ORDEN,
					            TIPO,
					            UPPER(CONCESIONARIO) AS CONCESIONARIO,
					            SUM(CAL1) AS CAL1,
					            SUM(CAL2) AS CAL2,
					            SUM(CAL3) AS CAL3,
					            SUM(CAL4) AS CAL4,
					            SUM(CAL5) AS CAL5
					    FROM
					        VERIFICACION1 AS A
					    INNER JOIN (SELECT 
					        ANIO, TRIMESTRE
					    FROM
					        PERIODO
					    WHERE
					        ESTADO = 1
					    ORDER BY 1 DESC , 2 DESC) AS B ON A.ANIO = B.ANIO
					        AND A.TRIMESTRE = B.TRIMESTRE
					    GROUP BY ORDEN , TIPO , CONCESIONARIO) AS A) AS D
					        INNER JOIN
					    (SELECT 
					        CONCESIONARIO,
					            SUM(CASE
					                WHEN CAL = 1 THEN DLIINDEX * - 50
					                WHEN CAL = 2 THEN DLIINDEX * 0
					                WHEN CAL = 3 THEN DLIINDEX * 50
					                WHEN CAL = 4 THEN DLIINDEX * 80
					                WHEN CAL = 5 THEN DLIINDEX * 100
					                ELSE 0
					            END) / SUM(DLIINDEX) AS DLIINDEX,
					            SUM(CASE
					                WHEN CAL = 1 THEN REFFERRALINDEX * - 50
					                WHEN CAL = 2 THEN REFFERRALINDEX * 0
					                WHEN CAL = 3 THEN REFFERRALINDEX * 50
					                WHEN CAL = 4 THEN REFFERRALINDEX * 80
					                WHEN CAL = 5 THEN REFFERRALINDEX * 100
					                ELSE 0
					            END) / SUM(REFFERRALINDEX) AS REFFERRALINDEX
					    FROM
					        VERIFICACION2 A
					    INNER JOIN (SELECT 
					        ANIO, TRIMESTRE
					    FROM
					        PERIODO
					    WHERE
					        ESTADO = 1
					    ORDER BY 1 DESC , 2 DESC) AS B ON A.ANIO = B.ANIO
					        AND A.TRIMESTRE = B.TRIMESTRE
					    GROUP BY CONCESIONARIO) AS E ON D.CONCESIONARIO = E.CONCESIONARIO
					ORDER BY D.CONCESIONARIO";
    		$query=$this->db->query($sql);

    		if ($query->num_rows() > 0) {
				return $query;
			}else{
				return $query;
				//return '';
			}	

    	}
		
		public function get_reporte1_verificacion2() {
			$str = "";
			$sql = "SELECT CAL, ATRIB1,ATRIB2,ATRIB3,ATRIB4,ATRIB5,ATRIB6,ATRIB7,ATRIB8,ATRIB9,ATRIB10,ATRIB11,ATRIB12,ATRIB13,ATRIB14,DLIINDEX, REFFERRALINDEX,EXPCOMPRA, ATRIB1+ATRIB2+ATRIB3+ATRIB4+ATRIB5+ATRIB6+ATRIB7+ATRIB8+ATRIB9+ATRIB10+ATRIB11+ATRIB12+ATRIB13+ATRIB14 as CSIINDEX
			from verificacion2 where GRUPO = '" . $this->input->post('elegido1') .  "'  AND CONCESIONARIO = '" . $this->input->post('elegido2') .  "'  AND ANIO = " . substr($this->input->post('elegido3'),0,4) .  " AND TRIMESTRE = " . substr($this->input->post('elegido3'),4,1) .  " ORDER BY CAL";

	
			$query = $this->db->query($sql);
			return $query;
			
    	}
		
		public function get_reporte2_total() {
			$str = "";			
			$val1 = "";		
		   	
			if ($this->input->post('elegido2')!=1)
			{
				$val1 ="AND CONCESIONARIO =  '".$this->input->post('elegido2')."'";
			}
			
			$sql = "	
			SELECT UPPER(CONCESIONARIO) AS CONCESIONARIO,ANIO, TRIMESTRE,SUM(CSIINDEX) AS CSIPROMEDIO
					FROM(
					SELECT CONCESIONARIO,ANIO, TRIMESTRE,ROUND(SUM( CASE WHEN CAL =1 THEN CSIINDEX * -50
									  WHEN CAL =2 THEN CSIINDEX * 0
									  WHEN CAL =3 THEN CSIINDEX * 50
									  WHEN CAL =4 THEN CSIINDEX * 80
									  WHEN CAL =5 THEN CSIINDEX * 100 END)/SUM(CSIINDEX),1) AS CSIINDEX
											
					FROM
					(     select  CONCESIONARIO, ANIO, TRIMESTRE, CAL, ATRIB1+ATRIB2+ATRIB3+ATRIB4+ATRIB5+ATRIB6+ATRIB7+ATRIB8+ATRIB9+ATRIB10+ATRIB11+ATRIB12+ATRIB13+ATRIB14 as CSIINDEX
						  from VERIFICACION2 
						  WHERE GRUPO = '" . $this->input->post('elegido1') .  "' 
						  order by 1) AS H 					 
					GROUP BY CONCESIONARIO,ANIO, TRIMESTRE
					UNION  
					SELECT C.CONCESIONARIO, B.ANIO, B.TRIMESTRE, 0
					FROM (                SELECT DISTINCT CONCESIONARIO
                                          FROM BASE 
                                          WHERE GRUPO = '" . $this->input->post('elegido1') .  "') C
					CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS B 
	) AS A
					GROUP BY CONCESIONARIO,ANIO, TRIMESTRE
					ORDER BY CONCESIONARIO,ANIO, TRIMESTRE";

			$query = $this->db->query($sql);
			return $query;
			
    	}
		
		
		public function get_resumen(){
                               $str = "";
                               $sql =    "SELECT V.ORDEN,V.CONCESIONARIO, V.ANIO, V.TRIMESTRE,
SUM(ATRIB1)AS ATRIB1,SUM(ATRIB2) AS ATRIB2 , SUM(ATRIB3) AS ATRIB3, SUM(ATRIB4) AS ATRIB4, SUM(ATRIB5) AS ATRIB5,
                                               SUM(ATRIB6) AS ATRIB6,SUM(ATRIB7) AS ATRIB7, SUM(ATRIB8) AS ATRIB8,SUM(ATRIB9) AS ATRIB9,SUM(ATRIB10) AS ATRIB10,
                                               SUM(ATRIB11) AS ATRIB11,SUM(ATRIB12) AS ATRIB12,SUM(ATRIB13) AS ATRIB13,SUM(ATRIB14) AS ATRIB14,SUM(CSIINDEX) AS CSIINDEX,
                                               SUM(DLIINDEX)AS DLIINDEX,SUM(REFFERRALINDEX)AS REFFERRALINDEX 
FROM (
SELECT B.ORDEN, A.CONCESIONARIO , B.ANIO, B.TRIMESTRE,
                               ROUND (SUM(CASE  WHEN CAL =1 THEN ATRIB1 * -50
                               WHEN CAL =2 THEN ATRIB1  * 0
                               WHEN CAL =3 THEN ATRIB1  * 50
                               WHEN CAL =4 THEN ATRIB1  * 80
                               WHEN CAL =5 THEN ATRIB1  * 100
                               ELSE 0 END) / SUM(ATRIB1),1) AS ATRIB1,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB2 * -50
                               WHEN CAL =2 THEN ATRIB2  * 0
                               WHEN CAL =3 THEN ATRIB2  * 50
                               WHEN CAL =4 THEN ATRIB2  * 80
                               WHEN CAL =5 THEN ATRIB2  * 100
                               ELSE 0 END) / SUM(ATRIB2),1) AS ATRIB2,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB3 * -50
                               WHEN CAL =2 THEN ATRIB3  * 0
                               WHEN CAL =3 THEN ATRIB3  * 50
                               WHEN CAL =4 THEN ATRIB3  * 80
                               WHEN CAL =5 THEN ATRIB3  * 100
                               ELSE 0 END) / SUM(ATRIB3),1) AS ATRIB3,
                                ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB4 * -50
                               WHEN CAL =2 THEN ATRIB4  * 0
                               WHEN CAL =3 THEN ATRIB4  * 50
                               WHEN CAL =4 THEN ATRIB4  * 80
                               WHEN CAL =5 THEN ATRIB4  * 100
                               ELSE 0 END) / SUM(ATRIB4),1) AS ATRIB4,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB5 * -50
                               WHEN CAL =2 THEN ATRIB5  * 0
                               WHEN CAL =3 THEN ATRIB5  * 50
                               WHEN CAL =4 THEN ATRIB5  * 80
                               WHEN CAL =5 THEN ATRIB5  * 100
                               ELSE 0 END) / SUM(ATRIB5),1) AS ATRIB5,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB6 * -50
                               WHEN CAL =2 THEN ATRIB6  * 0
                               WHEN CAL =3 THEN ATRIB6  * 50
                               WHEN CAL =4 THEN ATRIB6  * 80
                               WHEN CAL =5 THEN ATRIB6  * 100
                               ELSE 0 END) / SUM(ATRIB6),1) AS ATRIB6,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB7 * -50
                               WHEN CAL =2 THEN ATRIB7  * 0
                               WHEN CAL =3 THEN ATRIB7  * 50
                               WHEN CAL =4 THEN ATRIB7  * 80
                               WHEN CAL =5 THEN ATRIB7  * 100
                               ELSE 0 END) / SUM(ATRIB7),1) AS ATRIB7,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB8 * -50
                               WHEN CAL =2 THEN ATRIB8  * 0
                               WHEN CAL =3 THEN ATRIB8  * 50
                               WHEN CAL =4 THEN ATRIB8  * 80
                               WHEN CAL =5 THEN ATRIB8  * 100
                               ELSE 0 END) / SUM(ATRIB8),1) AS ATRIB8,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB9 * -50
                               WHEN CAL =2 THEN ATRIB9  * 0
                               WHEN CAL =3 THEN ATRIB9  * 50
                               WHEN CAL =4 THEN ATRIB9  * 80
                               WHEN CAL =5 THEN ATRIB9  * 100
                               ELSE 0 END) / SUM(ATRIB9),1) AS ATRIB9,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB10 * -50
                               WHEN CAL =2 THEN ATRIB10  * 0
                               WHEN CAL =3 THEN ATRIB10  * 50
                               WHEN CAL =4 THEN ATRIB10  * 80
                               WHEN CAL =5 THEN ATRIB10  * 100
                               ELSE 0 END) / SUM(ATRIB10),1) AS ATRIB10,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB11 * -50
                               WHEN CAL =2 THEN ATRIB11  * 0
                               WHEN CAL =3 THEN ATRIB11  * 50
                               WHEN CAL =4 THEN ATRIB11  * 80
                               WHEN CAL =5 THEN ATRIB11  * 100
                               ELSE 0 END) / SUM(ATRIB11),1) AS ATRIB11,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB12 * -50
                               WHEN CAL =2 THEN ATRIB12  * 0
                               WHEN CAL =3 THEN ATRIB12  * 50
                               WHEN CAL =4 THEN ATRIB12  * 80
                               WHEN CAL =5 THEN ATRIB12  * 100
                               ELSE 0 END) / SUM(ATRIB12),1) AS ATRIB12,                     
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB13 * -50
                               WHEN CAL =2 THEN ATRIB13  * 0
                               WHEN CAL =3 THEN ATRIB13 * 50
                               WHEN CAL =4 THEN ATRIB13  * 80
                               WHEN CAL =5 THEN ATRIB13  * 100
                               ELSE 0 END) / SUM(ATRIB13),1) AS ATRIB13,
                               ROUND(SUM(CASE  WHEN CAL =1 THEN ATRIB14 * -50
                               WHEN CAL =2 THEN ATRIB14  * 0
                               WHEN CAL =3 THEN ATRIB14  * 50
                               WHEN CAL =4 THEN ATRIB14  * 80
                               WHEN CAL =5 THEN ATRIB14  * 100
                               ELSE 0 END) / SUM(ATRIB14),1) AS ATRIB14,
                               ROUND(SUM(CASE        WHEN CAL =1 THEN (ATRIB1+ATRIB2+ATRIB3+ATRIB4+ATRIB5+ATRIB6+ATRIB7+ATRIB8+ATRIB9+ATRIB10+ATRIB11+ATRIB12+ATRIB13+ATRIB14) * -50 
                               WHEN CAL =2 THEN (ATRIB1+ATRIB2+ATRIB3+ATRIB4+ATRIB5+ATRIB6+ATRIB7+ATRIB8+ATRIB9+ATRIB10+ATRIB11+ATRIB12+ATRIB13+ATRIB14)   * 0
                               WHEN CAL =3 THEN (ATRIB1+ATRIB2+ATRIB3+ATRIB4+ATRIB5+ATRIB6+ATRIB7+ATRIB8+ATRIB9+ATRIB10+ATRIB11+ATRIB12+ATRIB13+ATRIB14)  * 50
                               WHEN CAL =4 THEN (ATRIB1+ATRIB2+ATRIB3+ATRIB4+ATRIB5+ATRIB6+ATRIB7+ATRIB8+ATRIB9+ATRIB10+ATRIB11+ATRIB12+ATRIB13+ATRIB14)  * 80
                               WHEN CAL =5 THEN (ATRIB1+ATRIB2+ATRIB3+ATRIB4+ATRIB5+ATRIB6+ATRIB7+ATRIB8+ATRIB9+ATRIB10+ATRIB11+ATRIB12+ATRIB13+ATRIB14)* 100
                               ELSE 0 END) / SUM(ATRIB1+ATRIB2+ATRIB3+ATRIB4+ATRIB5+ATRIB6+ATRIB7+ATRIB8+ATRIB9+ATRIB10+ATRIB11+ATRIB12+ATRIB13+ATRIB14),1) AS CSIINDEX,                    
                               ROUND(SUM(CASE  WHEN CAL =1 THEN DLIINDEX * -50
                               WHEN CAL =2 THEN DLIINDEX   * 0
                               WHEN CAL =3 THEN DLIINDEX   * 50
                               WHEN CAL =4 THEN DLIINDEX  * 80
                               WHEN CAL =5 THEN DLIINDEX  * 100
                               ELSE 0 END) / SUM(DLIINDEX),1) AS DLIINDEX, 
                               ROUND(SUM(CASE  WHEN CAL =1 THEN REFFERRALINDEX * -50
                               WHEN CAL =2 THEN REFFERRALINDEX   * 0
                               WHEN CAL =3 THEN REFFERRALINDEX  * 50
                               WHEN CAL =4 THEN REFFERRALINDEX  * 80
                               WHEN CAL =5 THEN REFFERRALINDEX * 100
                               ELSE 0 END) / SUM(REFFERRALINDEX),1) AS REFFERRALINDEX 
                               
                               
                
FROM VERIFICACION2 A             
INNER JOIN (
                SELECT M.CONCESIONARIO , M.ANIO , M.TRIMESTRE, M.ORDEN, ROUND(SUM(M.CAL1*-50 + M.CAL2*0 + M.CAL3*50 + M.CAL4*80 + M.CAL5*100)/SUM(M.CAL1 + M.CAL2 + M.CAL3 + M.CAL4 + M.CAL5),1) AS YYY, ROUND(AVG(CSIINDEX),1) AS CSIINDEX
                FROM  VERIFICACION1 M
                INNER JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS N ON  M.ANIO = N.ANIO AND
                                                                                                                                                                                                                                                                                                                                                         M.TRIMESTRE = N.TRIMESTRE
                GROUP BY M.CONCESIONARIO , M.ANIO, M.TRIMESTRE, M.ORDEN ) AS B ON                A.CONCESIONARIO = B.CONCESIONARIO AND
                                                                                                                                                                                                                                                                                          A.ANIO = B.ANIO AND
                                                                                                                                                                                                                                                                                          A.TRIMESTRE = B.TRIMESTRE
GROUP BY B.ORDEN, A.CONCESIONARIO , B.ANIO, B.TRIMESTRE
UNION
SELECT P.ORDEN ,P.CONCESIONARIO , Q.ANIO, Q.TRIMESTRE, 0, 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0
FROM (                SELECT  DISTINCT CONCESIONARIO, ORDEN
                               FROM VERIFICACION1 ) P
CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS Q
) AS V
GROUP BY V.ORDEN, V.CONCESIONARIO, V.ANIO, V.TRIMESTRE
ORDER BY V.ORDEN, V.ANIO DESC, V.TRIMESTRE DESC";
$query = $this->db->query($sql);
                                               return $query;
}
public function resumen_resumen()
                {
                               $sql="SELECT 
                                                                   CAL,
                                                                   SUM(ATRIB1) AS ATRIB1,
                                                                   SUM(ATRIB2) AS ATRIB2,
                                                                   SUM(ATRIB3) AS ATRIB3,
                                                                   SUM(ATRIB4) AS ATRIB4,
                                                                   SUM(ATRIB5) AS ATRIB5,
                                                                   SUM(ATRIB6) AS ATRIB6,
                                                                   SUM(ATRIB7) AS ATRIB7,
                                                                   SUM(ATRIB8) AS ATRIB8,
                                                                   SUM(ATRIB9) AS ATRIB9,
                                                                   SUM(ATRIB10) AS ATRIB10,
                                                                   SUM(ATRIB11) AS ATRIB11,
                                                                   SUM(ATRIB12) AS ATRIB12,
                                                                   SUM(ATRIB13) AS ATRIB13,
                                                                   SUM(ATRIB14) AS ATRIB14,
                                                                   SUM(ATRIB1 + ATRIB2 + ATRIB3 + ATRIB4 + ATRIB5 + ATRIB6 + ATRIB7 + ATRIB8 + ATRIB9 + ATRIB10 + ATRIB11 + ATRIB12 + ATRIB13 + ATRIB14) AS CSITOTAL,
                                                                   SUM(DLIINDEX) AS DLIINDEX,
                                                                   SUM(REFFERRALINDEX) AS REFFERRALINDEX
                                                               FROM
                                                                   VERIFICACION2
                                                               GROUP BY CAL";
                               $query=$this->db->query($sql);

                               return $query;
                }

		
		
		
		public function get_reporte2(){
		   $str = "";		   
		   $val1 = "";		
		   $val2 = "";		
		   
			if ($this->input->post('elegido2')!=1)
			{
				$val1 ="AND A.CONCESIONARIO = '".$this->input->post('elegido2')."'";
				$val2 ="AND CONCESIONARIO =  '".$this->input->post('elegido2')."'";
			}
	
	$sql="
SELECT UPPER(ZZZ1.CONCESIONARIO) AS CONCESIONARIO, ZZZ1.VENDEDOR, ZZZ1.ANIO, ZZZ1.TRIMESTRE, 		ZZZ1.NRO_ENCUESTA, ZZZ1.CSIPROMEDIO,
	   CASE WHEN ZZZ2.CONCESIONARIO IS NULL THEN 0 ELSE 1 END AS FLAG
FROM (

SELECT YYY.CONCESIONARIO,YYY.VENDEDOR, YYY.ANIO, YYY.TRIMESTRE, YYY.NRO_ENCUESTA, YYY.CSIPROMEDIO
FROM (
SELECT CONCESIONARIO, VENDEDOR, ROUND(SUM(NRO_ENCUESTA * CSIPROMEDIO)/SUM(NRO_ENCUESTA),1) AS CSIPROMEDIO
FROM
(
                SELECT  CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE, ROUND(SUM(CSIPROMEDIO),1) AS CSIPROMEDIO, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA
                FROM (
                               SELECT A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO,COUNT(1) AS NRO_ENCUESTA
                               FROM BASE A
                               INNER JOIN VERIFICACION1 AS B ON A.TIPO = B.TIPO AND                       
                                                                A.GRUPO = B.GRUPO AND
                                                                A.CONCESIONARIO = B.CONCESIONARIO AND
                                                                A.VENDEDOR = B.VENDEDOR AND 
                                                                A.ANIO = B.ANIO AND
                                                                A.TRIMESTRE = B.TRIMESTRE AND
                                                                A.ID = B.ID
                               INNER JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS C ON  B.ANIO = C.ANIO AND
                                                                                                                                                                                                                                                                                                                                                                        B.TRIMESTRE = C.TRIMESTRE                                                                                                                                                  
                               WHERE A.GRUPO = '" . $this->input->post('elegido1') .  "' ". $val1. "
                               GROUP BY A.CONCESIONARIO,A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO
                               UNION
                               SELECT A.CONCESIONARIO, A.VENDEDOR, B.ANIO, B.TRIMESTRE,0, 0
                               FROM ( SELECT DISTINCT CONCESIONARIO, VENDEDOR 
                                      FROM BASE 
                                      WHERE GRUPO = '" . $this->input->post('elegido1') .  "' ". $val2. ") A
                               CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) B 
                ) AS T
                GROUP BY CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE) AS U
GROUP BY CONCESIONARIO, VENDEDOR) AS XXX
INNER JOIN (SELECT K.CONCESIONARIO, K.VENDEDOR, Z.ANIO, Z.TRIMESTRE, Z.NRO_ENCUESTA, Z.CSIPROMEDIO 
FROM (
                SELECT CONCESIONARIO,VENDEDOR, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA,ROUND(SUM(NRO_ENCUESTA * CSIPROMEDIO)/SUM(NRO_ENCUESTA),1) AS CSIPROMEDIO
                FROM
                (
                               SELECT  CONCESIONARIO,VENDEDOR, ANIO, TRIMESTRE, SUM(CSIPROMEDIO) AS CSIPROMEDIO, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA
                               FROM (
                                               SELECT A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO,COUNT(1) AS NRO_ENCUESTA
                                               FROM BASE A
                                               INNER JOIN VERIFICACION1 AS B ON A.TIPO = B.TIPO AND  A.CONCESIONARIO = B.CONCESIONARIO AND
                                                                                                     A.VENDEDOR = B.VENDEDOR AND 
                                                                                                     A.ANIO = B.ANIO AND
                                                                                                     A.TRIMESTRE = B.TRIMESTRE AND
                                                                                                     A.ID = B.ID
                                               INNER JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS C ON  B.ANIO = C.ANIO AND B.TRIMESTRE = C.TRIMESTRE                                                                                                                                                  
                                               WHERE A.GRUPO = '" . $this->input->post('elegido1') .  "' ". $val1. "
                                               GROUP BY A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO
                                               UNION
                                               SELECT A.CONCESIONARIO, A.VENDEDOR, B.ANIO, B.TRIMESTRE,0, 0
                                               FROM (  	SELECT DISTINCT CONCESIONARIO,VENDEDOR 
														FROM BASE 
                                                        WHERE GRUPO = '" . $this->input->post('elegido1') .  "' ". $val2. ") A
                                               CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) B 
                               ) AS T
                               GROUP BY CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE) AS U
                GROUP BY CONCESIONARIO, VENDEDOR) AS K
INNER JOIN (
SELECT  CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA, SUM(CSIPROMEDIO) AS CSIPROMEDIO
FROM (
                SELECT A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO,COUNT(1) AS NRO_ENCUESTA
                FROM BASE A
                INNER JOIN VERIFICACION1 AS B ON A.VENDEDOR = B.VENDEDOR AND A.TIPO = B.TIPO AND                    
                                                                             A.GRUPO = B.GRUPO AND
                                                                             A.CONCESIONARIO = B.CONCESIONARIO AND
                                                                             A.ANIO = B.ANIO AND
                                                                             A.TRIMESTRE = B.TRIMESTRE AND
																			 A.ID = B.ID		
                INNER JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS C ON  B.ANIO = C.ANIO AND B.TRIMESTRE = C.TRIMESTRE                                                                                                                                                 
                WHERE A.GRUPO = '" . $this->input->post('elegido1') .  "' ". $val1. "
                GROUP BY A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO
                UNION
                SELECT A.CONCESIONARIO, A.VENDEDOR, B.ANIO, B.TRIMESTRE,0, 0
                FROM (                SELECT DISTINCT CONCESIONARIO, VENDEDOR 
                                               FROM BASE 
                                               WHERE GRUPO = '" . $this->input->post('elegido1') .  "' ". $val2. ") A
                CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS B 
) AS T
GROUP BY CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE) AS Z ON 	K.VENDEDOR = Z.VENDEDOR AND
															K.CONCESIONARIO = Z.CONCESIONARIO) AS YYY ON 	XXX.VENDEDOR = YYY.VENDEDOR AND
											XXX.CONCESIONARIO = YYY.CONCESIONARIO) AS ZZZ1
LEFT JOIN (
SELECT CONCESIONARIO, ANIO, TRIMESTRE, MAX(CSIPROMEDIO) AS CSIPROMEDIO FROM
(
SELECT CONCESIONARIO,VENDEDOR,ANIO, TRIMESTRE,  SUM(NRO_ENCUESTA) AS NRO_ENCUESTA, ROUND(SUM(NRO_ENCUESTA * CSIPROMEDIO)/SUM(NRO_ENCUESTA),1) AS CSIPROMEDIO
FROM
(
	SELECT  CONCESIONARIO,VENDEDOR, ANIO, TRIMESTRE, SUM(CSIPROMEDIO) AS CSIPROMEDIO, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA
    FROM (
          SELECT A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO,COUNT(1) AS NRO_ENCUESTA
          FROM BASE A
          INNER JOIN VERIFICACION1 AS B ON A.TIPO = B.TIPO AND  A.CONCESIONARIO = B.CONCESIONARIO AND
																A.VENDEDOR = B.VENDEDOR AND 
                                                                A.ANIO = B.ANIO AND
                                                                A.TRIMESTRE = B.TRIMESTRE AND
                                                                A.ID = B.ID
		  INNER JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1 ORDER BY ANIO DESC, TRIMESTRE DESC LIMIT 1) AS C ON  B.ANIO = C.ANIO AND B.TRIMESTRE = C.TRIMESTRE                                                                                                                                                  
          WHERE A.GRUPO = '" . $this->input->post('elegido1') .  "' ". $val1. "
          GROUP BY A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO
          UNION
          SELECT A.CONCESIONARIO, A.VENDEDOR, B.ANIO, B.TRIMESTRE,0, 0
          FROM (  	SELECT DISTINCT CONCESIONARIO,VENDEDOR 
					FROM BASE 
                    WHERE GRUPO = '" . $this->input->post('elegido1') .  "' ". $val2. ") A
                    CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1 ORDER BY ANIO DESC, TRIMESTRE DESC LIMIT 1) B 
		  ) AS T
		  GROUP BY CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE) AS U
GROUP BY CONCESIONARIO, VENDEDOR,ANIO, TRIMESTRE ) AS Q
WHERE NRO_ENCUESTA > 2
GROUP BY CONCESIONARIO, ANIO, TRIMESTRE
) AS ZZZ2 ON ZZZ1.CONCESIONARIO = ZZZ2.CONCESIONARIO AND
										ZZZ1.ANIO = ZZZ2.ANIO AND
										ZZZ1.TRIMESTRE = ZZZ2.TRIMESTRE AND
										ZZZ1.CSIPROMEDIO = ZZZ2.CSIPROMEDIO
ORDER BY ZZZ1.CONCESIONARIO, ZZZ1.CSIPROMEDIO DESC, ZZZ1.ANIO, ZZZ1.TRIMESTRE";

	
	
	/*
			$sql ="
SELECT UPPER(YYY.CONCESIONARIO) AS CONCESIONARIO,YYY.VENDEDOR, YYY.ANIO, YYY.TRIMESTRE, YYY.NRO_ENCUESTA, YYY.CSIPROMEDIO
FROM (
SELECT CONCESIONARIO, VENDEDOR, ROUND(SUM(NRO_ENCUESTA * CSIPROMEDIO)/SUM(NRO_ENCUESTA),1) AS CSIPROMEDIO
FROM
(
                SELECT  CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE, ROUND(SUM(CSIPROMEDIO),1) AS CSIPROMEDIO, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA
                FROM (
                               SELECT A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO,COUNT(1) AS NRO_ENCUESTA
                               FROM BASE A
                               INNER JOIN VERIFICACION1 AS B ON A.TIPO = B.TIPO AND                       
                                                                A.GRUPO = B.GRUPO AND
                                                                A.CONCESIONARIO = B.CONCESIONARIO AND
                                                                A.VENDEDOR = B.VENDEDOR AND 
                                                                A.ANIO = B.ANIO AND
                                                                A.TRIMESTRE = B.TRIMESTRE AND
                                                                A.ID = B.ID
                               INNER JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS C ON  B.ANIO = C.ANIO AND
                                                                                                                                                                                                                                                                                                                                                                        B.TRIMESTRE = C.TRIMESTRE                                                                                                                                                  
                               WHERE A.GRUPO = '" . $this->input->post('elegido1') .  "' ". $val1. "
                               GROUP BY A.CONCESIONARIO,A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO
                               UNION
                               SELECT A.CONCESIONARIO, A.VENDEDOR, B.ANIO, B.TRIMESTRE,0, 0
                               FROM ( SELECT DISTINCT CONCESIONARIO, VENDEDOR 
                                      FROM BASE 
                                      WHERE GRUPO = '" . $this->input->post('elegido1') .  "' ".$val2.") A
                               CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) B 
                ) AS T
                GROUP BY CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE) AS U
GROUP BY CONCESIONARIO, VENDEDOR) AS XXX
INNER JOIN (SELECT K.CONCESIONARIO, K.VENDEDOR, Z.ANIO, Z.TRIMESTRE, Z.NRO_ENCUESTA, Z.CSIPROMEDIO 
FROM (
                SELECT CONCESIONARIO,VENDEDOR, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA,ROUND(SUM(NRO_ENCUESTA * CSIPROMEDIO)/SUM(NRO_ENCUESTA),1) AS CSIPROMEDIO
                FROM
                (
                               SELECT  CONCESIONARIO,VENDEDOR, ANIO, TRIMESTRE, SUM(CSIPROMEDIO) AS CSIPROMEDIO, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA
                               FROM (
                                               SELECT A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO,COUNT(1) AS NRO_ENCUESTA
                                               FROM BASE A
                                               INNER JOIN VERIFICACION1 AS B ON A.TIPO = B.TIPO AND  A.CONCESIONARIO = B.CONCESIONARIO AND
                                                                                                     A.VENDEDOR = B.VENDEDOR AND 
                                                                                                     A.ANIO = B.ANIO AND
                                                                                                     A.TRIMESTRE = B.TRIMESTRE AND
                                                                                                     A.ID = B.ID
                                               INNER JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS C ON  B.ANIO = C.ANIO AND B.TRIMESTRE = C.TRIMESTRE                                                                                                                                                  
                                               WHERE A.GRUPO = '" . $this->input->post('elegido1') .  "' ".$val1."
                                               GROUP BY A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO
                                               UNION
                                               SELECT A.CONCESIONARIO, A.VENDEDOR, B.ANIO, B.TRIMESTRE,0, 0
                                               FROM (  	SELECT DISTINCT CONCESIONARIO,VENDEDOR 
														FROM BASE 
                                                        WHERE GRUPO = '" . $this->input->post('elegido1') .  "' ".$val2.") A
                                               CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) B 
                               ) AS T
                               GROUP BY CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE) AS U
                GROUP BY CONCESIONARIO, VENDEDOR) AS K
INNER JOIN (
SELECT  CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE, SUM(NRO_ENCUESTA) AS NRO_ENCUESTA, SUM(CSIPROMEDIO) AS CSIPROMEDIO
FROM (
                SELECT A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO,COUNT(1) AS NRO_ENCUESTA
                FROM BASE A
                INNER JOIN VERIFICACION1 AS B ON A.VENDEDOR = B.VENDEDOR AND A.TIPO = B.TIPO AND                    
                                                                             A.GRUPO = B.GRUPO AND
                                                                             A.CONCESIONARIO = B.CONCESIONARIO AND
                                                                             A.ANIO = B.ANIO AND
                                                                             A.TRIMESTRE = B.TRIMESTRE AND
																			 A.ID = B.ID		
                INNER JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS C ON  B.ANIO = C.ANIO AND B.TRIMESTRE = C.TRIMESTRE                                                                                                                                                 
                WHERE A.GRUPO = '" . $this->input->post('elegido1') .  "' ".$val1."
                GROUP BY A.CONCESIONARIO, A.VENDEDOR, A.ANIO, A.TRIMESTRE, B.CSIPROMEDIO
                UNION
                SELECT A.CONCESIONARIO, A.VENDEDOR, B.ANIO, B.TRIMESTRE,0, 0
                FROM (                SELECT DISTINCT CONCESIONARIO, VENDEDOR 
                                               FROM BASE 
                                               WHERE GRUPO = '" . $this->input->post('elegido1') .  "' ".$val2.") A
                CROSS JOIN (SELECT ANIO, TRIMESTRE FROM PERIODO WHERE ESTADO = 1) AS B 
) AS T
GROUP BY CONCESIONARIO, VENDEDOR, ANIO, TRIMESTRE) AS Z ON 	K.VENDEDOR = Z.VENDEDOR AND
															K.CONCESIONARIO = Z.CONCESIONARIO) AS YYY ON 	XXX.VENDEDOR = YYY.VENDEDOR AND
											XXX.CONCESIONARIO = YYY.CONCESIONARIO
ORDER BY YYY.CONCESIONARIO, XXX.CSIPROMEDIO DESC, YYY.ANIO, YYY.TRIMESTRE, YYY.VENDEDOR";*/

			   $query = $this->db->query($sql);
			   return $query;                                               
                
		}



}

/* End of file home_model.php */
/* Location: ./application/models/Home/home_model.php */
