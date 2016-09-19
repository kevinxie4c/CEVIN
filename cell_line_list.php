<?php
	include('config.php');
	$query=$_POST['q'];
	$con=mysql_connect(DB_HOST,DB_USER,DB_PASS);
	mysql_select_db(DB_NAME,$con);
	$result=mysql_query("select * from cell_line_list where cell_line like '$query%'");
	$output=array();
	while($row=mysql_fetch_array($result))
	{
		array_push($output, array(cell_line => $row['cell_line'], tissue => $row['tissue']));
	}
	echo json_encode($output);
?>