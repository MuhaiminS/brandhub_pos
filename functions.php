<?php
 //error_reporting(E_ERROR |E_PARSE );
  // Password encryption salt
$passwordSalt="anykeyword"; 

function redirect($url)
{
	echo "<script language=\"javascript\">window.location.href=\"$url\";</script>";
}

include("config.php");

// Disconnecting database
function disconnect()
{
        mysqli_close();
}

if ( ! function_exists('character_limiter'))
{
	function character_limiter($str, $n = 500, $end_char = '&#8230;')
	{
		if (strlen($str) < $n)
		{
			return $str;
		}

		$str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

		if (strlen($str) <= $n)
		{
			return $str;
		}

		$out = "";
		foreach (explode(' ', trim($str)) as $val)
		{
			$out .= $val.' ';

			if (strlen($out) >= $n)
			{
				$out = trim($out);
				return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
			}
		}
	}
}

// A function to get a value of a field from a particulare table with different conditions
function getnamewhere($tabname,$name,$where)     // pass the table name , name of field to return all the values
{

				$qry="SELECT ".DB_PRIFIX."$name FROM $tabname $where";
				//echo $qry;
				$result=mysqli_query($GLOBALS['conn'], $qry);
				$num=mysqli_num_rows($result);
				$i=0;
				$varname = '';
				if($num>0)
				{
					while($row = mysqli_fetch_assoc($result)) {					   
					   $varname = $row[$name]; 
					}
					//$varname=safeTextOut(mysqli_result($result,$i,$name));
					
				}
				return $varname;

}

 // Getting number of Records found in a table with / without conditions
function getTotalRecords($tabname,$where="")
{

				$qry="SELECT * FROM ".DB_PRIFIX."$tabname $where";
				//echo $qry;

				$result=mysqli_query($GLOBALS['conn'], $qry);
				$num=mysqli_num_rows($result);

				return $num;

}
// Get all the field value concating with space and line break with condition of a field and its value
function getnameall($tabname,$name,$idname,$id)     // pass the table name , name of field to return idname to match with id
{
		if($id!=""){
				$qry="SELECT * FROM ".DB_PRIFIX."$tabname where $idname=$id";
				//echo $qry;

				$result=mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $qry);
				$num=mysqli_num_rows($result);
				$i=0;
				while($i<$num)
				{

				$varname.=" ".safeTextOut(mysqli_result($result,$i,$name))."<br />";

                $i++;
				}
				return $varname;
		}
}

// Saving images  and returning the name of the image
function saveimage($userfile_name,$objectname,$path,$new_height,$new_width)
{

			$originalName=$userfile_name;
			$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.


			$filename=$_FILES[$objectname]['name'];
			$extloc=strpos($filename,".");
			$ext=substr($filename,$extloc,strlen($filename));
			$userfile_name=date("Ymdhis").$add.$ext;

					 $filedir = $path; // the directory for the original image
					 $thumbdir = $path; // the directory for the thumbnail image
					 $maxfile = '2000000';
					 $mode = '0777';
					 //$userfile_name = $_FILES[$objectname]['name'];
					 $userfile_tmp = $_FILES[$objectname]['tmp_name'];
					 $userfile_size = $_FILES[$objectname]['size'];
					 $userfile_type = $_FILES[$objectname]['type'];
					 //echo "File directory ".$filedir;
					 if (isset($_FILES[$objectname]['name']))
					 {
						 $prod_img = $filedir.$userfile_name;
						 $prod_img_thumb = $thumbdir.$userfile_name;
						 $prod_img_thumb2 = $thumbdir."tn_".$userfile_name;
						// echo "Image file path ".$prod_img;die();
						 move_uploaded_file($userfile_tmp, $prod_img);
						 chmod ($prod_img, octdec($mode));
						 $sizes = getimagesize($prod_img);

						//echo "Image Height:".$new_height;
						// $new_height = 160;
						 //    $new_width =215;

						 $destimg=ImageCreateTrueColor($new_width,$new_height)
							 or die('Problem In Creating image');
						 $srcimg=ImageCreateFromJPEG($prod_img)
							 or die('Problem In opening Source Image');
						 if(function_exists('imagecopyresampled'))
						 {
							 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
							 or die('Problem In resizing');
						 }else{
							 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
							 or die('Problem In resizing');
						 }
						 ImageJPEG($destimg,$prod_img_thumb,90)
							 or die('Problem In saving');
						 imagedestroy($destimg);


						 $new_height = 138;
						 $new_width =184;

						 $destimg=ImageCreateTrueColor($new_width,$new_height)
							 or die('Problem In Creating image');
						 $srcimg=ImageCreateFromJPEG($prod_img)
							 or die('Problem In opening Source Image');
						 if(function_exists('imagecopyresampled'))
						 {
							 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
							 or die('Problem In resizing');
						 }else{
							 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
							 or die('Problem In resizing');
						 }
						 ImageJPEG($destimg,$prod_img_thumb2,90)
							 or die('Problem In saving');
						 imagedestroy($destimg);
					 }
			//echo $userfile_name;die();
			return $userfile_name;
}


// Making a number with leading zeros
function pagenumber($pagenum)
{
			if($pagenum<10)
			  $pagenum="00".$pagenum;
			else
			   $pagenum="0".$pagenum;
			return $pagenum;
}

// Opening outlook email
function openoutlook($to,$body)
{
		redirect("mailto:$to?subject=Your new account is ready&body=$body");
}

// Getting order value of a table for order up and down purpose
function getorderval($tablename,$getid,$compareid="",$id="")
{

		if($compareid=="" || $id=="")
		    $qry="select max($getid) as $getid from ".DB_PRIFIX."$tablename";
		else
		    $qry="select max($getid) as $getid from ".DB_PRIFIX."$tablename where $compareid=$id";
		//echo $qry;die();


		$result=mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $qry);
		$num=mysqli_num_rows($result);
				 //   echo "total record ".$num;
								   if($num>0)
								        $maxval=mysqli_result($result,0,$getid);

								   $maxval=intval($maxval)+1;

		//echo $maxval;
		//die();
		return $maxval;
}


// Encrypting password string
function EnDeCrypt($str,$ky='dubaikennels')
{
		if($ky=='')return $str;
		$ky=str_replace(chr(32),'',$ky);
		if(strlen($ky)<8)exit('key error');
		$kl=strlen($ky)<32?strlen($ky):32;
		$k=array();for($i=0;$i<$kl;$i++){
		$k[$i]=ord($ky{$i})&0x1F;}
		$j=0;
		for($i=0;$i<strlen($str);$i++){
		$e=ord($str{$i});
		$str{$i}=$e&0xE0?chr($e^$k[$j]):chr($e);
		$j++;$j=$j==$kl?0:$j;}
		return $str;
}
function get_rnd_iv( $iv_len )
{

    $iv = '';
    while ( $iv_len-- > 0 )
    {
         $iv .= chr( mt_rand(  ) & 0xff );
    }

     return $iv;
}

function md5_encrypt( $plain_text, $password, $iv_len = 16 )
{

    $plain_text .= "\x13";
    $n = strlen( $plain_text );
    if ( $n % 16 )
    {
        $plain_text .= str_repeat( "\0", 16 - ( $n % 16 ) );
    }

    $i = 0;
    $enc_text = get_rnd_iv( $iv_len );
    $iv = substr( $password ^ $enc_text, 0, 512 );
    while ( $i < $n )
    {
         $block = substr( $plain_text, $i, 16 ) ^ pack( 'H*', md5( $iv ) );
         $enc_text .= $block;
         $iv = substr( $block . $iv, 0, 512 ) ^ $password;
         $i += 16;
    }

    return base64_encode( $enc_text );

}


function md5_decrypt( $enc_text, $password, $iv_len = 16 )
{

    $enc_text = base64_decode( $enc_text );
    $n = strlen( $enc_text );
    $i = $iv_len;
    $plain_text = '';
    $iv = substr( $password ^ substr( $enc_text, 0, $iv_len ), 0, 512 );
    while ( $i < $n )
    {
         $block = substr( $enc_text, $i, 16 );
         $plain_text .= $block ^ pack( 'H*', md5( $iv ) );
         $iv = substr( $block . $iv, 0, 512 ) ^ $password;
         $i += 16;
    }

    return preg_replace( '/\\x13\\x00*$/', '', $plain_text );

}

// Creating thumbnail of a image with given file name, width, height and location
function createthumb($name, $filename, $thumb_w, $thumb_h, $formato)
{
    imageresize($filename,$thumb_w,$thumb_h);
}

// Getting the value for dropdown with one where condition
function getdropdown($tablename,$valuefield,$displayfield,$orderby="",$comparefield="")
{
			if($orderby!="")
			   $qry="SELECT * FROM ".DB_PRIFIX."$tablename order by $orderby";
			else
				$qry="SELECT * FROM ".DB_PRIFIX."$tablename order by $displayfield";

			$result=mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $qry) or die("Database not connected");
			$num=mysqli_num_rows($result);
			$i=0;
				while($i<$num)
				{
					$id=mysqli_result($result,$i,$valuefield);
					$name=safeTextOut(mysqli_result($result,$i,$displayfield));
					if($comparefield==$id)
					   $option.="<option value='$id' selected>$name</option>";
					else
						$option.="<option value='$id'>$name</option>";
					$i++;
				}
			return $option;
}

// Getting the value for dropdown with different where condition
function getdropdownwhere($tablename,$valuefield,$displayfield,$where="",$orderby="",$comparefield="")
{
			if($orderby!="")
			$qry="SELECT * FROM ".DB_PRIFIX."$tablename $where order by $orderby";
			else
			$qry="SELECT * FROM ".DB_PRIFIX."$tablename $where order by $displayfield";
			$result=mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $qry) or die("Database not connected");
			$num=mysqli_num_rows($result);
			$i=0;
			while($i<$num)
			{
			$id=mysqli_result($result,$i,$valuefield);
			$name=safeTextOut(mysqli_result($result,$i,$displayfield));
			if($comparefield==$id)
			$option.="<option value='$id' selected>$name</option>";
			else
			$option.="<option value='$id'>$name</option>";
			$i++;
			}
			return $option;
}

// Resizing an emila with given path width and height
function imageresize($imagepath,$w,$h)
{

			//$img = $_GET['img'];
			//$percent = $_GET['percent'];
			//$constrain = $_GET['constrain'];
			//$w = $_GET['w'];
			//$h = $_GET['h'];

			// get image size of img
			$x = @getimagesize($img);
			// image width
			$sw = $x[0];
			// image height
			$sh = $x[1];

			if ($percent > 0) {
				// calculate resized height and width if percent is defined
				$percent = $percent * 0.01;
				$w = $sw * $percent;
				$h = $sh * $percent;
			} else {
				if (isset ($w) AND !isset ($h)) {
					// autocompute height if only width is set
					$h = (100 / ($sw / $w)) * .01;
					$h = @round ($sh * $h);
				} elseif (isset ($h) AND !isset ($w)) {
					// autocompute width if only height is set
					$w = (100 / ($sh / $h)) * .01;
					$w = @round ($sw * $w);
				} elseif (isset ($h) AND isset ($w) AND isset ($constrain)) {
					// get the smaller resulting image dimension if both height
					// and width are set and $constrain is also set
					$hx = (100 / ($sw / $w)) * .01;
					$hx = @round ($sh * $hx);

					$wx = (100 / ($sh / $h)) * .01;
					$wx = @round ($sw * $wx);

					if ($hx < $h) {
						$h = (100 / ($sw / $w)) * .01;
						$h = @round ($sh * $h);
					} else {
						$w = (100 / ($sh / $h)) * .01;
						$w = @round ($sw * $w);
					}
				}
			}

			$im = @ImageCreateFromJPEG ($img) or // Read JPEG Image
			$im = @ImageCreateFromPNG ($img) or // or PNG Image
			$im = @ImageCreateFromGIF ($img) or // or GIF Image
			$im = false; // If image is not JPEG, PNG, or GIF

			if (!$im) {
				// We get errors from PHP's ImageCreate functions...
				// So let's echo back the contents of the actual image.
				readfile ($img);
			} else {
				// Create the resized image destination
				$thumb = @ImageCreateTrueColor ($w, $h);
				// Copy from image source, resize it, and paste to image destination
				@ImageCopyResampled ($thumb, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
				// Output resized image
				@ImageJPEG ($thumb);
			}


}

// Formating email body with removing / replacing unwanted characters
function formateemail($emailmsg)
{
			$str=$emailmsg;
				 $str = str_replace("&", "&amp;", $str);
				 $str = str_replace("\'", "'", $str);
				 $str = str_replace('\"', "&quot;", $str);
				 $str = str_replace("\r", "", $str);

			//$emailmsg="<table width='100%'><tr><td><span style='font-family:Verdana;font-size:12px;'>$str</span></td></tr></table>";
			$emailmsg="<span style=\"font-family:Verdana, Verdana, Geneva, sans-serif;font-size:12px;\">$str</span>";

			return $emailmsg;

}

// Text changing the filing characters with equivalent html characters
function getTextForWeb($strText)
{
			$strText=str_replace("\'","'",$strText);
			$strText=str_replace('\"','"',$strText);
			$strText=ereg_replace("\n","<br />",$strText);
			$strText=str_replace("\n","<br />",$strText);
			return $strText;
}

// Filtering the name of email sender if something is injecting inside
function filterformaddress($string)
{
			$firststring=$string;
			$fpos=strpos($string,"@");
			$string=substr($string,$fpos);
			$pos=strpos($string,".");
			$spos=$pos+3;
			$lastchar=substr($string,$spos,1);

			if(($lastchar>='a' && $lastchar<='z')|| ($lastchar>='A' && $lastchar<='Z'))
			   $pos=$pos+4;
			else
			   $pos=$pos+3;

			$pos=$pos+$fpos;
			//echo "last char ".substr($firststring,$pos,1);
			if(substr($firststring,$pos,1)==".")
			{
			   if(substr($firststring,$pos+3,1)>='a' && substr($firststring,$pos+3,1)>='z ' || substr($firststring,$pos+3,1)>='A' && substr($firststring,$pos+3,1)>='Z ')
			     $pos=$pos+4;
			   else
			      $pos=$pos+3;
			}

			//echo "<br />Email is ----- > ".substr($firststring,0,$pos);die();
			return substr($firststring,0,$pos);
}

// Removing unwanted value from email sender's name
function namefilter($name)
{
			$name = str_replace("\r", "", $name);
			$name = str_replace("\n", "", $name);
			$name = str_replace("bcc:", "", $name);
			$name = str_replace("cc:", "", $name);
			$name = str_replace("to:", "", $name);
			$name = str_replace("Content-Type:", "", $name);
			$name = str_replace("MIME-Version:", "", $name);
			$name = str_replace("boundary=", "", $name);
			return $name;
}

function getTextForDB($string) {

	// SQL injection prevention
	if(get_magic_quotes_gpc()) {
		$string = stripslashes($string);
	}

	if (phpversion() >= '4.3.0') {
		$string = mysqli_real_escape_string($string);
	} else {
		$string = mysqli_escape_string($string);
	}

	// html special characters
	$string=ereg_replace("`","'",$string);
	$string=ereg_replace("\'","'",$string);

 	// html special characters
	$string=ereg_replace("`","'",$string);
	$string=ereg_replace("\'","'",$string);
	$string=ereg_replace("\'","'",$string);
	$string=ereg_replace("'","'",$string);
	$string=ereg_replace('\"','"',$string);
	$string=ereg_replace("&","&amp;",$string);

	$string=strip_tags($string);
	$string=htmlentities($string);

 return $string;
}

function myUrlEncode($string) {
    $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
    $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
    return str_replace($entities, $replacements, urlencode($string));
}

function safeTextIn($str_text)
{
	$str_text=strip_tags($str_text);
	return addslashes($str_text);
}

function safeTextOut($str_text)
{
    $str_text=strip_tags($str_text);
	return stripslashes($str_text);
}

function stripLineBreaks($text)
{
	$line_breaks = array("\n","\r","\r\n","<br />","<br/>","&nbsp;");
	$line_breaks_replace = array(' ',' ',' ',' ',' ','');
	$text = str_replace($line_breaks,$line_breaks_replace,$text);
	return $text;
}

function chkAdminLoggedIn() {
	if(!isset($_SESSION['user_name'])) {
		//header('Location: login.php');
		echo "<script language=\"javascript\">window.location.href=\"login.php\";</script>";
	}
	if(isset($_SESSION['role_id']) && $_SESSION['role_id'] != '1') {
		echo "<script language=\"javascript\">window.location.href=\"login.php\";</script>";
	}
}

 /** 
  * Return URL-Friendly string slug
  * @param string $string 
  * @return string 
  */

function seoUrl($string) {
    //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
    $string = strtolower($string);
    //Strip any unwanted characters
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}

// Saving images  and returning the name of the image
function saveBannerImage($userfile_name,$objectname,$path,$new_height,$new_width)
{
	$originalName=$userfile_name;
	$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.


	$filename=$_FILES[$objectname]['name'];
	$extloc=strpos($filename,".");
	$ext=substr($filename,$extloc,strlen($filename));
	$userfile_name=date("Ymdhis").$add.$ext;

			 $filedir = $path; // the directory for the original image
			 $thumbdir = $path; // the directory for the thumbnail image
			 $maxfile = '2000000';
			 $mode = '0777';
			 //$userfile_name = $_FILES[$objectname]['name'];
			 $userfile_tmp = $_FILES[$objectname]['tmp_name'];
			 $userfile_size = $_FILES[$objectname]['size'];
			 $userfile_type = $_FILES[$objectname]['type'];
			 //echo "File directory ".$filedir;
			 if (isset($_FILES[$objectname]['name']))
			 {
				 $prod_img = $filedir.$userfile_name;
				 $prod_img_thumb = $thumbdir.$userfile_name;
				 $prod_img_thumb2 = $thumbdir."tn_".$userfile_name;
				// echo "Image file path ".$prod_img;die();
				 move_uploaded_file($userfile_tmp, $prod_img);
				 chmod ($prod_img, octdec($mode));
				 $sizes = getimagesize($prod_img);

				//echo "Image Height:".$new_height;
				// $new_height = 160;
				 //    $new_width =215;

				 $destimg=ImageCreateTrueColor($new_width,$new_height)
					 or die('Problem In Creating image');
				 $srcimg=ImageCreateFromJPEG($prod_img)
					 or die('Problem In opening Source Image');
				 if(function_exists('imagecopyresampled'))
				 {
					 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }else{
					 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }
				 ImageJPEG($destimg,$prod_img_thumb,90)
					 or die('Problem In saving');
				 imagedestroy($destimg);

				 /*$new_width = "111";
				 $new_height = "97";

				 $destimg=ImageCreateTrueColor($new_width,$new_height)
					 or die('Problem In Creating image');
				 $srcimg=ImageCreateFromJPEG($prod_img)
					 or die('Problem In opening Source Image');
				 if(function_exists('imagecopyresampled'))
				 {
					 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }else{
					 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }
				 ImageJPEG($destimg,$prod_img_thumb2,90)
					 or die('Problem In saving');
				 imagedestroy($destimg);*/
			 }
	//echo $userfile_name;die();
	return $userfile_name;
}

// Saving images  and returning the name of the image
function savePageBannerImage($userfile_name,$objectname,$path,$new_height,$new_width)
{
	$originalName=$userfile_name;
	$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.


	$filename=$_FILES[$objectname]['name'];
	$extloc=strpos($filename,".");
	$ext=substr($filename,$extloc,strlen($filename));
	$userfile_name=date("Ymdhis").$add.$ext;

			 $filedir = $path; // the directory for the original image
			 $thumbdir = $path; // the directory for the thumbnail image
			 $maxfile = '2000000';
			 $mode = '0777';
			 //$userfile_name = $_FILES[$objectname]['name'];
			 $userfile_tmp = $_FILES[$objectname]['tmp_name'];
			 $userfile_size = $_FILES[$objectname]['size'];
			 $userfile_type = $_FILES[$objectname]['type'];
			 //echo "File directory ".$filedir;
			 if (isset($_FILES[$objectname]['name']))
			 {
				 $prod_img = $filedir.$userfile_name;
				 $prod_img_thumb = $thumbdir.$userfile_name;
				 $prod_img_thumb2 = $thumbdir."tn_".$userfile_name;
				// echo "Image file path ".$prod_img;die();
				 move_uploaded_file($userfile_tmp, $prod_img);
				 chmod ($prod_img, octdec($mode));
				 $sizes = getimagesize($prod_img);

				//echo "Image Height:".$new_height;
				// $new_height = 160;
				 //    $new_width =215;

				 $destimg=ImageCreateTrueColor($new_width,$new_height)
					 or die('Problem In Creating image');
				 $srcimg=ImageCreateFromJPEG($prod_img)
					 or die('Problem In opening Source Image');
				 if(function_exists('imagecopyresampled'))
				 {
					 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }else{
					 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }
				 ImageJPEG($destimg,$prod_img_thumb,90)
					 or die('Problem In saving');
				 imagedestroy($destimg);

				 /*$new_width = "111";
				 $new_height = "97";

				 $destimg=ImageCreateTrueColor($new_width,$new_height)
					 or die('Problem In Creating image');
				 $srcimg=ImageCreateFromJPEG($prod_img)
					 or die('Problem In opening Source Image');
				 if(function_exists('imagecopyresampled'))
				 {
					 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }else{
					 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }
				 ImageJPEG($destimg,$prod_img_thumb2,90)
					 or die('Problem In saving');
				 imagedestroy($destimg);*/
			 }
	//echo $userfile_name;die();
	return $userfile_name;
}

// Saving images  and returning the name of the image
function saveServiceImage($userfile_name,$objectname,$path,$new_height,$new_width)
{
	$originalName=$userfile_name;
	$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.


	$filename=$_FILES[$objectname]['name'];
	$extloc=strpos($filename,".");
	$ext=substr($filename,$extloc,strlen($filename));
	$userfile_name=date("Ymdhis").$add.$ext;

			 $filedir = $path; // the directory for the original image
			 $thumbdir = $path; // the directory for the thumbnail image
			 $maxfile = '2000000';
			 $mode = '0777';
			 //$userfile_name = $_FILES[$objectname]['name'];
			 $userfile_tmp = $_FILES[$objectname]['tmp_name'];
			 $userfile_size = $_FILES[$objectname]['size'];
			 $userfile_type = $_FILES[$objectname]['type'];
			 //echo "File directory ".$filedir;
			 if (isset($_FILES[$objectname]['name']))
			 {
				 $prod_img = $filedir.$userfile_name;
				 $prod_img_thumb = $thumbdir.$userfile_name;
				 $prod_img_thumb2 = $thumbdir."tn_".$userfile_name;
				// echo "Image file path ".$prod_img;die();
				 move_uploaded_file($userfile_tmp, $prod_img);
				 chmod ($prod_img, octdec($mode));
				 $sizes = getimagesize($prod_img);

				//echo "Image Height:".$new_height;
				// $new_height = 160;
				 //    $new_width =215;

				 $destimg=ImageCreateTrueColor($new_width,$new_height)
					 or die('Problem In Creating image');
				 $srcimg=ImageCreateFromJPEG($prod_img)
					 or die('Problem In opening Source Image');
				 if(function_exists('imagecopyresampled'))
				 {
					 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }else{
					 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }
				 ImageJPEG($destimg,$prod_img_thumb,90)
					 or die('Problem In saving');
				 imagedestroy($destimg);

				 /*$new_width = "111";
				 $new_height = "97";

				 $destimg=ImageCreateTrueColor($new_width,$new_height)
					 or die('Problem In Creating image');
				 $srcimg=ImageCreateFromJPEG($prod_img)
					 or die('Problem In opening Source Image');
				 if(function_exists('imagecopyresampled'))
				 {
					 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }else{
					 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }
				 ImageJPEG($destimg,$prod_img_thumb2,90)
					 or die('Problem In saving');
				 imagedestroy($destimg);*/
			 }
	//echo $userfile_name;die();
	return $userfile_name;
}

// Saving images  and returning the name of the image
function saveGalleryImage($userfile_name,$objectname,$path,$new_height,$new_width)
{
	$originalName=$userfile_name;
	$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.


	$filename=$_FILES[$objectname]['name'];
	$extloc=strpos($filename,".");
	$ext=substr($filename,$extloc,strlen($filename));
	$userfile_name=date("Ymdhis").$add.$ext;

			 $filedir = $path; // the directory for the original image
			 $thumbdir = $path; // the directory for the thumbnail image
			 $maxfile = '2000000';
			 $mode = '0777';
			 //$userfile_name = $_FILES[$objectname]['name'];
			 $userfile_tmp = $_FILES[$objectname]['tmp_name'];
			 $userfile_size = $_FILES[$objectname]['size'];
			 $userfile_type = $_FILES[$objectname]['type'];
			 //echo "File directory ".$filedir;
			 if (isset($_FILES[$objectname]['name']))
			 {
				 $prod_img = $filedir.$userfile_name;
				 $prod_img_thumb = $thumbdir.$userfile_name;
				 $prod_img_thumb2 = $thumbdir."tn_".$userfile_name;
				// echo "Image file path ".$prod_img;die();
				 move_uploaded_file($userfile_tmp, $prod_img);
				 chmod ($prod_img, octdec($mode));
				 $sizes = getimagesize($prod_img);

				//echo "Image Height:".$new_height;
				// $new_height = 160;
				 //    $new_width =215;

				 $destimg=ImageCreateTrueColor($new_width,$new_height)
					 or die('Problem In Creating image');
				 $srcimg=ImageCreateFromJPEG($prod_img)
					 or die('Problem In opening Source Image');
				 if(function_exists('imagecopyresampled'))
				 {
					 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }else{
					 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }
				 ImageJPEG($destimg,$prod_img_thumb,90)
					 or die('Problem In saving');
				 imagedestroy($destimg);

				 //$new_width = "96";
				 //$new_height = "68";

				/*$destimg=ImageCreateTrueColor($new_width,$new_height)
					 or die('Problem In Creating image');
				 $srcimg=ImageCreateFromJPEG($prod_img)
					 or die('Problem In opening Source Image');
				 if(function_exists('imagecopyresampled'))
				 {
					 imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }else{
					 Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
					 or die('Problem In resizing');
				 }
				 ImageJPEG($destimg,$prod_img_thumb2,90)
					 or die('Problem In saving');
				 imagedestroy($destimg);*/
			 }
	//echo $userfile_name;die();
	return $userfile_name;
}

function saveGalleryImages($userfile_name,$objectname,$path,$new_height,$new_width)
{
	
	$originalName=$userfile_name;
	$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.

	$filename=$_FILES[$objectname]['name'];
	$extloc=strpos($filename,".");
	$ext=substr($filename,$extloc,strlen($filename));
	$userfile_name=date("Ymdhis").$add.$ext;

			 $filedir = $path; // the directory for the original image
			 $thumbdir = $path; // the directory for the thumbnail image
			 $maxfile = '2000000';
			 $mode = '0777';			 
			 //$userfile_name = $_FILES[$objectname]['name'];
			 $userfile_tmp = $_FILES[$objectname]['tmp_name'];			 
			 $userfile_size = $_FILES[$objectname]['size'];
			 $userfile_type = $_FILES[$objectname]['type'];
			 $extension = end(explode(".", $filename));			 
			 $allowed_files = array("gif", "jpeg", "jpg", "png");
			 
			 if($userfile_size > $maxfile){
					echo 'File too large. File must be less than 2 megabytes.';die();
			 }			 
			 if((($userfile_type == "image/gif") || ($userfile_type == "image/jpeg") || ($userfile_type == "image/jpg") || ($userfile_type == "image/png")) && in_array($extension, $allowed_files)) {				
				 //echo "File directory ".$filedir;
				 if (isset($_FILES[$objectname]['name']))
				 {
					 $prod_img = $filedir.$userfile_name;
					 $prod_img_thumb = $thumbdir.$userfile_name;
					 $prod_img_thumb2 = $thumbdir."tn_".$userfile_name;
					// echo "Image file path ".$prod_img;die();
					 move_uploaded_file($userfile_tmp, $prod_img);
					 chmod ($prod_img, octdec($mode));
					 /*$sizes = getimagesize($prod_img);						

					 $destimg=ImageCreateTrueColor($new_width,$new_height)
						 or die('Problem In Creating image');					 
					
					 imagedestroy($destimg);*/
				 }			 
			 }else {
				return false;
			 }		
	
	return $userfile_name;
}

function saveCategoryImages($userfile_name,$objectname,$path,$new_height,$new_width)
{
	
	$originalName=$userfile_name;
	$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.

	$filename=$_FILES[$objectname]['name'];
	$extloc=strpos($filename,".");
	$ext=substr($filename,$extloc,strlen($filename));
	$userfile_name=date("Ymdhis").$add.$ext;

			 $filedir = $path; // the directory for the original image
			 $thumbdir = $path; // the directory for the thumbnail image
			 $maxfile = '5000000';
			 $mode = '0777';			 
			 //$userfile_name = $_FILES[$objectname]['name'];
			 $userfile_tmp = $_FILES[$objectname]['tmp_name'];			 
			 $userfile_size = $_FILES[$objectname]['size'];
			 $userfile_type = $_FILES[$objectname]['type'];
			 $array = explode('.', $filename);
			 $extension = end( $array); 
			 //$extension = end(explode(".", $filename));			 
			 $allowed_files = array("gif", "jpeg", "jpg", "png");
			 
			 if($userfile_size > $maxfile){
					echo 'File too large. File must be less than 5 megabytes.';die();
			 }			 
			 if((($userfile_type == "image/gif") || ($userfile_type == "image/jpeg") || ($userfile_type == "image/jpg") || ($userfile_type == "image/png")) && in_array($extension, $allowed_files)) {				
				 //echo "File directory ".$filedir;
				 if (isset($_FILES[$objectname]['name']))
				 {
					 $prod_img = $filedir.$userfile_name;
					 $prod_img_thumb = $thumbdir.$userfile_name;
					 $prod_img_thumb2 = $thumbdir."tn_".$userfile_name;
					// echo "Image file path ".$prod_img;die();
					 move_uploaded_file($userfile_tmp, $prod_img);
					 chmod ($prod_img, octdec($mode));
					 /*$sizes = getimagesize($prod_img);						

					 $destimg=ImageCreateTrueColor($new_width,$new_height)
						 or die('Problem In Creating image');					 
					
					 imagedestroy($destimg);*/
				 }			 
			 }else {
				return false;
			 }		
	
	return $userfile_name;
}

function saveProductItemImage($userfile_name,$objectname,$path,$new_height,$new_width)
{
 
 $originalName=$userfile_name;
 $add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.

 $filename=$_FILES[$objectname]['name'];
 $extloc=strpos($filename,".");
 $ext=substr($filename,$extloc,strlen($filename));
 $userfile_name=date("Ymdhis").$add.$ext;

    $filedir = $path; // the directory for the original image
    $thumbdir = $path; // the directory for the thumbnail image
    $maxfile = '3000000';
    $mode = '0777';    
    //$userfile_name = $_FILES[$objectname]['name'];
    $userfile_tmp = $_FILES[$objectname]['tmp_name'];    
    $userfile_size = $_FILES[$objectname]['size'];
    $userfile_type = $_FILES[$objectname]['type'];
    //$extension = end(explode(".", $filename));     
    $tmp = explode('.', $filename);
    $extension = end($tmp);

    $allowed_files = array("gif", "jpeg", "jpg", "png");
    
    if($userfile_size > $maxfile){
     echo 'File too large. File must be less than 3 megabytes.';die();
    }    
    if((($userfile_type == "image/gif") || ($userfile_type == "image/jpeg") || ($userfile_type == "image/jpg") || ($userfile_type == "image/png")) && in_array($extension, $allowed_files)) {    
     //echo "File directory ".$filedir;
     if (isset($_FILES[$objectname]['name']))
     {
      $prod_img = $filedir."or_".$userfile_name;
      //$prod_img_thumb = $thumbdir.$userfile_name;
      $prod_img_thumb2 = $thumbdir.$userfile_name;
     // echo "Image file path ".$prod_img;die();
      move_uploaded_file($userfile_tmp, $prod_img);
      chmod ($prod_img, octdec($mode));
      $sizes = getimagesize($prod_img);

    //echo "Image Height:".$new_height;
    // $new_height = 160;
     //    $new_width =215;

     $destimg=ImageCreateTrueColor($new_width,$new_height)
      or die('Problem In Creating image');
     $srcimg=ImageCreateFromJPEG($prod_img)
      or die('Problem In opening Source Image');
     if(function_exists('imagecopyresampled'))
     {
      imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
      or die('Problem In resizing');
     }else{
      Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))
      or die('Problem In resizing');
     }
     ImageJPEG($destimg,$prod_img_thumb2,90)
      or die('Problem In saving');
     imagedestroy($destimg);
     }    
    }else {
    echo 'Problem In Image Type';die();
    }  
 
 return $userfile_name;
}

function saveGalleryaudio($userfile_name,$objectname,$path)
{
	
	$originalName=$userfile_name;
	$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.

	$filename=$_FILES[$objectname]['name'];
	$extloc=strpos($filename,".");
	$ext=substr($filename,$extloc,strlen($filename));
	$userfile_name=date("Ymdhis").$add.$ext;

			 $filedir = $path; // the directory for the original image
			 $thumbdir = $path; // the directory for the thumbnail image
			 $maxfile = '2000000';
			 $mode = '0777';			 
			 //$userfile_name = $_FILES[$objectname]['name'];
			 $userfile_tmp = $_FILES[$objectname]['tmp_name'];			 
			 $userfile_size = $_FILES[$objectname]['size'];
			 $userfile_type = $_FILES[$objectname]['type'];
			 $extension = end(explode(".", $filename));			 
			 $allowed_files = array("mp4", "mp3", "avi");
			 
			 if($userfile_size > $maxfile){
					echo 'File too large. File must be less than 2 megabytes.';die();
			 }			 
			 if((($userfile_type == "audio/mp4") || ($userfile_type == "audio/mp3") || ($userfile_type == "audio/avi")) && in_array($extension, $allowed_files)) {				
				 //echo "File directory ".$filedir;
				 if (isset($_FILES[$objectname]['name']))
				 {
					 $prod_img = $filedir.$userfile_name;
					 $prod_img_thumb = $thumbdir.$userfile_name;
					 $prod_img_thumb2 = $thumbdir."tn_".$userfile_name;
					// echo "Image file path ".$prod_img;die();
					 move_uploaded_file($userfile_tmp, $prod_img);
					 chmod ($prod_img, octdec($mode));
					 /*$sizes = getimagesize($prod_img);						

					 $destimg=ImageCreateTrueColor($new_width,$new_height)
						 or die('Problem In Creating image');					 
					
					 imagedestroy($destimg);*/
				 }			 
			 }else {
				echo 'Problem In Image Type';die();
			 }		
	
	return $userfile_name;
}

// Saving pdf  and returning the name of the pdf
function savePdfFile($userfile_name,$objectname,$path)
{
	$originalName=$userfile_name;
	$add=$originalName; // the path with the file name where the file will be stored, upload is the directory name.


	$filename=$_FILES[$objectname]['name'];
	$extloc=strpos($filename,".");
	$ext=substr($filename,$extloc,strlen($filename));
	$userfile_name=date("Ymdhis").$add.$ext;

			 $filedir = $path; // the directory for the original image
			 $maxfile = '2000000';
			 $mode = '0777';
			 //$userfile_name = $_FILES[$objectname]['name'];
			 $userfile_tmp = $_FILES[$objectname]['tmp_name'];
			 //echo "File directory ".$filedir;
			 if (isset($_FILES[$objectname]['name']))
			 {
				 $prod_img = $filedir.$userfile_name;
				// echo "Image file path ".$prod_img;die();
				 move_uploaded_file($userfile_tmp, $prod_img);
				 chmod ($prod_img, octdec($mode));				 
			 }
	//echo $userfile_name;die();
	return $userfile_name;
}

function getPageDetails($page_slug) {
	$page_details = array();
	if(isset($page_slug)) {
		$query="SELECT PC.* FROM ".DB_PRIFIX."pages AS P LEFT JOIN page_contents AS PC ON P.id = PC.page_id WHERE P.page_slug = '$page_slug'";
		$run = mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $query);
		while($row = mysqli_fetch_array($run)) {
			$page_details['page_id'] = $row['page_id'];
			$page_details['page_title'] = $row['page_title'];			
			$page_details['page_banner'] = $row['page_banner'];
			$page_details['page_section1'] = $row['page_section1'];
			$page_details['page_section2'] = $row['page_section2'];			
		}
	}
	return $page_details;			
}

function getBannersList()
{
	$banners = array();
	$query = "SELECT * FROM ".DB_PRIFIX."index_banner ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$ban_id = $row['id'];
		$banners[$ban_id]['banner_title'] = $row['banner_title'];
		$banners[$ban_id]['banner_subtitle'] = $row['banner_subtitle'];
		$banners[$ban_id]['banner_link'] = $row['banner_link'];
		$banners[$ban_id]['banner_link_text'] = $row['banner_link_text'];
		$banners[$ban_id]['banner_img'] = $row['banner_img'];
	}
	//echo '<pre>';print_r($banners);echo '</pre>';
	return $banners;
}



function getordersList($order_id = 0, $limit = 0, $order_by = "ASC")
{
	$brands = array();
	$query = "SELECT * FROM ".DB_PRIFIX."orders";	
	$query .= " WHERE order_id = ".$order_id;	
	if($limit > 0)
		$query .= " LIMIT ".$limit;
	
	$run = mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $query);	
	while($row = mysqli_fetch_array($run)) {
		$id = $row['id'];
		$orders[$id]['order_id'] = $row['id'];
		$orders[$id]['contact_name'] = $row['contact_name'];
		$orders[$id]['contact_number'] = $row['contact_number'];
		$orders[$id]['address'] = $row['address'];
        $orders[$id]['delivery_date'] = $row['delivery_date'];
		$orders[$id]['delivery_time'] = $row['delivery_time'];
		$orders[$id]['advance_amount'] = $row['advance_amount'];
		$orders[$id]['payment_status'] = $row['payment_status'];
		$orders[$id]['status'] = $row['status'];
	}
	return $orders;
}

function getCategoriesList($parent_id = 0, $limit = 0, $order_by = "ASC")
{
	$categories = array();
	$query = "SELECT * FROM ".DB_PRIFIX."categories";
	$query .= " WHERE parent_id = ".$parent_id;
	$query .= " ORDER BY id ".$order_by;
	if($limit > 0)
		$query .= " LIMIT ".$limit;
	
	$run = mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$id = $row['id'];
		$categories[$id]['category_id'] = $row['id'];
		$categories[$id]['parent_id'] = $row['parent_id'];
		$categories[$id]['category_title'] = $row['category_title'];
		$categories[$id]['category_slug'] = $row['category_slug'];
		$categories[$id]['category_img'] = $row['category_img'];
		$categories[$id]['category_content'] = $row['category_content'];
	}
	return $categories;
}



function getCheckId($table, $id = 0) {
	$service_detail = array();
	$query="SELECT * FROM ".DB_PRIFIX."$table WHERE id = '$id'";
	$run = mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $query);	
	while($row = mysqli_fetch_array($run)) {
		$service_detail['service_id'] = $row['id'];
	}
	return $service_detail;
}

function getCategoryDetails($category_id) {
	$category_details = array();
	if(isset($category_id)) {
		$query="SELECT * FROM ".DB_PRIFIX."categories WHERE id = '$category_id'";
		$run = mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $query);
		while($row = mysqli_fetch_array($run)) {
			$category_details['category_id'] = $row['id'];
			$category_details['parent_id'] = $row['parent_id'];
			$category_details['category_title'] = $row['category_title'];
			$category_details['category_slug'] = $row['category_slug'];
			$category_details['category_img'] = $row['category_img'];
			$category_details['category_content'] = $row['category_content'];
		}
	}
	return $category_details;			
}



function getCurrentPage() {
	$page_name = basename($_SERVER['PHP_SELF']);
	return $page_name;
}
function getUnitList()
{
	$cat = array();
	$query = "SELECT * FROM ".DB_PRIFIX."item_units ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$unit_id = $row['id'];
		$unit[$row['unit_name']] = $row['unit_name'];
	}
	return $unit;	
}

function randomString($length = 6) {
	$str = "";
	$characters = array_merge(range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
}
function randomStringLong($length = 7) {
	$str = "";
	$characters = array_merge(range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
}

function displayPaginationBelow($per_page,$page,$tb_name) {
 $page_url="?";
 $query = "SELECT * FROM ".DB_PRIFIX."".$tb_name; 
 $result= mysqli_query($GLOBALS['conn'], $query);
 $total = mysqli_num_rows($result);
 $adjacents = "2"; 
 $page = ($page == 0 ? 1 : $page); 
 $start = ($page - 1) * $per_page; 
 $prev = $page - 1; 
 $next = $page + 1;
 $setLastpage = ceil($total/$per_page);
 $lpm1 = $setLastpage - 1;
 $setPaginate = ""; 
 if($setLastpage > 1)
 {  
  $setPaginate .= "<ul class='setPaginate'>";
    $setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
  if ($setLastpage < 7 + ($adjacents * 2))
  {  
   for ($counter = 1; $counter <= $setLastpage; $counter++)
   {
    if ($counter == $page)
     $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
    else
     $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
   }
  }
  elseif($setLastpage > 5 + ($adjacents * 2))
  {
   if($page < 1 + ($adjacents * 2)) 
   {
    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    {
     if ($counter == $page)
      $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
     else
      $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
    }
    $setPaginate.= "<li class='dot'>...</li>";
    $setPaginate.= "<li><a href='{$page_url}page=$lpm1'>$lpm1</a></li>";
    $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>$setLastpage</a></li>"; 
   }
   elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
   {
    $setPaginate.= "<li><a href='{$page_url}page=1'>1</a></li>";
    $setPaginate.= "<li><a href='{$page_url}page=2'>2</a></li>";
    $setPaginate.= "<li class='dot'>...</li>";
    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    {
     if ($counter == $page)
      $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
     else
      $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
    }
    $setPaginate.= "<li class='dot'>..</li>";
    $setPaginate.= "<li><a href='{$page_url}page=$lpm1'>$lpm1</a></li>";
    $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>$setLastpage</a></li>"; 
   }
   else
   {
    $setPaginate.= "<li><a href='{$page_url}page=1'>1</a></li>";
    $setPaginate.= "<li><a href='{$page_url}page=2'>2</a></li>";
    $setPaginate.= "<li class='dot'>..</li>";
    for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
    {
     if ($counter == $page)
      $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
     else
      $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
    }
   }
  }
  if ($page < $counter - 1){
   $setPaginate.= "<li><a href='{$page_url}page=$next'>Next</a></li>";
   $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>Last</a></li>";
  }else{
   $setPaginate.= "<li><a class='current_page'>Next</a></li>";
   $setPaginate.= "<li><a class='current_page'>Last</a></li>";
  }
  $setPaginate.= "</ul>\n"; 
 }
 return $setPaginate;
}
function getCustomerDetail($id) {
	$service_detail = array();
	$query="SELECT * FROM ".DB_PRIFIX."customer_details WHERE customer_id = '$id'";
	return mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'], $query));
}
?>