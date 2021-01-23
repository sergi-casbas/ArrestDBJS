<?php
// Google Autenticator compatibility
function base32_decode($data){
	$result = '';
	$binary_string = '';
	$binary_string_chunks = '';
	$data = str_replace(" ","",$data);; # remove user-friendly spaces.
	$data = strtoupper($data); # base32 only works with uppercase.
	
	$base32_mapping = array(
								'A' => '0',  'B' => '1',  'C' => '2',  'D' => '3',  'E' => '4',  'F' => '5',  'G' => '6',  'H' => '7',
								'I' => '8',  'J' => '9',  'K' => '10', 'L' => '11', 'M' => '12', 'N' => '13', 'O' => '14', 'P' => '15',
								'Q' => '16', 'R' => '17', 'S' => '18', 'T' => '19', 'U' => '20', 'V' => '21', 'W' => '22', 'X' => '23',
								'Y' => '24', 'Z' => '25', '2' => '26', '3' => '27', '4' => '28', '5' => '29', '6' => '30', '7' => '31'
							);
	$base32_charset = array(
								'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
								'I', 'J', 'K', 'L',	'M', 'N', 'O', 'P',
								'Q', 'R', 'S', 'T',	'U', 'V', 'W', 'X',
								'Y', 'Z', '2', '3',	'4', '5', '6', '7',
								'='
							);

	$data = str_replace('=', '', $data);
	$data = str_split($data);

	for($counter = 0; $counter < count($data); $counter = $counter + 8)
	{
		$binary_string = '';
		for($counter_binary_string = 0; $counter_binary_string < 8; $counter_binary_string = $counter_binary_string + 1)
		{
			$binary_string = $binary_string . str_pad(base_convert($base32_mapping[$data[$counter + $counter_binary_string]], 10, 2), 5, '0', STR_PAD_LEFT);
		}
		$binary_string_chunks = str_split($binary_string, 8);
		for($counter_binary_string_chunks = 0; $counter_binary_string_chunks < count($binary_string_chunks); $counter_binary_string_chunks = $counter_binary_string_chunks + 1)
		{
			if (base_convert($binary_string_chunks[$counter_binary_string_chunks], 2, 10) != 0 )
			{
				$result = $result . chr(base_convert($binary_string_chunks[$counter_binary_string_chunks], 2, 10));
			}
			else
			{
				$result = $result . '';
			}
		}
	}

	return $result;
}

//generate T value (for TOTP)
function generate_t_value($current_unix_time, $t_0, $time_step){
	$result = '';
	
	if (isset($current_unix_time) && isset($t_0) && isset($time_step))
	{
		$t = floor(($current_unix_time - $t_0) / $time_step);
		$result = $t;
	}
	else
	{
		//The current Unix time, initial time or time step (time slice, time window) was a NULL string!
	}

	return $result;
}

//generate binary data (counter for HOTP, time for TOTP)
function generate_hash_hmac_data($data){
	$result = '';
	
	if (isset($data))
	{
		$data_temp = '';
		$data_temp = str_pad(dechex($data), 16, '0', STR_PAD_LEFT);

		for ($counter = 0; $counter < (strlen($data_temp) / 2); $counter = $counter + 1)
		{
			$result = $result . chr(hexdec(substr($data_temp, ($counter * 2), 2)));
		}
	}
	else
	{
		//The counter of time data was a NULL string!
	}
	
	return $result;
}

//generate hash_hmac with SHA-1, SHA-256 and SHA-512
function generate_hash_hmac($hash_hmac_algo, $hash_hmac_data, $hash_hmac_key){
	$result = '';
	
	if (($hash_hmac_algo == 'sha1') || ($hash_hmac_algo == 'sha256') || ($hash_hmac_algo == 'sha512'))
	{
		if (($hash_hmac_data != '') && ($hash_hmac_key != ''))
		{
			$result = hash_hmac($hash_hmac_algo, $hash_hmac_data, $hash_hmac_key, TRUE);
		}
		else
		{
			//The shared secret key of data-to-be-hashed was a NULL string!
		}
	}
	else
	{
		//Not supported hash algorithm!
	}
	
	return $result;
}

//generate truncated value hash
function generate_truncated_value($data, $hash_hmac_algo){
	$result = '';
	
	if ($data != '')
	{
		switch ($hash_hmac_algo)
		{
			case 'sha1'  : $counter_algo = 19; break;
			case 'sha256': $counter_algo = 31; break;
			case 'sha512': $counter_algo = 63; break;
			default      : $counter_algo = 19; break;
		}

		//processing the result of hash_hmac
		$truncate_offset_bits = '';
		$truncate_offset_bits = decbin(ord($data[$counter_algo]));

		//padding final byte with "0" values to 8 bits (string)
		while (strlen($truncate_offset_bits) < 8)
		{
			$truncate_offset_bits = '0' . $truncate_offset_bits;
		}

		//selecting the final 4 bits to compute offset
		$truncate_offset_bits_temp = '';
		for ($counter = 4; $counter < 8; $counter = $counter + 1)
		{
			$truncate_offset_bits_temp = $truncate_offset_bits_temp . $truncate_offset_bits[$counter];
		}
		$truncate_offset_bits = bindec($truncate_offset_bits_temp);

		//selecting bytes of the hash from the offset 
		$counter = 0;
		$truncate_offset_data_temp = '';
		for ($counter = $truncate_offset_bits; $counter < ($truncate_offset_bits + 4); $counter = $counter + 1)
		{
			$truncate_offset_data_temp = $truncate_offset_data_temp . str_pad(dechex(ord($data[$counter])), 2, '0', STR_PAD_LEFT);
		}
		$truncate_offset_data = decbin(hexdec($truncate_offset_data_temp));

		//padding final byte with "0" values to 32 bits (string)
		while (strlen($truncate_offset_data) < 32)
		{
			$truncate_offset_data = '0' . $truncate_offset_data;
		}

		//selecting the final 31 bits
		$counter = 0;
		$truncate_offset_data_temp = '';
		while ($counter < 32)
		{
			if ($counter > 0)
			{
				$truncate_offset_data_temp = $truncate_offset_data_temp . $truncate_offset_data[$counter];
			}
			$counter = $counter + 1;
		}
		$result = bindec($truncate_offset_data_temp);
	}
	else
	{
		//The data-to-be-truncated was a NULL string!
	}

	return $result;
}

//generate HOTP or TOTP data
function generate_HOTP_TOTP_value($data, $digits){
	$result = '';
	
	if (is_int($digits) && ($data != ''))
	{
		if ($digits > 5)
		{
			$divided_by = pow(10, $digits);

			//compute modulus (HOTP or TOTP value) in the given length (digits)
			$result = $data % $divided_by;

			//padding one-time-password with "0" values to given number of digits (string)
			while (strlen($result) < $digits)
			{
				$result = '0' . $result;
			}
		}
		else
		{
			//The number of digits must be at least 6, value 7 and 8 must be also supported based on RFC requirements!
		}
	}
	else
	{
		//The exponent or the truncated data was a NULL string!
	}
	
	return $result;
 }

// generate google autenticator compatible totp
function generate_totp($privkey, $hash_hmac_algo, $digits, $time_step){
	$hash_hmac_data_chr = time();
	$hash_hmac_key = base32_decode($privkey);
	$current_unix_time = $hash_hmac_data_chr;
	$t_0 = 0; #0 + $_POST['google_totp_time_initial'];
	$time_step_window = 1; #0 + $_POST['google_totp_time_step_window'];
	$t = generate_t_value($current_unix_time, $t_0, $time_step);
	$t = str_pad(dechex($t), 16, '0', STR_PAD_LEFT);
	$hash_hmac_data_chr = hexdec($t);
	$hash_hmac_data = generate_hash_hmac_data($hash_hmac_data_chr);
	$hash_hmac = generate_hash_hmac($hash_hmac_algo, $hash_hmac_data, $hash_hmac_key);
	$truncated_data = generate_truncated_value($hash_hmac, $hash_hmac_algo);
	return generate_HOTP_TOTP_value($truncated_data, $digits);
}

function generate_google_totp($privkey){
	return generate_totp($privkey, 'sha1', 6, 30);
}


?>
