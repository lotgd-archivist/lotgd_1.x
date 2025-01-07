<?php

if(!extension_loaded('zlib')) {
	function zlib_get_coding_type() {
		return true;
	}

	function gzcompress($data) {
		return $data;
	}

	function gzuncompress($data) {
		return $data;
	}
}
