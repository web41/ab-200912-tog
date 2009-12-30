<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of string
 *
 * @author Minh Tuan
 */

/**
 * PHP mbstring and iconv local configuration
 */
// check if mbstring extension is loaded and attempt to load it if not present except for windows
if (extension_loaded('mbstring') || ((!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && dl('mbstring.so')))) {
	//Make sure to surpress the output in case ini_set is disabled
	@ini_set('mbstring.internal_encoding', 'UTF-8');
	@ini_set('mbstring.http_input', 'UTF-8');
	@ini_set('mbstring.http_output', 'UTF-8');
}

// same for iconv
if (function_exists('iconv') || ((!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && dl('iconv.so')))) {
	// these are settings that can be set inside code
	iconv_set_encoding("internal_encoding", "UTF-8");
	iconv_set_encoding("input_encoding", "UTF-8");
	iconv_set_encoding("output_encoding", "UTF-8");
}

/**
 * Include the utf8 package
 */

Prado::using('Application.common.phputf8.utf8');
Prado::using('Application.common.phputf8.trim');
class String {
	//put your code here
	function substr($str, $offset, $length = FALSE) {
		if ( $length === FALSE ) {
			return utf8_substr($str, $offset);
		} else {
			return utf8_substr($str, $offset, $length);
		}
	}

	function strlen($str) {
		return utf8_strlen($str);
	}

	function trim( $str, $charlist = FALSE ) {
		if ( $charlist === FALSE ) {
			return utf8_trim( $str );
		} else {
			return utf8_trim( $str, $charlist );
		}
	}

	function removeAccents($text)
	{//1
		$a = array("à","á","ả","ã","ạ","â","ấ","ầ","ẩ","ẫ","ậ","ă","ắ","ằ","ẳ","ẵ","ặ","ä","Ã","Ã¡","Ã¢","Ã¤");
		$e = array("è","é","ẻ","ẽ","ẹ","ê","ế","ề","ể","ễ","ệ","ë","Ã¨","Ã©","Ãª","Ã«");
		$i = array("ì","í","ỉ","ĩ","ị","î","ï","Ã¬","Ã-","Ã®","Ã¯");
		$o = array("ò","ó","ỏ","õ","ọ","ô","ố","ồ","ổ","ỗ","ộ","ơ","ớ","ờ","ở","ỡ","ợ","ö","Ã²","Ã³","Ã´","Ã¶");
		$u = array("ù","ú","ủ","ũ","ụ","û","ü","ừ","ứ","ử","ữ","ự","Ã¹","Ãº","Ã»","Ã¼");
		$y = array("ý","ỳ","ỷ","ỹ","ỵ","Ã½");
		$c = array("ç","Ã§");
		$n = array("ñ","Ã±");
		$d = array("đ","Ď","Đ");
		$chars = array(" ",",",".","?","!","@","#","$","%","*","/","'",'"');
		$changeset = array($a,$e,$i,$o,$u,$y,$c,$n,$d,$chars);
		$changeto = array("a","e","i","o","u","y","c","n","d","-");
	
		$textarray=self::mb_str_split(mb_strtolower($text));//MODIFY TO mb_str_split_UTF8() IF NECESSARY.
		$i=0;$j=0;
		foreach($textarray as $letter)
		{//2
			foreach($changeset as $accentgroup)
			{//3
				if(in_array($letter,$accentgroup))
				{//4
					$textarray[$i] = $changeto[$j];
					continue;
				}//4
				$j++;
			}//3
			$i++;$j=0;
		}//2
		return implode("",$textarray);
	}//1 END removeAccents()
	
	function mb_str_split($str, $length = 1) 
	{
		if ($length < 1) return FALSE;
		$result = array();
		for ($i = 0; $i < mb_strlen($str); $i += $length) 
			$result[] = mb_substr($str, $i, $length);
  	return $result;
	}
}
?>
