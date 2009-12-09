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
Prado::using('Application.common.String');

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
}
?>
