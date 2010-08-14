<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of common
 *
 * @author Minh Tuan
 */
Prado::using('Application.common.string');

class Common {
	//put your code here
	function plg_images_resize($text, $i_width=100, $i_height=100, $quality, & $widthm, & $heightm) {

		//$path = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'protected')).'thumbnails';
		$path = Prado::getApplication()->BasePath.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.'article'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'thumbnails'.DIRECTORY_SEPARATOR;
		$cache_path = $path;
		$cache_path_http = $path;//"thumbnails"; //$baseurl.

		if (preg_match_all("|src[\s\v]*=[\s\v]*['\"](.*)['\"]|Ui",$text,$matches,PREG_PATTERN_ORDER) == 0) {
			if (preg_match_all("|src[\s\v]*=[\s\v]*([\S]+)[\s\v]|Ui",$text,$matches,PREG_PATTERN_ORDER) == 0) return $text;
		}

		$src = $matches[1][0];

		$width = $widthm = $i_width;
		$height = $heightm = $i_height;

		$hash = md5(uniqid());

		$filename = $hash.".jpg";
		$full_path_filename = $cache_path."/".$filename;

		if (@is_file($full_path_filename)) {//Anh tai local
			touch($full_path_filename);
			$url = $cache_path_http."/".$filename;
			list($widthm, $heightm) = getimagesize($url);
		}
		else {//Anh cbua xu ly
			$image = Common::getimagedata($src);
			if ($image == false) $image = Common::getimagedata("thumbnails/".$src);
			if ($image == false) return $text;
			$width_orig = imagesx($image);
			$height_orig = imagesy($image);
			if($width_orig <= $i_width && $height_orig <= $i_height) {$widthm = $width_orig; $heightm = $height_orig; return $filename;}
			$ratio_orig = $width_orig/$height_orig;
			if ($width/$height > $ratio_orig) {
				$width = $widthm = floor($height*$ratio_orig);//round
			} else {
				$height = $heightm = floor($width/$ratio_orig);//round
			}
			$result = @imagecreatetruecolor($width, $height);
			if ($result == false) return '';
			$sample = @imagecopyresampled($result, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
			if ($sample == false) return '';
			$save = @imagejpeg($result, $full_path_filename, $quality);

			if ($save == false) return '';
			@imagedestroy($image);
			@imagedestroy($result);
		}

		return $filename;
	}

	function getimagedata($file) {
		$data = @file_get_contents($file);
		if ($data == false) return false;
		return @imagecreatefromstring($data);
	}

	function chars($text_, $charnum, $more='') {
		$text_ = String::trim($text_);
		if(($charnum >0) && (String::strlen($text_) > $charnum)) return String::substr($text_,0,$charnum).(($more=='')?"&hellip;":$more);
		else return $text_;
	}

	/*function roundPrecision($value){
		$valueArr = explode('.',$value);
		$precision = strlen($valueArr[0]);
		$value = (float)(substr($value,0,strlen($value)-1).(substr($value,strlen($value)-1)>=5 ? '5' : '0'));
		var_dump($value);
		$round = $precision - floor(log10(abs($value))); 
		return round($value, $round); 
	}*/
	
	public function roundTo($number, $to=0.05){ 
		$number = number_format($number, 2);
		$number = (float)(substr($number,0,strlen($number)-1).(substr($number,strlen($number)-1)>=5 ? '5' : '0'));
		return round($number/$to, 0)* $to; 
	}
	
	function showOrdinal($num) {
		// first convert to string if needed
		$the_num = (string) $num;
		// now we grab the last digit of the number
		$last_digit = substr($the_num, -1, 1);
		// if the string is more than 2 chars long, we get
		// the second to last character to evaluate
		if (strlen($the_num)>1) {
			$next_to_last = substr($the_num, -2, 1);
		} else {
			$next_to_last = "";
		}
		// now iterate through possibilities in a switch
		switch($last_digit) {
			case "1":
				// testing the second from last digit here
				switch($next_to_last) {
					case "1":
						$the_num.="th";
						break;
					default:
						$the_num.="st";
				}
				break;
			case "2":
				// testing the second from last digit here
				switch($next_to_last) {
					case "1":
						$the_num.="th";
						break;
					default:
						$the_num.="nd";
				}
				break;
			// if last digit is a 3
			case "3":
				// testing the second from last digit here
				switch($next_to_last) {
					case "1":
						$the_num.="th";
						break;
					default:
						$the_num.="rd";
				}
				break;
			// for all the other numbers we use "th"
			default:
				$the_num.="th";
		}
	
		// finally, return our string with it's new suffix
		return $the_num;
	}
	
	/** 
	 * Cut string to n symbols and add delim but do not break words. 
	 * 
	 * Example: 
	 * <code> 
	 *  $string = 'this sentence is way too long'; 
	 *  echo neat_trim($string, 16); 
	 * </code> 
	 * 
	 * Output: 'this sentence is...' 
	 * 
	 * @access public 
	 * @param string string we are operating with 
	 * @param integer character count to cut to 
	 * @param string|NULL delimiter. Default: '...' 
	 * @return string processed string 
	 **/ 
	function neatTrim($str, $n, $delim='...') { 
	   $len = strlen($str); 
	   if ($len > $n) { 
		   preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches); 
		   return rtrim($matches[1]) . $delim; 
	   } 
	   else { 
		   return $str; 
	   } 
	} 

}
?>
