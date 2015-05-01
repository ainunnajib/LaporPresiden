<?php
if(extension_loaded('zlib')){
  ob_start('ob_gzhandler');
}
header ("content-type: text/css; charset: UTF-8");
header ("cache-control: must-revalidate");
$offset = 60 * 60;
$expire = "expires: " . gmdate ("D, d M Y H:i:s", time() + $offset) . " GMT";
header ($expire);
ob_start("compress");

// *** remove comments
function compress($buffer) {
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    return $buffer;
}

 // list CSS files or JS to be included in the Gzip
  include('qa-styles.css');
 
if(extension_loaded('zlib')){
  ob_end_flush();
}
	
/*
	Omit PHP closing tag to help avoid accidental output
*/