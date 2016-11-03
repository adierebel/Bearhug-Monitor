<?php
$main_content = ''; $main_script = '';


function get_store_name ($id) {
	global $mysqli;
	$query_store = $mysqli->query("SELECT * FROM store_market WHERE id='".$id."'");
	$data_num = $query_store->num_rows;
	if ($data_num > 0) {
		$data_result = $query_store->fetch_assoc();
		return $data_result['name'];
	} else {
		return '?';
	}
}
function cut_me($x, $length) {
	if(strlen($x)<=$length) {
		return $x;
	}
	else {
		$y = substr ($x,0,$length) . '...';
		return $y;
	}
}
$store_query = $mysqli->query("SELECT * FROM store_name");
$store_query_num = $store_query->num_rows;
if ($store_query_num > 0) {
	while ($store_data = $store_query->fetch_assoc()) {
		$content = ''; $script = '';
		if ($store_data['market_id'] == 1) { //Lazada
			$lazada_query = $mysqli->query("SELECT * FROM order_lazada WHERE confirm_tgl='".date("d")."' AND confirm_bln='".date("m")."' AND confirm_thn='".date("Y")."' AND status='pending'");
			$lazada_num = $lazada_query->num_rows;
			if ($lazada_num > 0) {
				$num = 1;
				while ($lazada_data = $lazada_query->fetch_assoc()) {
					$content .= '
					<tr> 
						<th scope="row">'.$num.'</th> 
						<td>'.$lazada_data['shipping_name'].'</td> 
						<td>'.cut_me($lazada_data['shipping_address'], 20).'</td> 
						<td><div id="cd'.$lazada_data['id'].'"></div></td> 
					</tr>';
					$script .= "CountDownTimer('".$lazada_data['confirm_thn']."-".$lazada_data['confirm_bln']."-".$lazada_data['confirm_tgl']." ".$lazada_data['confirm_time']."', 'cd".$lazada_data['id']."');";
					$num ++;
				}
			}
		}
		
		
		$main_content .= '
			<div class="col-xs-4">
				<div class="panel panel-default">
				  <div class="panel-heading">
					<h3 class="panel-title">'.$store_data['name'].' '.get_store_name($store_data['market_id']).'</h3>
				  </div>
				  <!-- Table -->
				  <table class="table table-hover"> 
					  <thead> 
						  <tr> 
							  <th>#</th> 
							  <th>Atas Nama</th> 
							  <th>Alamat</th> 
							  <th>Sisa Waktu</th> 
						  </tr> 
					  </thead> 
					  <tbody> 
						  '.$content.'
					  </tbody> 
				  </table>
				</div>
			</div>';
		$main_script .= $script;
	}
}



$main_script .= "
    function CountDownTimer(dt, id) {
        var end = new Date(dt);
        var _second = 1000;
        var _minute = _second * 60;
        var _hour = _minute * 60;
        var _day = _hour * 24;
        var timer;
        function showRemaining() {
            var now = new Date();
            var distance = end - now;
            if (distance < 0) {
                clearInterval(timer);
                document.getElementById(id).innerHTML = 'Telat !';
                return;
            }
            var days = Math.floor(distance / _day);
            var hours = Math.floor((distance % _day) / _hour);
            var minutes = Math.floor((distance % _hour) / _minute);
            var seconds = Math.floor((distance % _minute) / _second);
            //document.getElementById(id).innerHTML = days + ' Hari ';
            document.getElementById(id).innerHTML = hours + ':';
            document.getElementById(id).innerHTML += minutes + ':';
            document.getElementById(id).innerHTML += seconds + '';
        }
        timer = setInterval(showRemaining, 1000);
    }
";

include "core/viewer.template.php";
?>
