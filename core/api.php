<?php
if (isset($_GET['mode'])) {
	$mode = $_GET['mode'];
	if ($mode == 1) { //Send Data
		include "core/reader.php";
	}
	elseif ($mode == 2) { //Daftar Toko
		$toko_query = $mysqli->query("SELECT * FROM store_name");
		while ($data = $toko_query->fetch_assoc()) {
			$toko_sub_query = $mysqli->query("SELECT * FROM store_market WHERE id='".$data['market_id']."'");
			$toko_data = $toko_sub_query->fetch_assoc();
			echo $data['id'].'-'.$data['name'].' '.$toko_data['name'];
		}
	} 
	else { //Error
		echo 'Error';
	}
}
?>