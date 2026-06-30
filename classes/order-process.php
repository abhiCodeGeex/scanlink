<?php
include("connect.php");
require_once('phpmailer/class.phpmailer.php');
if($_POST['HidVal'] == '0')
{
	//print_r($_REQUEST); exit;
	if(isset($_REQUEST['mode']))
		$mode = $_REQUEST['mode'];
	else
		$mode = "";
	
	switch($mode)
	{
		case "add":
			session_unregister('cart');
			$i = count($_SESSION['cart']['id']);
			$_SESSION['SCItem']=$i;
			if($i == 0 || $i == "")
			{
				$i = 0;
				$LID = GetLastID('qr_codes','id','LastID')+1;
				$QRImage = $LID.".png";
				$response = shortenUrl($_REQUEST['url']);
				$ShortURL = $response['id'];
				$URL = "https://chart.googleapis.com/chart?cht=qr&chd=test&chs=200x200&chl=".$ShortURL."&chld=M";
				$QRCode = file_get_contents($URL);
				
				$file = fopen("QRimages/".$QRImage,"w");
				fwrite($file,$QRCode);
				fclose($file);
				$insQry = "INSERT INTO `qr_codes` SET `UserID` = '".$_SESSION['uSeRiD']."', `CampaignName` = '".stripslashes($_REQUEST['txtCName'])."', `QRCode` = '".$QRImage."', `ShortURL` = '".$ShortURL."', `LongURL` = '".stripslashes($_REQUEST['url'])."', `CreateDate` = '".date('Y-m-d')."', `Payment` = 'N'";
				$resQry = mysql_query($insQry) or die(mysql_error());
				$LastID = mysql_insert_id();
				$_SESSION['cart']['qty'][$i] = '1';
				$_SESSION['cart']['id'][$i] = $LastID;
			}
			else
			{
				$flag = 0;
				for($i=0;$i < count($_SESSION['cart']['id']);$i++)
				{
					if($_SESSION['cart']['id'][$i] == $_REQUEST['pro_id'])
					{
						$_SESSION['cart']['qty'][$i]=$_SESSION['cart']['qty'][$i] + $_REQUEST['quantity'];
						$flag = 1;
						break;
					}
				}
				if($flag == 0 )
				{
					$j = count($_SESSION['cart']['id']);
					$LID = GetLastID('qr_codes','id','LastID')+1;
					$QRImage = $LID.".png";
					$response = shortenUrl($_REQUEST['url']);
					$ShortURL = $response['id'];
					$URL = "https://chart.googleapis.com/chart?cht=qr&chd=test&chs=200x200&chl=".$ShortURL."&chld=M";
					$QRCode = file_get_contents($URL);
					
					$file = fopen("QRimages/".$QRImage,"w");
					fwrite($file,$QRCode);
					fclose($file);
					$insQry = "INSERT INTO `qr_codes` SET `UserID` = '".$_SESSION['uSeRiD']."', `CampaignName` = '".stripslashes($_REQUEST['txtCName'])."', `QRCode` = '".$QRImage."', `ShortURL` = '".$ShortURL."', `LongURL` = '".stripslashes($_REQUEST['url'])."', `CreateDate` = '".date('Y-m-d')."', `Payment` = 'N'";
					$resQry = mysql_query($insQry) or die(mysql_error());
					$LastID = mysql_insert_id();
					$_SESSION['cart']['qty'][$j] = '1';
					$_SESSION['cart']['id'][$j]  = $LastID;
				}
			}
		header("location:qr-payment.php");
		exit();
		break;
			
		case "delete":
			$j = count($_SESSION['cart']['id']);
			$_SESSION['SCItem']=$j;
			  
			$temp['cart']['id'] = "";
			$temp['cart']['qty'] = "";
			$j=0;
			for($i=0;$i < count($_SESSION['cart']['id']);$i++)
			{
				if($_SESSION['cart']['id'][$i] == $_REQUEST['pro_id']){}
				else
				{
					$temp['cart']['id'][$j] = $_SESSION['cart']['id'][$i];
					$temp['cart']['qty'][$j] = $_SESSION['cart']['qty'][$i];
					$j++;
				}
			}
			$_SESSION['cart']['id'] = "";
			$_SESSION['cart']['qty'] = "";
			$_SESSION['cart']['id'] = $temp['cart']['id'];
			$_SESSION['cart']['qty'] = $temp['cart']['qty'];
			if(count($_SESSION['cart']['id']) == 1 && $_SESSION['cart']['id'][0] == "")
			{
				session_unregister("cart");
				header("Location:order-list.php");
				/*header("Location:cart_db.php");
				exit;*/
			}
			else
			{
				header("Location:order-list.php");
				exit;
			}
		break;
	
	
	default:
		 break;
	}
}
?>
