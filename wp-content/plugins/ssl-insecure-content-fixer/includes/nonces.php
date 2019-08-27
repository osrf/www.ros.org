<?php

/**
* generate nonce value
* @return string
*/
function ssl_insecure_content_fix_nonce_value() {
	// some system data, difficult to guess unless server environment is already known
	$tick = ceil(time() / (60 * 60 * 6));
	$data = sprintf("%s\n%s\n%s\n%s\n%s", php_uname(), php_ini_loaded_file(), php_ini_scanned_files(), implode("\n", get_loaded_extensions()), $tick);

	return hash_hmac('md5', $data, 'ssl_insecure_content_fix');
}
