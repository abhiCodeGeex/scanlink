<?php	 	
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

//set it to writable location, a place for temp generated PNG files
error_reporting(E_ALL ^ E_NOTICE);
$PNG_TEMP_DIR = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

//html PNG location prefix
$PNG_WEB_DIR = 'temp/';

include "qrlib.php";
//ofcourse we need rights to create temp dir
if (! file_exists ( $PNG_TEMP_DIR ))
	mkdir ( $PNG_TEMP_DIR );

$filename = $PNG_TEMP_DIR . 'test.png';
$filename1 = $PNG_TEMP_DIR . 'test_.png';
$data = 'Web Planex - QR Code Generator';

switch ($_REQUEST ['type']) {
	case 'TEXT' :
		$data = $_REQUEST ['text'];
		break;
	case 'URL' :
		$data = $_REQUEST['url'];
		break;
	case 'TEL' :
		$data = 'TEL:' . $_REQUEST ['tel-number'];
		break;
	case 'SMSTO' :
		$data = 'SMSTO:' . $_REQUEST ['smsto-number'] . ':' . $_REQUEST ['smsto-message'];
		break;
	case 'MAILTO' :
		$data = 'MAILTO:' . $_REQUEST ['mailto-email-address'];
		break;
	case 'MATMSG' :
		$data = 'MATMSG:TO:' . $_REQUEST ['matmsg-email-address'] . ';SUB:' . $_REQUEST ['matmsg-subject'] . ';BODY:' . $_REQUEST ['matmsg-body'] . ';;';
		break;
	case 'VCARD' :
		//Embed VCARD data in QR code as-is
	if (req ( 'vcard-type' ) == 'vcard') {
			$data = 'BEGIN:VCARD'."\n".
			'FN:'.$_REQUEST['vcard-first-name'].' '.$_REQUEST['vcard-last-name']."\n".
			'N:'.$_REQUEST['vcard-last-name'].';'.$_REQUEST['vcard-first-name']."\n".
			'TITLE:'.req('vcard-honorific')."\n".
			'TEL:'.req('vcard-tel-number')."\n".
			'TEL;WORK:'.req('vcard-tel-number')."\n".
			'TEL;FAX:'.req('vcard-fax-number')."\n".
			'EMAIL:'.req('vcard-email')."\n".
			'ORG:'.req('vcard-org')."\n".
			'ADR:;;'.req('vcard-street').';'.req('vcard-city').';'.req('vcard-state').';'.req('vcard-postcode').';'.req('vcard-country')."\n".
			'URL:'.req('vcard-url')."\n".
			'VERSION:2.1'."\n".
			'END:VCARD';
			;
		
		} else {
			$data='MEMCARD:N:'.req('vcard-last-name').','.
			req('vcard-first-name').';'.'TEL:'.
			req('vcard-tel-number').';EMAIL:'.
			req('vcard-email').';NOTE:'.req('vcard-org').';ADDR:,,'.req('vcard-street').','.
			req('vcard-city').','.
			req('vcard-state').','.
			req('vcard-postcode').','.
			req('vcard-country').';URL:'.req('vcard-url').';;';
		}
			
		//Link to VCF file for download
		//Still required to program that for download vcard from the client side.
		break;
	case 'VCALENDAR':
		
		$eventstartdate = str_replace(':','',str_replace('-','',req('vcalendar-start-date-time'))).'Z';
		$eventenddate = str_replace(':','',str_replace('-','',req('vcalendar-end-date-time'))).'Z';
		
		$data = 'BEGIN:VCALENDAR'."\n".
				'VERSION:2.0'."\n".
				'BEGIN:VEVENT'."\n".
				'SUMMARY:'.req('vcalendar-event-name')."\n".
				'LOCATION;VALUE=URL:http://maps.google.com/maps?q='.req('vcalendar-map-latitude').'%2C'.req('vcalendar-map-longitude').'%28'.req('vcalendar-map-address').'%29'."\n".
				'DTSTART:'.$eventstartdate."\n".
				'DTEND:'.$eventenddate."\n".
				'END:VEVENT'."\n".
				'BEGIN:VCALENDAR';
		break;
	case "MAPS":
		$data = 'http://maps.google.com/maps?q='.req('map_latitude').'%2C'.req('map_longitude');
		break;
	case "WIFI":
		$data = "WIFI:T:".req('wifi_network_type').";S:".req('wifi_ssid').';P:'.req('wifi_password').';;';
		break;
	case "PAYPAL_BUY":
		$data = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=".req('paypal-buy-account-email-address').'&item_number='.req('paypal-buy-item-product-code').'&item_name='.req('paypal-buy-item-description').'&amount='.req('paypal-buy-item-value').'&currency_code='.req('paypal-buy-currency-code');
		break;
	case "SOCIAL_NETWORK":
		$data = req('social_network_url');
		break;
	case "TWITTER_STATUS":
		$data = "http://twitter.com/home?status=".req('twstatus');
		break;
	case "LINKEDIN_SHARE":
		$data = "http://www.linkedin.com/shareArticle?mini=true&url=".req('linkedinshare');
		break;
	case "FOURSQUARE":
		$data = "http://foursquare.com/mobile/venue/".req('foursqureID');
		break;
	case "ITUNES_LINK":
		$data = req("itunes_link_url")."?partnerId=partnerId&siteID=siteId";	
		break;
	case "YOUTUBE":
		$video_id=req('youtube-video-id');
		if($video_id == ""){
			
			$parts = explode('=',req('youtube-video-url'));
			
			$video_id = $parts[1];
		}
		if(req('youtube-type')=='mobile')
		$data = "https://m.youtube.com/watch?v=".$video_id;
		else
		$data = "https://www.youtube.com/watch?v=".$video_id;
		break;
}

function req($param) {
	return $_REQUEST [$param];
}
//processing form input
//remember to sanitize user input in real-life solution !!!
$errorCorrectionLevel = 'H';
if (isset ( $_REQUEST ['level'] ) && in_array ( $_REQUEST ['level'], array ('L', 'M', 'Q', 'H' ) ))
	$errorCorrectionLevel = $_REQUEST ['level'];

$matrixPointSize = 10;

if (isset ( $_REQUEST ['block_size'] ))
	$matrixPointSize = min ( max ( ( int ) $_REQUEST ['block_size'], 1 ), 10 );
		
	// user data
	$filename_high_res = $PNG_TEMP_DIR . 'test' . md5 ( $_REQUEST ['data'] . '|' . $errorCorrectionLevel . '|22') . '_high_res.png';
	$filename = $PNG_TEMP_DIR . 'test' . md5 ( $_REQUEST ['data'] . '|' . $errorCorrectionLevel . '|' . $matrixPointSize ) . '.png';
	//@unlink($filename);

		
		QRcode::png ( $data, $filename, $errorCorrectionLevel, $matrixPointSize, $_REQUEST['border_size'], $_REQUEST['background_color'], $_REQUEST['foreground_color'],$_REQUEST["cb_top_left_ext_color"],$_REQUEST["cb_top_left_int_color"],$_REQUEST["cb_top_right_ext_color"],$_REQUEST["cb_top_right_int_color"],$_REQUEST["cb_bottom_left_ext_color"],$_REQUEST["cb_bottom_left_int_color"],$_REQUEST["block_style"]);
		QRcode::png ( $data, $filename_high_res, $errorCorrectionLevel, 22, $_REQUEST['border_size'], $_REQUEST['background_color'], $_REQUEST['foreground_color'],$_REQUEST["cb_top_left_ext_color"],$_REQUEST["cb_top_left_int_color"],$_REQUEST["cb_top_right_ext_color"],$_REQUEST["cb_top_right_int_color"],$_REQUEST["cb_bottom_left_ext_color"],$_REQUEST["cb_bottom_left_int_color"],$_REQUEST["block_style"]);
		
		
		if($_REQUEST["block_style"] == "rounded_blocks" || $_REQUEST["block_style"] == "circular_blocks")
		{
			$path = explode(";",$_SERVER["PATH"]);
			if($_REQUEST["block_style"] == "rounded_blocks")
			{
				/*$im = new Imagick($filename);
				$height = $im->getImageHeight();
				$width = $im->getImageWidth(); 
				//$im->resizeImage($width, $height, Imagick::FILTER_POINT, 0); 
				$im->medianFilterImage(1);
				$im->writeImage($filename);*/
				
				if($matrixPointSize>=5)
				{
					exec($path[0].'\convert '.$filename.' -morphology smooth disk:2.5 '.$filename); 	
				}
				
				exec($path[0].'\convert '.$filename_high_res.' -morphology smooth disk:4.3 '.$filename_high_res);
				
			}
			else
			{
				/*$im = new Imagick($filename);
				$height = $im->getImageHeight();
				$width = $im->getImageWidth(); 
				//$im->resizeImage($width, $height, Imagick::FILTER_POINT, 0); 
				$im->medianFilterImage(2);
				$im->writeImage($filename);*/
				if($matrixPointSize>9)
				{
					exec($path[0].'\convert '.$filename.' -morphology smooth disk:4.3 '.$filename); 	
				}
				else if($matrixPointSize>=5 && $matrixPointSize<=9)
				{
					exec($path[0].'\convert '.$filename.' -morphology smooth disk:2.3 '.$filename); 	
				}
				else if($matrixPointSize>=3 && $matrixPointSize<5)
				{
					exec($path[0].'\convert '.$filename.' -morphology smooth disk:1.3 '.$filename); 	
				}
				else
				{
					exec($path[0].'\convert '.$filename.' -morphology smooth disk:0.5 '.$filename); 	
				}
				
				exec($path[0].'\convert '.$filename_high_res.' -morphology smooth disk:6.3 '.$filename_high_res); 	
				
			}
		}
		
		$im = imagecreatefrompng ( $filename );
		$im_high_res = imagecreatefrompng ( $filename_high_res );
		
	if($_REQUEST['download']=='true1')
	{
		//print_r($_REQUEST); exit;
		$size = getimagesize($filename);
		$logofile = $_REQUEST['logo'];
		$file_str = pathinfo($logofile);
		$file_ext = $file_str['extension'];
		
		if($file_ext == 'png' || $file_ext == 'PNG'){
			$logo = imagecreatefrompng($logofile);
		}
		else{
			$logo = imagecreatefromjpeg($logofile);
		}
		
		$size_logo = getimagesize($logofile);
		imagecopy(
			$im, 
			$logo, 
			($size[0] - $size_logo[0])/2 , 
			($size[1] - $size_logo[1])/2, 
			0, 
			0, 
			$size_logo[0], 
			$size_logo[1]
		);
		
		imagepng ( $im , $filename1);
		
		//print_r($filename1); exit;
		imagedestroy ( $logo );
		if($_REQUEST['format'] == 'png')
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=code.png');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename1));
			ob_clean();
			flush();
			readfile($filename1);
			
			imagedestroy ( $im );
			exit;
		}
		if($_REQUEST['format'] == 'pdf')
		{
			$fname=basename($filename1);
			require_once('pdf/fpdf.php');
			$pdf=new FPDF();
			$pdf->AddPage();
			$pdf->Image('temp/'.$fname,10,8,100);
			//$pdf->Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='');
			$pdf->Output('code.pdf','D');

			ob_clean();
			flush();
			readfile($filename1);
			
			imagedestroy ( $im );
			exit;
		}
	}
	if($_REQUEST['download']=='true')
	{
		if (file_exists($filename)) 
		{
			if($_REQUEST["download_resolution"] =="low")
			{
				if($_REQUEST["download_as"] == "png")
				{
					$download_filename = 'code.png';
					$download_file_path = $filename;			
				}
				elseif($_REQUEST["download_as"] == "jpg")
				{
					$path = explode(";",$_SERVER["PATH"]);
					$download_filename = 'code.jpg';	
					$jpgfile = $PNG_TEMP_DIR."code_jpg.jpg";
					exec($path[0].'\convert '.$filename.' '.$jpgfile);
					$download_file_path = $jpgfile;
					
					/*$download_filename = 'code.jpg';
					$image = new Imagick();
					$image->readImage($filename);
					$filename = substr_replace($filename, '.jpg',strrpos($filename,"."));
					//$image->setImageFormat("jpg");
					$image->writeImage($filename);*/
					
					//$filename = imagecreatefromjpeg($filename);
				}
				elseif($_REQUEST["download_as"] == "tiff")
				{
					$path = explode(";",$_SERVER["PATH"]);
					$download_filename = 'code.tiff';	
					$tifffile = $PNG_TEMP_DIR."code_tiff.tiff";
					exec($path[0].'\convert '.$filename.' '.$tifffile);
					$download_file_path = $tifffile;
					/*try
					{
						$image = new Imagick();
						$image->readImage($filename);
						$filename = substr_replace($filename, '.tiff',strrpos($filename,"."));
						//$image->setImageFormat("tiff");
						$image->writeImage($filename);
					}
					catch(Exception $e)
					{
					  echo $e->getMessage();
					}*/
				}
				elseif($_REQUEST["download_as"] == "eps")
				{
					$path = explode(";",$_SERVER["PATH"]);
					$download_filename = 'code.eps';	
					$espfile = $PNG_TEMP_DIR."code_eps.eps";
					exec($path[0].'\convert '.$filename.' '.$espfile);
					$download_file_path = $espfile;
				}
			}
			else
			{
				if($_REQUEST["download_as"] == "png")
				{
					$download_filename = 'code_high_res.png';
					$download_file_path = $filename_high_res;			
				}
				elseif($_REQUEST["download_as"] == "jpg")
				{
					$path = explode(";",$_SERVER["PATH"]);
					$download_filename = 'code_high_res.jpg';	
					$jpgfile = $PNG_TEMP_DIR."code_jpg.jpg";
					exec($path[0].'\convert '.$filename_high_res.' '.$jpgfile);
					$download_file_path = $jpgfile;
				}
				elseif($_REQUEST["download_as"] == "tiff")
				{
					$path = explode(";",$_SERVER["PATH"]);
					$download_filename = 'code_high_res.tiff';	
					$tifffile = $PNG_TEMP_DIR."code_tiff.tiff";
					exec($path[0].'\convert '.$filename_high_res.' '.$tifffile);
					$download_file_path = $tifffile;
				}
				elseif($_REQUEST["download_as"] == "eps")
				{
					$path = explode(";",$_SERVER["PATH"]);
					$download_filename = 'code_high_res.eps';	
					$espfile = $PNG_TEMP_DIR."code_eps.eps";
					exec($path[0].'\convert '.$filename_high_res.' '.$espfile);
					$download_file_path = $espfile;
				}
			}
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$download_filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($download_file_path));
			ob_clean();
			flush();
			readfile($download_file_path);
			@unlink($jpgfile);
			@unlink($tifffile);
			@unlink($epsfile);
		
			exit;
		}
	}
	header ( 'Content-type: image/png' );
	imagepng ( $im );
	imagedestroy ( $im );
	


//display generated file


//config form
/*echo '<form action="code.php" method="post">
        Data:&nbsp;<input name="data" value="' . (isset ( $_REQUEST ['data'] ) ? htmlspecialchars ( $_REQUEST ['data'] ) : 'PHP QR Code :)') . '" />&nbsp;
        ECC:&nbsp;<select name="level">
            <option value="L"' . (($errorCorrectionLevel == 'L') ? ' selected' : '') . '>L - smallest</option>
            <option value="M"' . (($errorCorrectionLevel == 'M') ? ' selected' : '') . '>M</option>
            <option value="Q"' . (($errorCorrectionLevel == 'Q') ? ' selected' : '') . '>Q</option>
            <option value="H"' . (($errorCorrectionLevel == 'H') ? ' selected' : '') . '>H - best</option>
        </select>&nbsp;
        Size:&nbsp;<select name="size">';

for($i = 1; $i <= 10; $i ++)
	echo '<option value="' . $i . '"' . (($matrixPointSize == $i) ? ' selected' : '') . '>' . $i . '</option>';

echo '</select>&nbsp;
        <input type="submit" value="GENERATE"></form>';
*/

    