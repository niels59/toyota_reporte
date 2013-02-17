<?php 
	header("Content-type: application/vnd.ms-excel; name='excel' charset=UTF-8");
	header("Content-Disposition: filename=".$name_excel.".xls");
	header ('Content-Transfer-Encoding: binary');
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $excel_file;
 ?>