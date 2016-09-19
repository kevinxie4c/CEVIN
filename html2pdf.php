<?php
	//This version use mpdf
	$html = stripcslashes($_POST['data']);
	//ini_set("memory_limit","516M");
	include("mpdf60/mpdf.php");
	$mpdf = new mPDF('c', 'A4-L');
	$mpdf->WriteHTML($html);
	$mpdf->Output();
?>