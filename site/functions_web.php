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
        mysql_close();
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

				$qry="SELECT $name FROM ".DB_PRIFIX."$tabname $where";
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

				$result=mysql_query($qry);
				$num=mysql_num_rows($result);

				return $num;

}
// Get all the field value concating with space and line break with condition of a field and its value
function getnameall($tabname,$name,$idname,$id)     // pass the table name , name of field to return idname to match with id
{
		if($id!=""){
				$qry="SELECT * FROM ".DB_PRIFIX."$tabname where $idname=$id";
				//echo $qry;

				$result=mysql_query($qry);
				$num=mysql_num_rows($result);
				$i=0;
				while($i<$num)
				{

				$varname.=" ".safeTextOut(mysql_result($result,$i,$name))."<br />";

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
		    $qry="select max($getid) as $getid from $tablename";
		else
		    $qry="select max($getid) as $getid from $tablename where $compareid=$id";
		//echo $qry;die();


		$result=mysql_query($qry);
		$num=mysql_num_rows($result);
				 //   echo "total record ".$num;
								   if($num>0)
								        $maxval=mysql_result($result,0,$getid);

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

			$result=mysql_query($qry) or die("Database not connected");
			$num=mysql_num_rows($result);
			$i=0;
				while($i<$num)
				{
					$id=mysql_result($result,$i,$valuefield);
					$name=safeTextOut(mysql_result($result,$i,$displayfield));
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
			$result=mysql_query($qry) or die("Database not connected");
			$num=mysql_num_rows($result);
			$i=0;
			while($i<$num)
			{
			$id=mysql_result($result,$i,$valuefield);
			$name=safeTextOut(mysql_result($result,$i,$displayfield));
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
		$string = mysql_real_escape_string($string);
	} else {
		$string = mysql_escape_string($string);
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

function chkUserLoggedIn() {
	if(!isset($_SESSION['user_name'])) {
		//header('Location: login.php');
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

function displayPaginationBelow($per_page,$page,$tb_name) {
    $page_url="?";
	$query = "SELECT count(*) as totalCount FROM ".DB_PRIFIX."".$tb_name;	
	//print_r($query);exit;
	$rec = mysql_fetch_array(mysql_query($query));
	$total = $rec['totalCount'];
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

//Pos WEb
function getCategoryList()
{
	$category = array();
	$query = "SELECT id, category_title, category_slug FROM ".DB_PRIFIX."item_category WHERE category_slug != 'stock'";	
	
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$id = $row['id'];
		$category[$id]['id'] = $row['id'];
		$category[$id]['category_title'] = $row['category_title'];
		$category[$id]['category_slug'] = $row['category_slug'];
	}
	return $category;
}

function getItemsList($limit = 0, $order_by = "ASC")
{
	$items = array();
	$query = "SELECT items.id, cat_id, name, other_name, price, image, CGST, SGST, item_category.category_slug, items.barcode_id FROM ".DB_PRIFIX."items as items LEFT JOIN ".DB_PRIFIX."item_category as item_category ON items.cat_id = item_category.id WHERE items.active != '0'";	
	$query .= " ORDER BY id ".$order_by;
	if($limit > 0)
		$query .= " LIMIT ".$limit;
	//echo $query;
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$id = $row['id'];
		$items[$id]['id'] = $row['id'];
		$items[$id]['cat_id'] = $row['cat_id'];
		$items[$id]['name'] = $row['name'];
		$items[$id]['other_name'] = $row['other_name'];
		$items[$id]['price'] =  $row['price'] + (($row['price'] / 100) * ($row['CGST'] + $row['CGST']));
		$items[$id]['image'] = $row['image'];		
		$items[$id]['category_slug'] = $row['category_slug'];
		$items[$id]['barcode_id'] = $row['barcode_id'];
	}
	return $items;
}

function custom_echo($x, $length) { if(strlen($x)<=$length) { echo $x; } else { $y=substr($x,0,$length) . '...'; echo $y; } }
	
$letters = range('A', 'Z');

//Main Sale insert
function getSaleOrderItemDetails($inputs) {
$shop_id = $_SESSION['shop_id'];
$user_id = $_SESSION['user_id'];
$order_type = $inputs['order_type'];
$driver_id=(isset($inputs['driver_id']) && $inputs['driver_id'] !='') ? $inputs['driver_id'] : '';
$card_num=(isset($inputs['card_num']) && $inputs['card_num'] !='') ? $inputs['card_num'] : '';
$receipt_id = RECIPT_PRE;
if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') {
	$vat = BILL_TAX_VAL;
} else {
	$vat = '';
}
$payment_type = (isset($inputs['payment_type']) && $inputs['payment_type'] !='') ? $inputs['payment_type'] : '';
$payment_status=(isset($inputs['payment_status']) && $inputs['payment_status'] !='') ? $inputs['payment_status'] : '';
$status='pending';
$ordered_date=date("Y-m-d H:i:s");
$contact_name = (isset($inputs['contact_name']) && $inputs['contact_name'] !='') ? $inputs['contact_name'] : '';
$contact_number = (isset($inputs['contact_number']) && $inputs['contact_number'] !='') ? $inputs['contact_number'] : '0';
$address = (isset($inputs['address']) && $inputs['address'] !='') ? $inputs['address'] : '';
$amount_given = (isset($inputs['amount_given']) && $inputs['amount_given'] !='') ? $inputs['amount_given'] : '';
$grand_total = (isset($inputs['grand_total']) && $inputs['grand_total'] !='') ? $inputs['grand_total'] : '';
$balance_amount = (isset($inputs['balance_amount']) && $inputs['balance_amount'] !='') ? $inputs['balance_amount'] : '';
$table_id = (isset($inputs['table_id']) && $inputs['table_id'] !='') ? $inputs['table_id'] : '';
$floor_id = (isset($inputs['floor_id']) && $inputs['floor_id'] !='') ? $inputs['floor_id'] : '';
$num_members = (isset($inputs['num_members']) && $inputs['num_members'] !='') ? $inputs['num_members'] : '';
$remarks = (isset($inputs['remarks']) && $inputs['remarks'] !='') ? $inputs['remarks'] : '';

$combo_package_name = (isset($inputs['combo_package_name']) && $inputs['combo_package_name'] !='') ? $inputs['combo_package_name'] : '';
$combo_package_price = (isset($inputs['combo_package_price']) && $inputs['combo_package_price'] !='') ? $inputs['combo_package_price'] : '';

$vat = $grand_total/21;
$combo_package_price = round(($grand_total-$vat),2);

$discount = (isset($inputs['discount']) && $inputs['discount'] !='') ? $inputs['discount'] : '0.0';
$additional = (isset($inputs['additional']) && $inputs['additional'] !='') ? $inputs['additional'] : '0.0';
$customer_id = '';
$sql = "SELECT customer_id FROM ".DB_PRIFIX."customer_details WHERE customer_number = $contact_number";
$cus_details = mysqli_query($GLOBALS['conn'], $sql);
$num = mysqli_num_rows($cus_details);
if($num > 0)
{
	$customer_details = mysqli_fetch_assoc($cus_details);
	$customer_id = $customer_details['customer_id'];
} else {
	if($contact_number != 0) {
		$result1 = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."customer_details(customer_name, customer_number, customer_address) VALUES('$contact_name', '$contact_number', '$address')");
		if ($result1) {            
			$customer_id = mysqli_insert_id($GLOBALS['conn']);
		}
	}
}

$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."sale_orders(table_id, floor_id, num_members, shop_id, user_id, customer_id, order_type, payment_type, discount , contact_name, contact_number, address,driver_id,
ordered_date,payment_status,status,amount_given,balance_amount,remarks, card_num, vat, combo_package_price, combo_package_name) 
VALUES('$table_id', '$floor_id', '$num_members', '$shop_id', '$user_id', '$customer_id', '$order_type', '$payment_type', '$discount' , '$contact_name', '$contact_number', '$address','$driver_id','$ordered_date','$payment_status','$status','$amount_given','$balance_amount','$remarks', '$card_num', '$vat', '$combo_package_price', '$combo_package_name')");
if ($result) {            
	$sale_order_id = mysqli_insert_id($GLOBALS['conn']);
	$receipt_id = $receipt_id.$sale_order_id;
	mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."sale_orders SET receipt_id = '$receipt_id' WHERE id = '$sale_order_id'");
	//$obj = (array)json_decode($inputs['items'], TRUE);
	$items_l = $inputs['items'];
	$qty_l = $inputs['quantity'];	
	$unit_price_l = $inputs['unit_price'];
	//echo '<pre>'; print_r($items_l); die;
	$total_amount=0;
	for($i=0; $i<count($items_l); $i++) {
		$item_id = $items_l[$i];
		$qty = $qty_l[$i];
		$unit_price = $unit_price_l[$i];

		$sql = "SELECT id, name, price, weight, unit, CGST, SGST, stock FROM ".DB_PRIFIX."items WHERE id = $item_id";
		$item_details = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'], $sql));
		$item_id_i = $item_details['id'];
		if($item_details['weight']) {
			$item_name_i = $item_details['name'].' - '.$item_details['weight'].$item_details['unit'];
		} else {
			$item_name_i = $item_details['name'];
		}		
		$item_price_i = $unit_price;//$item_details['price'];
		$tax_without_price = $unit_price;//$item_details['price'];
		$SGST_i = $item_details['SGST'];
		$CGST_i = $item_details['CGST'];
		$stock = $item_details['stock'];
		$item_price_i = $item_price_i + (($item_price_i / 100) * ($item_details['SGST'] + $item_details['CGST']));
		$multiplle_val=$qty*$item_price_i;
		$total_amount+=$multiplle_val;
		
		$sql = "INSERT INTO ".DB_PRIFIX."sale_order_items (sale_order_id, item_id, item_name, price, qty, CGST, SGST, tax_without_price) VALUES('$sale_order_id', '$item_id_i', '$item_name_i', '$item_price_i', '$qty', '$CGST_i', '$SGST_i', '$tax_without_price')";
		// echo $sql; die;
		mysqli_query($GLOBALS['conn'], $sql);

		$stock_reaming = $stock - $qty;
		if($stock_reaming >= 0) {
			mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."items SET stock = '$stock_reaming' WHERE id = '$item_id_i'");
		}
	}
	if($additional != 0 && $additional > 0) {
	mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."sale_order_items (sale_order_id, item_name, price, qty, CGST, SGST, tax_without_price) VALUES('$sale_order_id', 'Additional', '$additional', '1', '0', '0', '$additional')");
	}
	if($order_type == 'dine_in'){
		$table_result=mysqli_query($GLOBALS['conn'], "SELECT * from ".DB_PRIFIX."table_management where table_id=$table_id");
			
		if($row=mysqli_fetch_assoc($table_result)){
			$filled_seats=$row['filled_seats'];
	
			if(isset($num_members)){
				$seats=$filled_seats+$num_members;			
				mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."table_management set filled_seats=$seats WHERE table_id=$table_id");
			}
		}
	}
	if($payment_type=='credit'){
		$result_credit=mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."credit_sale(customer_id, name,number,type,amount,paid_date,sale_order_id,user_id,shop_id) 
		VALUES('$customer_id', '$contact_name','$contact_number','credit','$total_amount','$ordered_date','$sale_order_id','$user_id','$shop_id')");
		
	}
	$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = $sale_order_id");  
	return mysqli_fetch_assoc($result);
} else {
	return false;
}
}

function getSaleOrderItemDetailsEdit($inputs) {
$shop_id = $_SESSION['shop_id'];
$user_id = $_SESSION['user_id'];
$order_type = $inputs['order_type'];
$sale_order_id=(isset($inputs['sale_order_id']) && $inputs['sale_order_id'] !='') ? $inputs['sale_order_id'] : '';
$driver_id=(isset($inputs['driver_id']) && $inputs['driver_id'] !='') ? $inputs['driver_id'] : '';
$card_num=(isset($inputs['card_num']) && $inputs['card_num'] !='') ? $inputs['card_num'] : '';
$receipt_id = RECIPT_PRE;
$payment_type = (isset($inputs['payment_type']) && $inputs['payment_type'] !='') ? $inputs['payment_type'] : '';
$payment_status=(isset($inputs['payment_status']) && $inputs['payment_status'] !='') ? $inputs['payment_status'] : '';
$status='pending';
$ordered_date=date("Y-m-d H:i:s");
$contact_name = (isset($inputs['contact_name']) && $inputs['contact_name'] !='') ? $inputs['contact_name'] : '';
$contact_number = (isset($inputs['contact_number']) && $inputs['contact_number'] !='') ? $inputs['contact_number'] : '0';
$address = (isset($inputs['address']) && $inputs['address'] !='') ? $inputs['address'] : '';
$amount_given = (isset($inputs['amount_given']) && $inputs['amount_given'] !='') ? $inputs['amount_given'] : '';
$balance_amount = (isset($inputs['balance_amount']) && $inputs['balance_amount'] !='') ? $inputs['balance_amount'] : '';
$grand_total = (isset($inputs['grand_total']) && $inputs['grand_total'] !='') ? $inputs['grand_total'] : '';
$table_id = (isset($inputs['table_id']) && $inputs['table_id'] !='') ? $inputs['table_id'] : '';
$floor_id = (isset($inputs['floor_id']) && $inputs['floor_id'] !='') ? $inputs['floor_id'] : '';
$num_members = (isset($inputs['num_members']) && $inputs['num_members'] !='') ? $inputs['num_members'] : '';

$combo_package_name = (isset($inputs['combo_package_name']) && $inputs['combo_package_name'] !='') ? $inputs['combo_package_name'] : '';
$combo_package_price = (isset($inputs['combo_package_price']) && $inputs['combo_package_price'] !='') ? $inputs['combo_package_price'] : '';
$vat = $grand_total/21;
$combo_package_price = round(($grand_total-$vat),2);


$discount = (isset($inputs['discount']) && $inputs['discount'] !='') ? $inputs['discount'] : '0.0';
$additional = (isset($inputs['additional']) && $inputs['additional'] !='') ? $inputs['additional'] : '0.0';
$customer_id = '';
$sql = "SELECT customer_id FROM ".DB_PRIFIX."customer_details WHERE customer_number = $contact_number";
$cus_details = mysqli_query($GLOBALS['conn'], $sql);
$num = mysqli_num_rows($cus_details);
if($num > 0)
{
	$customer_details = mysqli_fetch_assoc($cus_details);
	$customer_id = $customer_details['customer_id'];
} else {
	if($contact_number != 0) {
		$result1 = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."customer_details(customer_name, customer_number, customer_address) VALUES('$contact_name', '$contact_number', '$address')");
		if ($result1) {            
			$customer_id = mysqli_insert_id($GLOBALS['conn']);
		}
	}
}

//$result = mysqli_query($GLOBALS['conn'], "INSERT INTO sale_orders(table_id, floor_id, num_members, shop_id, user_id, customer_id, order_type, payment_type, discount , contact_name, contact_number, address,driver_id, ordered_date,payment_status,status,amount_given,balance_amount) VALUES('$table_id', '$floor_id', '$num_members', '$shop_id', '$user_id', '$customer_id', '$order_type', '$payment_type', '$discount' , '$contact_name', '$contact_number', '$address','$driver_id','$ordered_date','$payment_status','$status','$amount_given','$balance_amount')");
//if ($result) {            
	//$sale_order_id = mysqli_insert_id($GLOBALS['conn']);
	//$receipt_id = $receipt_id.$sale_order_id;
	mysqli_query($GLOBALS['conn'], "UPDATE sale_orders SET combo_package_price = '$combo_package_price', vat = '$vat'  WHERE id = '$sale_order_id'");
	//$obj = (array)json_decode($inputs['items'], TRUE);
	$items_l = $inputs['items'];
	$qty_l = $inputs['quantity'];
	
	$total_amount=0;
	mysqli_query($GLOBALS['conn'], "DELETE FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id = '$sale_order_id'");
	for($i=0; $i<count($items_l); $i++) {
		$item_id = $items_l[$i];
		$qty = $qty_l[$i];

		$sql = "SELECT id, name, price, weight, unit, CGST, SGST, stock FROM ".DB_PRIFIX."items WHERE id = $item_id";
		$item_details = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'], $sql));
		$item_id_i = $item_details['id'];
		if($item_details['weight']) {
			$item_name_i = $item_details['name'].' - '.$item_details['weight'].$item_details['unit'];
		} else {
			$item_name_i = $item_details['name'];
		}		
		$item_price_i = $item_details['price'];
		$tax_without_price = $item_details['price'];
		$SGST_i = $item_details['SGST'];
		$CGST_i = $item_details['CGST'];
		$stock = $item_details['stock'];
		$item_price_i = $item_price_i + (($item_price_i / 100) * ($item_details['SGST'] + $item_details['CGST']));
		$multiplle_val=$qty*$item_price_i;
		$total_amount+=$multiplle_val;		
		
		mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."sale_order_items (sale_order_id, item_id, item_name, price, qty, CGST, SGST, tax_without_price) VALUES('$sale_order_id', '$item_id_i', '$item_name_i', '$item_price_i', '$qty', '$CGST_i', '$SGST_i', '$tax_without_price')");

		// $stock_reaming = $stock - $qty;
		// if($stock_reaming >= 0) {
		// 	mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."items SET stock = '$stock_reaming' WHERE id = '$item_id_i'");
		// }
	}	
	
	/*if($order_type == 'dine_in'){
		$table_result=mysqli_query($GLOBALS['conn'], "SELECT * from ".DB_PRIFIX."table_management where table_id=$table_id");
			
		if($row=mysqli_fetch_assoc($table_result)){
			$filled_seats=$row['filled_seats'];
	
			if(isset($num_members)){
				$seats=$filled_seats+$num_members;			
				mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."table_management set filled_seats=$seats WHERE table_id=$table_id");
			}
		}
	}
	if($payment_type=='credit'){
		$result_credit=mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."credit_sale(customer_id, name,number,type,amount,paid_date,sale_order_id,user_id,shop_id) 
		VALUES('$customer_id', '$contact_name','$contact_number','credit','$total_amount','$ordered_date','$sale_order_id','$user_id','$shop_id')");
		
	}*/
	$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = $sale_order_id");  
	return mysqli_fetch_assoc($result);
//} else {
	//return false;
//}
}

function getCustomerDetail($id) {
	$service_detail = array();
	$query="SELECT * FROM ".DB_PRIFIX."customer_details WHERE customer_id = '$id'";
	return mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'], $query));
}

function getFloorsList() {
	$query = "SELECT * FROM ".DB_PRIFIX."floors WHERE 1";                    
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
		return $result_arr;
	}		
	else {
		return false;
	}
}

function getTablesList($floor_id) {
	$query = "SELECT * FROM ".DB_PRIFIX."table_management WHERE floor_id = $floor_id";                    
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
		return $result_arr;
	}		
	else {
		return false;
	}
}

function getTablesSeats($floor_id, $table_id) {
	$query = "SELECT * FROM ".DB_PRIFIX."table_management WHERE floor_id = $floor_id AND table_id = $table_id";
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr = $row;			
		}
		return $result_arr;
	}		
	else {
		return false;
	}
}

function getDineInPending($floor_id, $table_id) {
	$query = "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE floor_id = $floor_id AND table_id = $table_id AND order_type = 'dine_in' AND payment_status = 'unpaid'";
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
		return $result_arr;
	}		
	else {
		return false;
	}
}

function gettableno($table_id)
{
	$where = "WHERE table_id = '$table_id'";
	$service = getnamewhere('table_management', 'table_no', $where);
	return $service;
}

function getDineInRunning($payment_status) {
	$query = "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE order_type = 'dine_in' AND payment_status = '$payment_status' ORDER BY id DESC";
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
		return $result_arr;
	}		
	else {
		return false;
	}
}

function getTablesListFull()
{
	$query = "SELECT * FROM ".DB_PRIFIX."table_management";
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
		return $result_arr;
	}		
	else {
		return false;
	}
}

function getDrivers(){
		$query = "SELECT * FROM ".DB_PRIFIX."drivers WHERE 1";  
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
		else {
			return false;
		}
	}

//Send SMS

function sendsms($api_key, $from, $to, $message){

	$url = "https://api.smsbump.com/send/".$api_key.".json?to=".$to."&message=".$message."";
	// init the resource
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
	));
	
	curl_setopt($ch, CURLOPT_HTTPGET, 1);
	
	//Ignore SSL certificate verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	
	//get response
	$output = curl_exec($ch);
	
	//Print error if any
	if(curl_errno($ch))
	{
		echo 'error:' . curl_error($ch);
	}
	
	curl_close($ch);
	
	//return $output;
	
}

function getUserDetails($user_id)
{
	$user = array();
	$query = "SELECT * FROM ".DB_PRIFIX."users WHERE id = '$user_id'";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$user_id = $row['id'];
		$user['user_action'] = $row['user_action'];
	}
	//echo '<pre>';print_r($user);echo '</pre>';
	return $user;
}

function getComboOffers()
{
	$user = array();
	$query = "SELECT * FROM ".DB_PRIFIX."combo_package WHERE 1";
	//echo $query;
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
		else {
			return false;
		}
}

function getcomboitems($id)
{
	$where = "WHERE id = '$id'";
	$service = getnamewhere('combo_package', 'package_items', $where);
	return $service;
}
function getcomboname($id)
{
	$where = "WHERE id = '$id'";
	$service = getnamewhere('combo_package', 'package_name', $where);
	return $service;
}
function getcomboprice($id)
{
	$where = "WHERE id = '$id'";
	$service = getnamewhere('combo_package', 'package_price', $where);
	return $service;
}
function getdrivername($id)
{
	$where = "WHERE id = '$id'";
	$service = getnamewhere('drivers', 'name', $where);
	return $service;
}
function getReaminCombaoBill() { 
	$qry = "SELECT so.id, so.contact_number, so.receipt_id FROM `sale_orders` as so LEFT JOIN sale_order_items as soi ON (soi.sale_order_id = so.id) WHERE so.order_type = 'combo' AND soi.staff_id = '0' GROUP BY soi.sale_order_id";
	//echo $qry;
	$result = mysqli_query($GLOBALS['conn'], $qry);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
		return $result_arr;
	}		
	else {
		return false;
	}
}
function getCombaoBillSingle($id) {
	$qry = "SELECT * FROM `sale_orders` as so LEFT JOIN sale_order_items as soi ON (soi.sale_order_id = so.id) WHERE so.order_type = 'combo' AND so.id = '$id'";
	//echo $qry;
	$result = mysqli_query($GLOBALS['conn'], $qry);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
		return $result_arr;
	}		
	else {
		return false;
	}
}
?>