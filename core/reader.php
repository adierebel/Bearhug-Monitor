<?php
/**
 * ********************************************************
 * Insert Lazada, Elevenia, MatahariMall, Blibli, Tokopedia export file data into database
 * Writen by Adierebel
 * Github https://github.com/adierebel
 * ********************************************************
 */
if (isset ( $_FILES ['file'] )) {
	$temp_name = $_FILES ['file'] ['tmp_name'];
	$file_header = md5 ( preg_replace ( '#[^a-z]#', '', strtolower ( file_get_contents ( $temp_name, NULL, NULL, 0, 50 ) ) ) ); // Get header hash
	// echo $file_header;
	// exit;
	if (($handle = fopen ( $temp_name, "r" )) !== FALSE) {
		$filename = explode ( "-", $_FILES ['file'] ['name'] );
		$store_id = $filename [0];
		if ($file_header == "4e0312d522bd981b3cac340ec49fe2fe") { // Lazada
			read_lazada ( $handle, $store_id );
		} elseif ($file_header == "9e3c4bf427c4d4be2f5b69877678d693") { // Elevenia OLD: 9e3c4bf427c4d4be2f5b69877678d693 / 7b8b965ad4bca0e41ab51de7b31363a1
			read_elevenia ( $handle, $store_id );
		} elseif ($file_header == "c2e9bc45b45d1414dd690d239a490c11") { // MatahariMall
			read_matahari ( $handle, $store_id );
		} elseif ($file_header == "27c09e84b870618e8b073387f027e2d3") { // Blibli
			read_blibli ( $handle, $store_id );
		} elseif ($file_header == "d41d8cd98f00b204e9800998ecf8427e") { // Tokopedia
			echo file_get_contents ( $temp_name );
			// read_tokopedia($handle, $store_id);
		} else {
			echo "2-0-0-0-0-0";
		}
		fclose ( $handle );
	}
}
function read_lazada($handle, $store_id) {
	global $mysqli;
	$row = 1;
	$update_success = 0;
	$update_fail = 0;
	$update_skip = 0;
	$insert_success = 0;
	$insert_fail = 0;
	$error_id = 0; // 0=No Error, 1=DB Error, 2=Not Match
	$error = false;
	while ( ($data = fgetcsv ( $handle, 0, ";" )) !== FALSE ) {
		$num = count ( $data );
		if ($row == 1) { // check lazada header with table title hash
			$is_lazada = md5 ( preg_replace ( '/\s+/', '', implode ( "-", $data ) ) );
			if ($is_lazada != "f532ab78caa84fd295d5ec626d43c05f") { // check with hash
				$error = true;
			}
		} else {
			if ($error == false) { // if no error result
				$ambil_tgl = explode ( " ", $mysqli->real_escape_string ( $data [4] ) );
				$pisah_tgl = explode ( "-", $ambil_tgl [0] );
				$tgl = ltrim ( $pisah_tgl [2], '0' ); // Get Tanggal
				$bln = ltrim ( $pisah_tgl [1], '0' ); // Get Bulan
				$thn = $pisah_tgl [0]; // Get Tahun
				$check_data_query = $mysqli->query ( "SELECT id, store_id, status FROM order_lazada WHERE order_item_id='" . $data [0] . "' AND lazada_id='" . $data [1] . "' AND order_number='" . $data [6] . "'" );
				$check_data_rows = $check_data_query->num_rows;
				$check_data_result = $check_data_query->fetch_assoc ();
				if ($check_data_rows > 0) { // check if order data is exist
					if ($check_data_result ['status'] != $data [47] || $check_data_result ['store_id'] != $store_id) { // check if order status changed
						$timestamp = strtotime('10:09') + (60 * 60) * 4;
						$time = date('H:i', $timestamp);
						if ($mysqli->real_escape_string ($data[47]) == "pending") {
							$update_addon = "confirm_time = '" . $time . "',
							confirm_tgl = '" . date ( "d" ) . "',
							confirm_bln = '" . date ( "m" ) . "',
							confirm_thn = '" . date ( "Y" ) . "',";
						} 
						else {
							$update_addon = "
							confirm_time = '0',
							confirm_tgl = '0',
							confirm_bln = '0',
							confirm_thn = '0',";
						}
						$update_query = "UPDATE order_lazada SET 
						store_id = '" . $mysqli->real_escape_string ( $store_id ) . "',
						tgl = '" . $mysqli->real_escape_string ( $tgl ) . "',
						bln = '" . $mysqli->real_escape_string ( $bln ) . "',
						thn = '" . $mysqli->real_escape_string ( $thn ) . "',
						".$update_addon."
						order_item_id = '" . $mysqli->real_escape_string ( $data [0] ) . "',
						lazada_id = '" . $mysqli->real_escape_string ( $data [1] ) . "',
						seller_sku = '" . $mysqli->real_escape_string ( $data [2] ) . "',
						lazada_sku = '" . $mysqli->real_escape_string ( $data [3] ) . "',
						created_at = '" . $mysqli->real_escape_string ( $data [4] ) . "',
						updated_at = '" . $mysqli->real_escape_string ( $data [5] ) . "',
						order_number = '" . $mysqli->real_escape_string ( $data [6] ) . "',
						customer_name = '" . $mysqli->real_escape_string ( $data [7] ) . "',
						national_registration_number = '" . $mysqli->real_escape_string ( $data [8] ) . "',
						shipping_name = '" . $mysqli->real_escape_string ( $data [9] ) . "',
						shipping_address = '" . $mysqli->real_escape_string ( $data [10] ) . "',
						shipping_address2 = '" . $mysqli->real_escape_string ( $data [11] ) . "',
						shipping_address3 = '" . $mysqli->real_escape_string ( $data [12] ) . "',
						shipping_address4 = '" . $mysqli->real_escape_string ( $data [13] ) . "',
						shipping_address5 = '" . $mysqli->real_escape_string ( $data [14] ) . "',
						shipping_phone_number = '" . $mysqli->real_escape_string ( $data [15] ) . "',
						shipping_phone_number2 = '" . $mysqli->real_escape_string ( $data [16] ) . "',
						shipping_city = '" . $mysqli->real_escape_string ( $data [17] ) . "',
						shipping_postcode = '" . $mysqli->real_escape_string ( $data [18] ) . "',
						shipping_country = '" . $mysqli->real_escape_string ( $data [19] ) . "',
						billing_name = '" . $mysqli->real_escape_string ( $data [20] ) . "',
						billing_address = '" . $mysqli->real_escape_string ( $data [21] ) . "',
						billing_address2 = '" . $mysqli->real_escape_string ( $data [22] ) . "',
						billing_address3 = '" . $mysqli->real_escape_string ( $data [23] ) . "',
						billing_address4 = '" . $mysqli->real_escape_string ( $data [24] ) . "',
						billing_address5 = '" . $mysqli->real_escape_string ( $data [25] ) . "',
						billing_phone_number = '" . $mysqli->real_escape_string ( $data [26] ) . "',
						billing_phone_number2 = '" . $mysqli->real_escape_string ( $data [27] ) . "',
						billing_city = '" . $mysqli->real_escape_string ( $data [28] ) . "',
						billing_postcode = '" . $mysqli->real_escape_string ( $data [29] ) . "',
						billing_country = '" . $mysqli->real_escape_string ( $data [30] ) . "',
						payment_method = '" . $mysqli->real_escape_string ( $data [31] ) . "',
						paid_price = '" . $mysqli->real_escape_string ( $data [32] ) . "',
						unit_price = '" . $mysqli->real_escape_string ( $data [33] ) . "',
						shipping_fee = '" . $mysqli->real_escape_string ( $data [34] ) . "',
						wallet_credits = '" . $mysqli->real_escape_string ( $data [35] ) . "',
						item_name = '" . $mysqli->real_escape_string ( $data [36] ) . "',
						variation = '" . $mysqli->real_escape_string ( $data [37] ) . "',
						cd_shipping_provider = '" . $mysqli->real_escape_string ( $data [38] ) . "',
						shipping_provider = '" . $mysqli->real_escape_string ( $data [39] ) . "',
						shipment_type_name = '" . $mysqli->real_escape_string ( $data [40] ) . "',
						shipping_provider_type = '" . $mysqli->real_escape_string ( $data [41] ) . "',
						cd_tracking_code = '" . $mysqli->real_escape_string ( $data [42] ) . "',
						tracking_code = '" . $mysqli->real_escape_string ( $data [43] ) . "',
						tracking_url = '" . $mysqli->real_escape_string ( $data [44] ) . "',
						promised_shipping_time = '" . $mysqli->real_escape_string ( $data [45] ) . "',
						premium = '" . $mysqli->real_escape_string ( $data [46] ) . "',
						status = '" . $mysqli->real_escape_string ( $data [47] ) . "',
						reason = '" . $mysqli->real_escape_string ( $data [48] ) . "'
						WHERE id='" . $check_data_result ['id'] . "'
						";
						if ($mysqli->query ( $update_query )) { // update Database
							$update_success ++;
						} else {
							$update_fail ++;
						}
					} else {
						$update_skip ++;
					}
				} 
				else { // if order data not found
					$timestamp = strtotime('10:09') + (60 * 60) * 4;
					$time = date('H:i', $timestamp);
					if ($mysqli->real_escape_string ($data[47]) == "pending") {
						$insert_addon = "confirm_time = '" . $time . "',
						confirm_tgl = '" . date ( "d" ) . "',
						confirm_bln = '" . date ( "m" ) . "',
						confirm_thn = '" . date ( "Y" ) . "',";
					} 
					else {
						$insert_addon = "
						confirm_time = '0',
						confirm_tgl = '0',
						confirm_bln = '0',
						confirm_thn = '0',";
					}
					$insert_query = "INSERT INTO order_lazada SET 
					store_id = '" . $mysqli->real_escape_string ( $store_id ) . "',
					tgl = '" . $mysqli->real_escape_string ( $tgl ) . "',
					bln = '" . $mysqli->real_escape_string ( $bln ) . "',
					thn = '" . $mysqli->real_escape_string ( $thn ) . "',
					".$insert_addon."
					order_item_id = '" . $mysqli->real_escape_string ( $data [0] ) . "',
					lazada_id = '" . $mysqli->real_escape_string ( $data [1] ) . "',
					seller_sku = '" . $mysqli->real_escape_string ( $data [2] ) . "',
					lazada_sku = '" . $mysqli->real_escape_string ( $data [3] ) . "',
					created_at = '" . $mysqli->real_escape_string ( $data [4] ) . "',
					updated_at = '" . $mysqli->real_escape_string ( $data [5] ) . "',
					order_number = '" . $mysqli->real_escape_string ( $data [6] ) . "',
					customer_name = '" . $mysqli->real_escape_string ( $data [7] ) . "',
					national_registration_number = '" . $mysqli->real_escape_string ( $data [8] ) . "',
					shipping_name = '" . $mysqli->real_escape_string ( $data [9] ) . "',
					shipping_address = '" . $mysqli->real_escape_string ( $data [10] ) . "',
					shipping_address2 = '" . $mysqli->real_escape_string ( $data [11] ) . "',
					shipping_address3 = '" . $mysqli->real_escape_string ( $data [12] ) . "',
					shipping_address4 = '" . $mysqli->real_escape_string ( $data [13] ) . "',
					shipping_address5 = '" . $mysqli->real_escape_string ( $data [14] ) . "',
					shipping_phone_number = '" . $mysqli->real_escape_string ( $data [15] ) . "',
					shipping_phone_number2 = '" . $mysqli->real_escape_string ( $data [16] ) . "',
					shipping_city = '" . $mysqli->real_escape_string ( $data [17] ) . "',
					shipping_postcode = '" . $mysqli->real_escape_string ( $data [18] ) . "',
					shipping_country = '" . $mysqli->real_escape_string ( $data [19] ) . "',
					billing_name = '" . $mysqli->real_escape_string ( $data [20] ) . "',
					billing_address = '" . $mysqli->real_escape_string ( $data [21] ) . "',
					billing_address2 = '" . $mysqli->real_escape_string ( $data [22] ) . "',
					billing_address3 = '" . $mysqli->real_escape_string ( $data [23] ) . "',
					billing_address4 = '" . $mysqli->real_escape_string ( $data [24] ) . "',
					billing_address5 = '" . $mysqli->real_escape_string ( $data [25] ) . "',
					billing_phone_number = '" . $mysqli->real_escape_string ( $data [26] ) . "',
					billing_phone_number2 = '" . $mysqli->real_escape_string ( $data [27] ) . "',
					billing_city = '" . $mysqli->real_escape_string ( $data [28] ) . "',
					billing_postcode = '" . $mysqli->real_escape_string ( $data [29] ) . "',
					billing_country = '" . $mysqli->real_escape_string ( $data [30] ) . "',
					payment_method = '" . $mysqli->real_escape_string ( $data [31] ) . "',
					paid_price = '" . $mysqli->real_escape_string ( $data [32] ) . "',
					unit_price = '" . $mysqli->real_escape_string ( $data [33] ) . "',
					shipping_fee = '" . $mysqli->real_escape_string ( $data [34] ) . "',
					wallet_credits = '" . $mysqli->real_escape_string ( $data [35] ) . "',
					item_name = '" . $mysqli->real_escape_string ( $data [36] ) . "',
					variation = '" . $mysqli->real_escape_string ( $data [37] ) . "',
					cd_shipping_provider = '" . $mysqli->real_escape_string ( $data [38] ) . "',
					shipping_provider = '" . $mysqli->real_escape_string ( $data [39] ) . "',
					shipment_type_name = '" . $mysqli->real_escape_string ( $data [40] ) . "',
					shipping_provider_type = '" . $mysqli->real_escape_string ( $data [41] ) . "',
					cd_tracking_code = '" . $mysqli->real_escape_string ( $data [42] ) . "',
					tracking_code = '" . $mysqli->real_escape_string ( $data [43] ) . "',
					tracking_url = '" . $mysqli->real_escape_string ( $data [44] ) . "',
					promised_shipping_time = '" . $mysqli->real_escape_string ( $data [45] ) . "',
					premium = '" . $mysqli->real_escape_string ( $data [46] ) . "',
					status = '" . $mysqli->real_escape_string ( $data [47] ) . "',
					reason = '" . $mysqli->real_escape_string ( $data [48] ) . "'
					";
					if ($mysqli->query ( $insert_query )) { // insert into Database
						$insert_success ++;
					} else {
						$insert_fail ++;
					}
				}
			} else { // if lazada header not match
				$error_id = 2;
				break;
			}
		}
		$row ++;
	}
	echo $error_id . '-' . $update_success . '-' . $update_fail . '-' . $update_skip . '-' . $insert_success . '-' . $insert_fail . '-' . $store_id;
}
function read_elevenia($handle, $store_id) {
	global $mysqli;
	$row = 1;
	$update_success = 0;
	$update_fail = 0;
	$update_skip = 0;
	$insert_success = 0;
	$insert_fail = 0;
	$error_id = 0; // 0=No Error, 1=DB Error, 2=Not Match
	$error = false;
	$out = "";
	
	while ( ($data = fgetcsv ( $handle, 0, ";" )) !== FALSE ) {
		$num = count ( $data );
		if ($row > 2) { // check contents row
			if ($data [0] != '') { // skip empty row
				$ambil_tgl = explode ( " ", $mysqli->real_escape_string ( $data [4] ) );
				$pisah_tgl = explode ( "/", $ambil_tgl [0] );
				$tgl = ltrim ( $pisah_tgl [0], '0' ); // Get Tanggal
				$bln = ltrim ( $pisah_tgl [1], '0' ); // Get Bulan
				$thn = $pisah_tgl [2]; // Get Tahun
				$check_data_query = $mysqli->query ( "SELECT id, store_id, status_pesanan FROM order_elevenia WHERE nomor='" . $data [0] . "' AND nomor_pemesanan='" . $data [2] . "'" );
				$check_data_rows = $check_data_query->num_rows;
				$check_data_result = $check_data_query->fetch_assoc ();
				
				$query_addon = "confirm_tgl = '" . date ( "j" ) . "',
						confirm_bln = '" . date ( "n" ) . "',
						confirm_thn = '" . date ( "Y" ) . "',";
				
				if ($check_data_rows > 0) { // check if order data is exist
					if ($check_data_result ['status_pesanan'] != $data [1] || $check_data_result ['store_id'] != $store_id) { // check if order status changed
						$update_query = "UPDATE order_elevenia SET 
						store_id = '" . $mysqli->real_escape_string ( $store_id ) . "',
						tgl = '" . $mysqli->real_escape_string ( $tgl ) . "',
						bln = '" . $mysqli->real_escape_string ( $bln ) . "',
						thn = '" . $mysqli->real_escape_string ( $thn ) . "',
						" . $query_addon . "
						nomor = '" . $mysqli->real_escape_string ( $data [0] ) . "',
						status_pesanan = '" . $mysqli->real_escape_string ( $data [1] ) . "',
						nomor_pemesanan = '" . $mysqli->real_escape_string ( $data [2] ) . "',
						urutan_pemesanan = '" . $mysqli->real_escape_string ( $data [3] ) . "',
						tanggal_transaksi = '" . $mysqli->real_escape_string ( $data [4] ) . "',
						nomor_pengiriman = '" . $mysqli->real_escape_string ( $data [5] ) . "',
						nama_produk = '" . $mysqli->real_escape_string ( $data [6] ) . "',
						opsi = '" . $mysqli->real_escape_string ( $data [7] ) . "',
						kuantitas = '" . $mysqli->real_escape_string ( $data [8] ) . "',
						jumlah_pemesanan = '" . $mysqli->real_escape_string ( $data [9] ) . "',
						jumlah_pembayaran_kurangi_diskon = '" . $mysqli->real_escape_string ( $data [10] ) . "',
						penerima = '" . $mysqli->real_escape_string ( $data [11] ) . "',
						metode_pengiriman = '" . $mysqli->real_escape_string ( $data [12] ) . "',
						perusahaan_pengiriman = '" . $mysqli->real_escape_string ( $data [13] ) . "',
						kode_perusahaan_jasa_pengiriman = '" . $mysqli->real_escape_string ( $data [14] ) . "',
						booking_code_pre_awb = '" . $mysqli->real_escape_string ( $data [15] ) . "',
						nomor_resi_pengiriman = '" . $mysqli->real_escape_string ( $data [16] ) . "',
						tanggal_konfirmasi_pemesanan = '" . $mysqli->real_escape_string ( $data [17] ) . "',
						tanggal_proses_pengiriman = '" . $mysqli->real_escape_string ( $data [18] ) . "',
						keterlambatan_pengiriman = '" . $mysqli->real_escape_string ( $data [19] ) . "',
						tanggal_pengiriman_yang_diharapkan = '" . $mysqli->real_escape_string ( $data [20] ) . "',
						status_bundel = '" . $mysqli->real_escape_string ( $data [21] ) . "',
						ongkos_kirim = '" . $mysqli->real_escape_string ( $data [22] ) . "',
						voucher_ongkos_kirim = '" . $mysqli->real_escape_string ( $data [23] ) . "',
						biaya_asuransi_pengiriman = '" . $mysqli->real_escape_string ( $data [24] ) . "',
						ongkos_kirim_ditanggung_pelanggan = '" . $mysqli->real_escape_string ( $data [25] ) . "',
						nomor_ponsel = '" . $mysqli->real_escape_string ( $data [26] ) . "',
						nomor_telepon = '" . $mysqli->real_escape_string ( $data [27] ) . "',
						alamat = '" . $mysqli->real_escape_string ( $data [28] ) . "',
						keterangan_tambahan_pengiriman = '" . $mysqli->real_escape_string ( $data [29] ) . "',
						pembeli = '" . $mysqli->real_escape_string ( $data [30] ) . "',
						id_pembeli = '" . $mysqli->real_escape_string ( $data [31] ) . "',
						nomor_produk = '" . $mysqli->real_escape_string ( $data [32] ) . "',
						kode_produk_penjual = '" . $mysqli->real_escape_string ( $data [33] ) . "',
						harga_satuan = '" . $mysqli->real_escape_string ( $data [34] ) . "',
						tanggal_dipesan = '" . $mysqli->real_escape_string ( $data [35] ) . "',
						ada_produk_terkait = '" . $mysqli->real_escape_string ( $data [36] ) . "',
						tanggal_edit_nomor_resi_pengiriman = '" . $mysqli->real_escape_string ( $data [37] ) . "',
						alamat_pengiriman = '" . $mysqli->real_escape_string ( $data [38] ) . "',
						nilai_ekspektasi_kalkulasi = '" . $mysqli->real_escape_string ( $data [39] ) . "'
						WHERE id='" . $check_data_result ['id'] . "'
						";
						if ($mysqli->query ( $update_query )) { // update Database
							$update_success ++;
						} else {
							$update_fail ++;
						}
					} else {
						$update_skip ++;
					}
				} else { // if order data not found
					$insert_query = "INSERT INTO order_elevenia SET 
					store_id = '" . $mysqli->real_escape_string ( $store_id ) . "',
					tgl = '" . $mysqli->real_escape_string ( $tgl ) . "',
					bln = '" . $mysqli->real_escape_string ( $bln ) . "',
					thn = '" . $mysqli->real_escape_string ( $thn ) . "',
					nomor = '" . $mysqli->real_escape_string ( $data [0] ) . "',
					status_pesanan = '" . $mysqli->real_escape_string ( $data [1] ) . "',
					nomor_pemesanan = '" . $mysqli->real_escape_string ( $data [2] ) . "',
					urutan_pemesanan = '" . $mysqli->real_escape_string ( $data [3] ) . "',
					tanggal_transaksi = '" . $mysqli->real_escape_string ( $data [4] ) . "',
					nomor_pengiriman = '" . $mysqli->real_escape_string ( $data [5] ) . "',
					nama_produk = '" . $mysqli->real_escape_string ( $data [6] ) . "',
					opsi = '" . $mysqli->real_escape_string ( $data [7] ) . "',
					kuantitas = '" . $mysqli->real_escape_string ( $data [8] ) . "',
					jumlah_pemesanan = '" . $mysqli->real_escape_string ( $data [9] ) . "',
					jumlah_pembayaran_kurangi_diskon = '" . $mysqli->real_escape_string ( $data [10] ) . "',
					penerima = '" . $mysqli->real_escape_string ( $data [11] ) . "',
					metode_pengiriman = '" . $mysqli->real_escape_string ( $data [12] ) . "',
					perusahaan_pengiriman = '" . $mysqli->real_escape_string ( $data [13] ) . "',
					kode_perusahaan_jasa_pengiriman = '" . $mysqli->real_escape_string ( $data [14] ) . "',
					booking_code_pre_awb = '" . $mysqli->real_escape_string ( $data [15] ) . "',
					nomor_resi_pengiriman = '" . $mysqli->real_escape_string ( $data [16] ) . "',
					tanggal_konfirmasi_pemesanan = '" . $mysqli->real_escape_string ( $data [17] ) . "',
					tanggal_proses_pengiriman = '" . $mysqli->real_escape_string ( $data [18] ) . "',
					keterlambatan_pengiriman = '" . $mysqli->real_escape_string ( $data [19] ) . "',
					tanggal_pengiriman_yang_diharapkan = '" . $mysqli->real_escape_string ( $data [20] ) . "',
					status_bundel = '" . $mysqli->real_escape_string ( $data [21] ) . "',
					ongkos_kirim = '" . $mysqli->real_escape_string ( $data [22] ) . "',
					voucher_ongkos_kirim = '" . $mysqli->real_escape_string ( $data [23] ) . "',
					biaya_asuransi_pengiriman = '" . $mysqli->real_escape_string ( $data [24] ) . "',
					ongkos_kirim_ditanggung_pelanggan = '" . $mysqli->real_escape_string ( $data [25] ) . "',
					nomor_ponsel = '" . $mysqli->real_escape_string ( $data [26] ) . "',
					nomor_telepon = '" . $mysqli->real_escape_string ( $data [27] ) . "',
					alamat = '" . $mysqli->real_escape_string ( $data [28] ) . "',
					keterangan_tambahan_pengiriman = '" . $mysqli->real_escape_string ( $data [29] ) . "',
					pembeli = '" . $mysqli->real_escape_string ( $data [30] ) . "',
					id_pembeli = '" . $mysqli->real_escape_string ( $data [31] ) . "',
					nomor_produk = '" . $mysqli->real_escape_string ( $data [32] ) . "',
					kode_produk_penjual = '" . $mysqli->real_escape_string ( $data [33] ) . "',
					harga_satuan = '" . $mysqli->real_escape_string ( $data [34] ) . "',
					tanggal_dipesan = '" . $mysqli->real_escape_string ( $data [35] ) . "',
					ada_produk_terkait = '" . $mysqli->real_escape_string ( $data [36] ) . "',
					tanggal_edit_nomor_resi_pengiriman = '" . $mysqli->real_escape_string ( $data [37] ) . "',
					alamat_pengiriman = '" . $mysqli->real_escape_string ( $data [38] ) . "',
					nilai_ekspektasi_kalkulasi = '" . $mysqli->real_escape_string ( $data [39] ) . "'
					";
					if ($mysqli->query ( $insert_query )) { // insert into Database
						$insert_success ++;
					} else {
						$insert_fail ++;
					}
				}
				$out = $out . '<h3>' . $row . '</h3>';
				for($c = 0; $c < $num; $c ++) {
					$out = $out . $c . " - " . $data [$c] . "<br />\n";
				}
			}
		}
		$row ++;
	}
	echo $error_id . '-' . $update_success . '-' . $update_fail . '-' . $update_skip . '-' . $insert_success . '-' . $insert_fail . '-' . $store_id;
	// print_debug($out);
}
function read_matahari($handle, $store_id) {
	global $mysqli;
	$row = 1;
	$update_success = 0;
	$update_fail = 0;
	$update_skip = 0;
	$insert_success = 0;
	$insert_fail = 0;
	$error_id = 0; // 0=No Error, 1=DB Error, 2=Not Match
	$error = false;
	$out = "";
	while ( ($data = fgetcsv ( $handle, 0, ";" )) !== FALSE ) {
		$num = count ( $data );
		if ($row > 2) {
			$ambil_tgl = explode ( " ", $mysqli->real_escape_string ( $data [1] ) );
			$pisah_tgl = explode ( "-", $ambil_tgl [0] );
			$tgl = ltrim ( $pisah_tgl [2], '0' ); // Get Tanggal
			$bln = ltrim ( $pisah_tgl [1], '0' ); // Get Bulan
			$thn = $pisah_tgl [0]; // Get Tahun
			$check_data_query = $mysqli->query ( "SELECT id, store_id, status FROM order_matahari WHERE order_number='" . $data [0] . "' AND mm_sku='" . $data [13] . "'" );
			$check_data_rows = $check_data_query->num_rows;
			$check_data_result = $check_data_query->fetch_assoc ();
			if ($check_data_rows > 0) { // check if order data is exist
				if ($check_data_result ['status'] != $data [12] || $check_data_result ['store_id'] != $store_id) { // check if order status changed
					$update_query = "UPDATE order_matahari SET 
					store_id = '" . $mysqli->real_escape_string ( $store_id ) . "',
					tgl = '" . $mysqli->real_escape_string ( $tgl ) . "',
					bln = '" . $mysqli->real_escape_string ( $bln ) . "',
					thn = '" . $mysqli->real_escape_string ( $thn ) . "',
					order_number = '" . $mysqli->real_escape_string ( $data [0] ) . "',
					order_date = '" . $mysqli->real_escape_string ( $data [1] ) . "',
					total_price_rp = '" . $mysqli->real_escape_string ( $data [2] ) . "',
					customer = '" . $mysqli->real_escape_string ( $data [3] ) . "',
					shipped_to = '" . $mysqli->real_escape_string ( $data [4] ) . "',
					phone_number = '" . $mysqli->real_escape_string ( $data [5] ) . "',
					address = '" . $mysqli->real_escape_string ( $data [6] ) . "',
					district = '" . $mysqli->real_escape_string ( $data [7] ) . "',
					city = '" . $mysqli->real_escape_string ( $data [8] ) . "',
					province = '" . $mysqli->real_escape_string ( $data [9] ) . "',
					zip_code = '" . $mysqli->real_escape_string ( $data [10] ) . "',
					total_qty = '" . $mysqli->real_escape_string ( $data [11] ) . "',
					status = '" . $mysqli->real_escape_string ( $data [12] ) . "',
					mm_sku = '" . $mysqli->real_escape_string ( $data [13] ) . "',
					seller_sku = '" . $mysqli->real_escape_string ( $data [14] ) . "',
					product_name = '" . $mysqli->real_escape_string ( $data [15] ) . "',
					price_rp = '" . $mysqli->real_escape_string ( $data [16] ) . "',
					qty = '" . $mysqli->real_escape_string ( $data [17] ) . "'
					WHERE id='" . $check_data_result ['id'] . "'
					";
					if ($mysqli->query ( $update_query )) { // update Database
						$update_success ++;
					} else {
						$update_fail ++;
					}
					$out = $out . $update_query . '<br />';
				} else {
					$update_skip ++;
				}
			} else { // if order data not found
				$insert_query = "INSERT INTO order_matahari SET 
				store_id = '" . $mysqli->real_escape_string ( $store_id ) . "',
				tgl = '" . $mysqli->real_escape_string ( $tgl ) . "',
				bln = '" . $mysqli->real_escape_string ( $bln ) . "',
				thn = '" . $mysqli->real_escape_string ( $thn ) . "',
				order_number = '" . $mysqli->real_escape_string ( $data [0] ) . "',
				order_date = '" . $mysqli->real_escape_string ( $data [1] ) . "',
				total_price_rp = '" . $mysqli->real_escape_string ( $data [2] ) . "',
				customer = '" . $mysqli->real_escape_string ( $data [3] ) . "',
				shipped_to = '" . $mysqli->real_escape_string ( $data [4] ) . "',
				phone_number = '" . $mysqli->real_escape_string ( $data [5] ) . "',
				address = '" . $mysqli->real_escape_string ( $data [6] ) . "',
				district = '" . $mysqli->real_escape_string ( $data [7] ) . "',
				city = '" . $mysqli->real_escape_string ( $data [8] ) . "',
				province = '" . $mysqli->real_escape_string ( $data [9] ) . "',
				zip_code = '" . $mysqli->real_escape_string ( $data [10] ) . "',
				total_qty = '" . $mysqli->real_escape_string ( $data [11] ) . "',
				status = '" . $mysqli->real_escape_string ( $data [12] ) . "',
				mm_sku = '" . $mysqli->real_escape_string ( $data [13] ) . "',
				seller_sku = '" . $mysqli->real_escape_string ( $data [14] ) . "',
				product_name = '" . $mysqli->real_escape_string ( $data [15] ) . "',
				price_rp = '" . $mysqli->real_escape_string ( $data [16] ) . "',
				qty = '" . $mysqli->real_escape_string ( $data [17] ) . "'
				";
				if ($mysqli->query ( $insert_query )) { // insert into Database
					$insert_success ++;
				} else {
					$insert_fail ++;
				}
				$out = $out . $insert_query . '<br />';
			}
		}
		$row ++;
	}
	echo $error_id . '-' . $update_success . '-' . $update_fail . '-' . $update_skip . '-' . $insert_success . '-' . $insert_fail . '-' . $store_id;
}
function read_blibli($handle, $store_id) {
	global $mysqli;
	$row = 1;
	$update_success = 0;
	$update_fail = 0;
	$update_skip = 0;
	$insert_success = 0;
	$insert_fail = 0;
	$error_id = 0; // 0=No Error, 1=DB Error, 2=Not Match
	$error = false;
	$out = "";
	while ( ($data = fgetcsv ( $handle, 0, "," )) !== FALSE ) {
		$num = count ( $data );
		if ($row != 1) {
			$ambil_tgl = explode ( " ", $mysqli->real_escape_string ( $data [3] ) );
			$pisah_tgl = explode ( "/", $ambil_tgl [0] );
			$tgl = ltrim ( $pisah_tgl [0], '0' ); // Get Tanggal
			$bln = ltrim ( $pisah_tgl [1], '0' ); // Get Bulan
			$thn = $pisah_tgl [2]; // Get Tahun
			$check_data_query = $mysqli->query ( "SELECT id, store_id, order_status FROM order_blibli WHERE no_order='" . $data [0] . "' AND no_order_item='" . $data [1] . "'" );
			$check_data_rows = $check_data_query->num_rows;
			$check_data_result = $check_data_query->fetch_assoc ();
			if ($check_data_rows > 0) { // check if order data is exist
				if ($check_data_result ['order_status'] != $data [14] || $check_data_result ['store_id'] != $store_id) { // check if order status changed
					$update_query = "UPDATE order_blibli SET 
					store_id = '" . $mysqli->real_escape_string ( $store_id ) . "',
					tgl = '" . $mysqli->real_escape_string ( $tgl ) . "',
					bln = '" . $mysqli->real_escape_string ( $bln ) . "',
					thn = '" . $mysqli->real_escape_string ( $thn ) . "',
					no_order = '" . $mysqli->real_escape_string ( $data [0] ) . "',
					no_order_item = '" . $mysqli->real_escape_string ( $data [1] ) . "',
					no_awb = '" . $mysqli->real_escape_string ( $data [2] ) . "',
					tanggal_order = '" . $mysqli->real_escape_string ( $data [3] ) . "',
					nama_pemesan = '" . $mysqli->real_escape_string ( $data [4] ) . "',
					kode_sku = '" . $mysqli->real_escape_string ( $data [5] ) . "',
					blibli_sku = '" . $mysqli->real_escape_string ( $data [6] ) . "',
					merchant_sku = '" . $mysqli->real_escape_string ( $data [7] ) . "',
					nama_produk = '" . $mysqli->real_escape_string ( $data [8] ) . "',
					total_barang = '" . $mysqli->real_escape_string ( $data [9] ) . "',
					harga_produk = '" . $mysqli->real_escape_string ( $data [10] ) . "',
					servis_logistik = '" . $mysqli->real_escape_string ( $data [11] ) . "',
					kode_merchant = '" . $mysqli->real_escape_string ( $data [12] ) . "',
					nama_store = '" . $mysqli->real_escape_string ( $data [13] ) . "',
					order_status = '" . $mysqli->real_escape_string ( $data [14] ) . "'
					WHERE id='" . $check_data_result ['id'] . "'
					";
					if ($mysqli->query ( $update_query )) { // update Database
						$update_success ++;
					} else {
						$update_fail ++;
					}
				} else {
					$update_skip ++;
				}
			} else { // if order data not found
				$insert_query = "INSERT INTO order_blibli SET 
				store_id = '" . $mysqli->real_escape_string ( $store_id ) . "',
				tgl = '" . $mysqli->real_escape_string ( $tgl ) . "',
				bln = '" . $mysqli->real_escape_string ( $bln ) . "',
				thn = '" . $mysqli->real_escape_string ( $thn ) . "',
				no_order = '" . $mysqli->real_escape_string ( $data [0] ) . "',
				no_order_item = '" . $mysqli->real_escape_string ( $data [1] ) . "',
				no_awb = '" . $mysqli->real_escape_string ( $data [2] ) . "',
				tanggal_order = '" . $mysqli->real_escape_string ( $data [3] ) . "',
				nama_pemesan = '" . $mysqli->real_escape_string ( $data [4] ) . "',
				kode_sku = '" . $mysqli->real_escape_string ( $data [5] ) . "',
				blibli_sku = '" . $mysqli->real_escape_string ( $data [6] ) . "',
				merchant_sku = '" . $mysqli->real_escape_string ( $data [7] ) . "',
				nama_produk = '" . $mysqli->real_escape_string ( $data [8] ) . "',
				total_barang = '" . $mysqli->real_escape_string ( $data [9] ) . "',
				harga_produk = '" . $mysqli->real_escape_string ( $data [10] ) . "',
				servis_logistik = '" . $mysqli->real_escape_string ( $data [11] ) . "',
				kode_merchant = '" . $mysqli->real_escape_string ( $data [12] ) . "',
				nama_store = '" . $mysqli->real_escape_string ( $data [13] ) . "',
				order_status = '" . $mysqli->real_escape_string ( $data [14] ) . "'
				";
				if ($mysqli->query ( $insert_query )) { // insert into Database
					$insert_success ++;
				} else {
					$insert_fail ++;
				}
				$out = $out . $insert_query . '<br />';
			}
			$out = $out . '<h3>' . $row . '</h3>';
			for($c = 0; $c < $num; $c ++) {
				$out = $out . $c . " - " . $data [$c] . "<br />\n";
			}
		}
		$row ++;
	}
	echo $error_id . '-' . $update_success . '-' . $update_fail . '-' . $update_skip . '-' . $insert_success . '-' . $insert_fail . '-' . $store_id;
	// print_debug($out);
}
function read_tokopedia($handle, $store_id) {
	global $mysqli;
	global $temp_name;
	$row = 1;
	$update_success = 0;
	$update_fail = 0;
	$update_skip = 0;
	$insert_success = 0;
	$insert_fail = 0;
	$error_id = 0; // 0=No Error, 1=DB Error, 2=Not Match
	$error = false;
	$out = "";
	while ( ($data = fgetcsv ( $handle, 0, ";" )) !== FALSE ) {
		$num = count ( $data );
		$out = $out . '<h3>' . $row . '</h3>';
		for($c = 0; $c < $num; $c ++) {
			$out = $out . $c . " - " . $data [$c] . "<br />\n";
		}
		$row ++;
	}
	echo $error_id . '-' . $update_success . '-' . $update_fail . '-' . $update_skip . '-' . $insert_success . '-' . $insert_fail . '-' . $store_id;
	print_debug ( file_get_contents ( $temp_name, NULL, NULL, 0, 50 ) );
}
function print_debug($string) {
	$myfile = fopen ( "debug.html", "w" ) or die ( "Unable to open file!" );
	fwrite ( $myfile, $string );
	fclose ( $myfile );
}
function expired_date ($timestamp) {
	$current_time = date("Y-m-d H:i:s");
	$start_date = date($timestamp);
	$expires = strtotime('+6 hours', strtotime($timestamp));
	//$expires = date($expires);
	$date_diff=($expires-strtotime($current_time)) / 3600;
	echo "Mulai: ".$timestamp."<br>";
	echo "Kadaluarsa: ".date('Y-m-d H:i:s', $expires)."<br>";
	echo "Saat Ini: ".$current_time."<br>";
	echo round($date_diff, 0)." Jam Tersisa";
}
//echo expired_date ("2016-11-02 09:40:23");
?> 