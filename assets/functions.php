<?php
function slug ($str) {
	$to = array('Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-');
	$str = preg_replace(array("/\s{2,}/", "/[\t\n]/"), ' ', $str);
	$str = strtr($str, $to);
	return strtolower($str);
}

function get_ip () {
	$ipaddress = '';
	if (getenv('HTTP_CLIENT_IP'))
		$ipaddress = getenv('HTTP_CLIENT_IP');
	else if (getenv('HTTP_X_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	else if (getenv('HTTP_X_FORWARDED'))
		$ipaddress = getenv('HTTP_X_FORWARDED');
	else if (getenv('HTTP_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_FORWARDED_FOR');
	else if (getenv('HTTP_FORWARDED'))
		$ipaddress = getenv('HTTP_FORWARDED');
	else if (getenv('REMOTE_ADDR'))
		$ipaddress = getenv('REMOTE_ADDR');
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}

function get_mili_time () {
	return round(microtime(true) * 1000);
}

function get_iso_date () {
	return date('Y-m-d\TH:i:s');
}

function get_uniq ($str) {
	return base64_encode($str.'-'.rand().'-'.microtime().'-'.get_ip());
}

function pre_print_r ($data) {
	echo '<pre>'.print_r($data, true).'</pre>';
}

function print_log ($data) {
	error_log(print_r($data, true));
}

function validate_rut ($rut) {
	$arr = array();
	$rut = trim($rut);
	
	if (strpos($rut, '-') === false) {
		$arr[0] = substr($rut, 0, -1);
		$arr[1] = substr($rut, -1);
	}
	else {
		$arr = explode('-', $rut);
	}

	$arr[0] = trim($arr[0]);
	$arr[1] = trim($arr[1]);

	$rut = str_replace('.', '', $arr[0]);
	$factor = 2;
	$suma = 0;
	$i = strlen($rut) - 1;

	if (!is_numeric($rut)) return false;

	for ($i; $i >= 0; $i--) {
		$factor = $factor > 7 ? 2 : $factor;
		$suma += $rut{$i}*$factor++;
	}

	$resto = $suma % 11;
	$dv = 11 - $resto;

	if ($dv == 11) $dv = 0;
	else if ($dv == 10) $dv = "k";

	return strlen($rut) > 6 && strlen($rut) < 9 && strtolower($arr[1]) == $dv;
}

function get_clean_data ($data) {

	$out = array();

	foreach ($data as $field => $value) {
		${$field} = $value;

		if (isset($telephone)) {
			$telephone = preg_replace("/[^\d]/", '', $telephone);
			$telephone = '+'.$telephone;
			$telephone = strlen($telephone) === 12 ? $telephone : null;
		}
		
		if (isset($name)) {
			$name = ucfirst(strtolower($name));
			$name = strlen($name) > 1 ? $name : null;
		}
		
		if (isset($surname)) {
			$surname = ucfirst(strtolower($surname));
			$surname = strlen($surname) > 1 ? $surname : null;
		}
		
		if (isset($rut)) {
			$rut = preg_replace("/[^\-\dkK]/", '', $rut);
			
			if (!strpos($rut, '-')) {
				$rut = explode('', ''.$rut);
				$dig = array_pop($rut);
				$rut = implode('', $rut).'-'.$dig;
			}
		
			$rut = validate_rut($rut) ? $rut : null;
		}
		
		if (isset($email)) {
			$email = strtolower($email);
			$email = preg_match("/.+@.+\..+/", $email) ? $email : null;
		}

		$out[$field] = ${$field};
	}

	return $out;

}