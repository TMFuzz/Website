<?php

// Section image: create a CAPTCHA image
if(isset($_GET['image'])){ 
     captcha_image();
    
// Section audio: create a .WAV file containing the contents of $_SESSION['captcha'] in voice form
}elseif(isset($_GET['audio'])){  
	if(empty($_SESSION['captcha']))exit;
	$length = strlen($_SESSION['captcha']);
	$files = array();
	for($i = 0; $i < $length; $i++){
		$alphanumeric = substr($_SESSION['captcha'], $i, 1);
	   	if(preg_match('/[A-Z]/', $alphanumeric)) $files[] = CAPTCHA_PATH.'capital.wav';
		if(preg_match('/[a-z]/', $alphanumeric)) $files[] = CAPTCHA_PATH.'lower.wav';
		$files[] = CAPTCHA_PATH.strtolower($alphanumeric).'.wav';
	}
	$ChunkID_ = array(0x52, 0x49, 0x46, 0x46);       //"RIFF" big endian
	$FileFormat_ = array(0x57, 0x41, 0x56, 0x45);    //"WAVE" big endian
	$Subchunk1ID_ = array(0x66, 0x6D, 0x74, 0x20);   //"fmt" big endian
	$AudioFormat_ = array(0x1, 0x0);                 //PCM = 1 little endian
	$Channels_ = array(0x1, 0x0);                    //Mono = 1 little endian(0x1, 0x0)... Stereo = 2 little endian(0x2, 0x0) 
	$SampleRate_ = array(0x70, 0x17, 0x0, 0x0);      //6000hz little endian, Hex 1770 (array(0x70, 0x17, 0x0, 0x0)) or 44100hz little endian, Hex AC44 (array(0x44, 0xAC, 0x0, 0x0))
	$BitsPerSample_ = array(0x10, 0x0);              //16 little endian
	$Subchunk2ID_ = array(0x64, 0x61, 0x74, 0x61);   //"data" big endian
	
	$Stitcher = new CStitcher();
	$file = new FILESTRUCT();
	$Stitcher->StitchFiles($file, $files);
	
	header('Content-type: audio/x-wav', true);
	header('Content-Disposition: attachment;filename=captcha.wav');
	
	foreach($file->ChunkID as $val) {
	    print chr($val);
	} 
	foreach($file->ChunkSize as $val) {
	    print chr($val);
	} 
	foreach($file->Format as $val) {
	    print chr($val);
	} 
	foreach($file->Subchunk1ID as $val) {
	    print chr($val);
	} 
	foreach($file->Subchunk1Size as $val) {
	    print chr($val);
	} 
	foreach($file->AudioFormat as $val) {
	    print chr($val);
	} 
	foreach($file->NumChannels as $val) {
	    print chr($val);
	} 
	foreach($file->SampleRate as $val) {
	    print chr($val);
	} 
	foreach($file->ByteRate as $val) {
	    print chr($val);
	} 
	foreach($file->BlockAlign as $val) {
	    print chr($val);
	} 
	foreach($file->BitsPerSample as $val) {
	    print chr($val);
	} 
	foreach($file->Subchunk2ID as $val) {
	    print chr($val);
	} 
	foreach($file->Subchunk2Size as $val) {
	    print chr($val);
	} 
	foreach($file->Data as $val) {
	    print chr($val);
	} 

	exit;

// Block direct download of .WAV and .ttf files
}elseif(isset($_GET['wav']) or isset($_GET['ttf'])){
	send_404();

	
// Stop direct opening of this file	
}elseif('captcha.php' == basename($_SERVER['PHP_SELF'])){
	send_404();
}

function send_404()
{
	header('HTTP/1.x 404 Not Found');
	print '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'."\n".
	'<html><head>'."\n".
	'<title>404 Not Found</title>'."\n".
	'</head><body>'."\n".
	'<h1>Not Found</h1>'."\n".
	'<p>The requested URL '.
	str_replace(strstr($_SERVER['REQUEST_URI'], '?'), '', $_SERVER['REQUEST_URI']).
	' was not found on this server.</p>'."\n".
	'</body></html>'."\n";
	exit;
}

function captcha_validate()
{
	if(trim($_POST['captcha']) == $_SESSION['captcha'])
	{
		$_SESSION['captcha'] = NULL;
		return true;
	}
	return false;
}

function captcha_image()
{
	function create_captcha_code($len = 4){
	   $e = base64_encode(pack("h*", sha1(mt_rand())));
	   $_SESSION['captcha'] = str_replace('l', '2', str_replace('O', '3', str_replace('0', '4', str_replace('1', '5', str_replace('o', '6', str_replace('I', '7', substr(strtr($e, "+/=", "xyz"), 0, $len)))))));//substr(strtr($e, "+/=", "xyz"), 0, $len);
	}
	
	function rand_string($len = 28){
	   $e = base64_encode(pack("h*", sha1(mt_rand())));
	   return substr(strtr($e, "+/=", "xyz"), 0, $len);
	}
	$dir = opendir(CAPTCHA_PATH);
		while (false !== ($file = readdir($dir))) {
		   if (eregi('ttf{1}$', $file)){
				$fonts[] = CAPTCHA_PATH.$file;		    
			}
		}
	closedir($dir);
	
	if(!empty($fonts) and is_array($fonts)){
		shuffle($fonts);
		$font = $fonts['0'];
		if(isset($fonts['1'])){
			$background_font = $fonts['1'];
		}else{
			$background_font = $fonts['0'];
		}
	}
	
	
	create_captcha_code();
	$colour1 = mt_rand(125, 175);
	$colour2 = mt_rand(50, 235);
	$colour3 = ((420 - ($colour1 + $colour2)) + mt_rand(-10, 10));
	$colours = array($colour1, $colour2, $colour3); 
	shuffle($colours);
	
	$text_colour1 = mt_rand(55, 75);
	$text_colour2 = mt_rand(20, 145);
	$text_colour3 = ((240 - ($text_colour1 + $text_colour2)) + mt_rand(-5, 5));
	$text_colours = array($text_colour1, $text_colour2, $text_colour3);
	shuffle($text_colours);
	
	if($text_colour1 > 60){ $text_shadow1 = $text_colour1 - 60; }else{ $text_shadow1 = 0;}
	if($text_colour2 > 60){ $text_shadow2 = $text_colour2 - 60; }else{ $text_shadow2 = 0;}
	if($text_colour3 > 60){ $text_shadow3 = $text_colour3 - 60; }else{ $text_shadow3 = 0;}
	
	$alpha_string = '';
	for($i = 0; $i < 25; $i++){
		$alpha_string .= rand_string().rand_string().rand_string()."\n";
	}
	foreach($colours as $colour){
		$hidden[] = $colour + (mt_rand(15, 255 - $colour) - 15);
	}
	
	header ('Content-type: image/jpeg');
	
	$image = @imagecreate(140, 90); // image dimensions
	$background_colour = imagecolorallocate($image, $colours['0'],$colours['1'],$colours['2']);
	$text_shadow = imagecolorallocate($image, $text_shadow1, $text_shadow2, $text_shadow3);
	$text_colour = imagecolorallocate($image, $text_colours['0'],$text_colours['1'],$text_colours['2']);
	$hidden = imagecolorallocate($image, $hidden['0'],$hidden['1'],$hidden['2']);
	if(!empty($font) and is_readable($font)){
		$angle = mt_rand(-20, 20);
		$xCoord = mt_rand(4, 36);
		$xCoord = $xCoord + round($angle/4);
		$yCoord = mt_rand(0, 22);
		imagettftext($image, mt_rand(10,15), mt_rand(60, 120), -70, 180, $hidden, $background_font, $alpha_string);
		imagettftext($image, 27, $angle, 2 + $xCoord, 43 + ceil($angle/1.5) + $yCoord, $text_shadow, $font, $_SESSION['captcha']);
		imagettftext($image, 27, $angle, $xCoord, 42 + ceil($angle/1.5) + $yCoord, $text_colour, $font, $_SESSION['captcha']);
	}else{
		imagestring($image, 5, 26, 19, $_SESSION['captcha'], $text_shadow);
		imagestring($image, 5, 25, 18, $_SESSION['captcha'], $text_colour);
	}
	imagejpeg($image, null, 100);
	imagedestroy($image);	
	exit;
}

// The classes and functions below manage splicing the various .WAV audio files. They 
// originally came from an excellent tutorial found on: http://www.builderau.com.au/
// http://www.builderau.com.au/webdev/soa/Create_an_audio_stitching_tool_in_PHP/0,39024680,39176704,00.htm

class FILESTRUCT {
    var $ChunkID;
    var $ChunkSize;
    var $Format;
    var $Subchunk1ID;
    var $Subchunk1Size;
    var $AudioFormat;
    var $NumChannels;
    var $SampleRate;
    var $ByteRate;
    var $BlockAlign;
    var $BitsPerSample;
    var $Subchunk2ID;
    var $Subchunk2Size;
    var $Data;

    function FILESTRUCT() {
        $this->ChunkID = array(0x0, 0x0, 0x0, 0x0);      //4
        $this->ChunkSize = array(0x0, 0x0, 0x0, 0x0);    //4
        $this->Format = array(0x0, 0x0, 0x0, 0x0);       //4
        $this->Subchunk1ID = array(0x0, 0x0, 0x0, 0x0);  //4
        $this->Subchunk1Size = array(0x0, 0x0, 0x0, 0x0);//4
        $this->AudioFormat = array(0x0, 0x0);            //2
        $this->NumChannels = array(0x0, 0x0);            //2
        $this->SampleRate = array(0x0, 0x0, 0x0, 0x0);   //4
        $this->ByteRate = array(0x0, 0x0, 0x0, 0x0);     //4
        $this->BlockAlign = array(0x0, 0x0);             //2
        $this->BitsPerSample = array(0x0, 0x0);          //2
        $this->Subchunk2ID = array(0x0, 0x0, 0x0, 0x0);  //4
        $this->Subchunk2Size = array(0x0, 0x0, 0x0, 0x0);//4
        $this->Data = array();
    }
}

class CStitcher {

    function StitchFiles(&$fsFile, &$sFiles) {

        $fsFiles = array(); //() As FILESTRUCT
        $lFileSize = 0;
	$lOffset = 0;
        $bData = array(); //() As Byte
        
        for ($i = 0; $i < count($sFiles); $i++) {
            $fsFiles[$i] = new FILESTRUCT();
            SetFile($fsFiles[$i], $sFiles[$i]);
            $lSize = CalcLittleEndianValue($fsFiles[$i]->Subchunk2Size);
            $lFileSize = $lFileSize + $lSize;
            $bData = array_merge($bData, $fsFiles[$i]->Data);
            $lOffset = $lOffset + $lSize;
        }
        $fsFile->ChunkID = $GLOBALS["ChunkID_"];
        $fsFile->ChunkSize = GetLittleEndianByteArray(36 + $lFileSize);
        $fsFile->Format = $GLOBALS["FileFormat_"];
        $fsFile->Subchunk1ID = $GLOBALS["Subchunk1ID_"];
        $fsFile->Subchunk1Size = array(0x10, 0x0, 0x0, 0x0);
        $fsFile->AudioFormat = $GLOBALS["AudioFormat_"];
        $fsFile->NumChannels = $GLOBALS["Channels_"];
        $fsFile->SampleRate = $GLOBALS["SampleRate_"];
        $fsFile->ByteRate = GetLittleEndianByteArray(
                                            CalcLittleEndianValue($GLOBALS["SampleRate_"]) *
                                            CalcLittleEndianValue($GLOBALS["Channels_"]) *
                                            (CalcLittleEndianValue($GLOBALS["BitsPerSample_"]) / 8));
        $fsFile->BlockAlign = array_splice(GetLittleEndianByteArray(CalcLittleEndianValue($GLOBALS["Channels_"]) *
                                                                    (CalcLittleEndianValue($GLOBALS["BitsPerSample_"]) / 8)), 0, 2);
        $fsFile->BitsPerSample = $GLOBALS["BitsPerSample_"];
        $fsFile->Subchunk2ID = $GLOBALS["Subchunk2ID_"];
        $fsFile->Subchunk2Size = GetLittleEndianByteArray($lFileSize);
	$fsFile->Data = $bData;
    }


}

function SetFile(&$fsFile_, $sFileName) {
    $lSize = 1;
    if (file_exists($sFileName)) {
        $fil = fopen($sFileName, "rb");
        $contents = fread($fil, count($fsFile_->ChunkID));
	$fsFile_->ChunkID = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->ChunkSize));
	$fsFile_->ChunkSize = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->Format));
	$fsFile_->Format = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->Subchunk1ID));
	$fsFile_->Subchunk1ID = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->Subchunk1Size));
	$fsFile_->Subchunk1Size = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->AudioFormat));
	$fsFile_->AudioFormat = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->NumChannels));
	$fsFile_->NumChannels = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->SampleRate));
	$fsFile_->SampleRate = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->ByteRate));
	$fsFile_->ByteRate = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->BlockAlign));
	$fsFile_->BlockAlign = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->BitsPerSample));
	$fsFile_->BitsPerSample = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->Subchunk2ID));
	$fsFile_->Subchunk2ID = bin_split($contents, 1);
        $contents = fread($fil, count($fsFile_->Subchunk2Size));
	$fsFile_->Subchunk2Size = bin_split($contents, 1);
        $lSize = CalcLittleEndianValue($fsFile_->Subchunk2Size);
	$contents = fread($fil, $lSize);
	$fsFile_->Data = bin_split($contents, 1);
        fclose($fil);
    }
}

function CalcLittleEndianValue(&$bValue) {
    $lSize_ = 0;
    for ($iByte = 0; $iByte < count($bValue); $iByte++) {
        $lSize_ += ($bValue[$iByte] * pow(16, ($iByte * 2)));
    }
    return $lSize_;
}

function GetLittleEndianByteArray($lValue) {
    $running = 0;
    $b = array(0, 0, 0, 0);
    $running = $lValue / pow(16,6);
    $b[3] = floor($running);
    $running -= $b[3];
    $running *= 256;
    $b[2] = floor($running);
    $running -= $b[2];
    $running *= 256;
    $b[1] = floor($running);
    $running -= $b[1];
    $running *= 256;
    $b[0] = round($running);
    return $b;
}


function bin_split($text, $c)
{
 $arr = array();
 $len = strlen($text);
 $a = 0;
 while($a < $len)
 {
  if ($a + $c > $len)
  {
   $c = $len - $a;
  }
  $arr[$a] = ord(substr($text, $a, $c));
  $a += $c;
 }
 return $arr;
}

?>