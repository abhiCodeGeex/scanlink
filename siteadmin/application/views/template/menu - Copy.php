<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#CCC;">
  <tr height="25px" bgcolor="#33FFFF">
    <td align="left" valign="middle" bgcolor="#EAEAEA" colspan="2">
	<?php
	$Product=array(
	array("Manage Product","products/list","",0,20,190),
	);
	
	$Order=array(
	array("Manage Order","orders/list","",0,20,170),
	);
	
	$Clients=array(
	array("Add User","clients/add","",0,20,190),
	array("Manage User","clients/list","",0,20,190),
	);
	
	$Users=array(
	array("Add User","users/add","",0,20,190),
	array("Manage User","users/list","",0,20,190),
	);
		
	$website = array (
	array("Client","clients/list","",count($Clients),20,190,$Clients),
	array("User","users/list","",count($Users),20,190,$Users),
	array("Product","products/list","",count($Product),20,190,$Product),
	array("Order","orders/list","",count($Order),20,190,$Order),

	);
	
	$users= array(
	//array("Users","manage_registration.php","",0,20,190),
	array("Change Password","password/change","",0,20,190),
	);
	
	////////////// HEADING OF MAIN SECTIONS ARE STORED IN THIS ARRAY==>This Array Must Come Last//////////////////////
	$HeadLinksArray = array (
	array("Admin Home",0,20,190,"","dashboard/list"), //First Array Only For Top Javascript Menu
	array("Website",count($website),20,190,$website),	
	array("Users",count($users),15,190,$users)
	);
?>
	<script type="text/javascript" language="javascript">	
	
	var NoOffFirstLineMenus=3;	//Total No. Of Main Sections Specify Here Too
	var LowBgColor='#999999';		
	var LowSubBgColor='#CCCCCC';		
	var HighBgColor='#CCCCCC';		
	var HighSubBgColor='#CCCCCC';	
	var FontLowColor='#FFFFFF';		
	var FontSubLowColor='#333333';	
	var FontHighColor='Black';		
	var FontSubHighColor='White';	
	var BorderColor='#333333';		
	var BorderSubColor='#333333';	
	var BorderWidth=1;				
	var BorderBtwnElmnts=0;			
	var FontFamily="Verdana,Arial"	
	var FontSize=9;					
	var FontBold=1;					
	var FontItalic=0;				
	var MenuTextCentered='center';
	var MenuCentered='center';
	var MenuVerticalCentered='top';
	var ChildOverlap=.0;
	var ChildVerticalOverlap=.0;
	var StartTop=199;
	var StartLeft=0;
	var VerCorrect=0;
	var HorCorrect=0;
	var LeftPaddng=3;
	var TopPaddng=3;
	var FirstLineHorizontal=1;
	var MenuFramesVertical=0;
	var DissapearDelay=75;
	var TakeOverBgColor=0;
	var FirstLineFrame='navig';
	var SecLineFrame='navig';
	var DocTargetFrame='navig';
	var TargetLoc='';
	var HideTop=0;
	var MenuWrap=0;
	var RightToLeft=0;
	var UnfoldsOnClick=0;
	var WebMasterCheck=0;
	var ShowArrow=0;
	var KeepHilite=1;
	var Arrws=['../images/tri.gif',3,10,'../images/tridown.gif',6,4,'../images/trileft.gif',5,10];	// Arrow source, width and height
	
function BeforeStart(){return}
function AfterBuild(){return}
function BeforeFirstOpen(){return}
function AfterCloseAll(){return}
<?
	for($LinkCount=1;$LinkCount<=count($HeadLinksArray);$LinkCount++)
	{
		////////// FOR MAIN MENU (IE) 1ST LEVEL OF MENU (HEADING)/////////////////////
		echo 'Menu'.$LinkCount.' = new Array("'.$HeadLinksArray[$LinkCount-1][0].'","'.$HeadLinksArray[$LinkCount-1][4].'","",'.$HeadLinksArray[$LinkCount-1][1].','.$HeadLinksArray[$LinkCount-1][2].','.$HeadLinksArray[$LinkCount-1][3].');';
			
		$TempChildAry = $HeadLinksArray[$LinkCount-1][4];
		if(is_array($TempChildAry))
		{
			for($LinkCount2=1;$LinkCount2<=count($TempChildAry);$LinkCount2++)
			{
				////////// FOR SUB OF THE MENU (IE) 2ND LEVEL OF MENU/////////////////////
				echo "\n Menu".$LinkCount."_".$LinkCount2." = new Array('".$TempChildAry[$LinkCount2-1][0]."','".$TempChildAry[$LinkCount2-1][1]."','".$TempChildAry[$LinkCount2-1][2]."',".$TempChildAry[$LinkCount2-1][3].",".$TempChildAry[$LinkCount2-1][4].",".$TempChildAry[$LinkCount2-1][5].");";
				
				$TempChildAry2 = $TempChildAry[$LinkCount2-1][5];
				
				if(is_array($TempChildAry2))
				{
					////////// FOR SUB - SUB OF THE MENU (IE) 3RD LEVEL OF MENU///////////////////// 							
					for($LinkCount3=1;$LinkCount3<=count($TempChildAry2);$LinkCount3++)
					{
							echo "\n Menu".$LinkCount."_".$LinkCount2."_".$LinkCount3." = new Array('".$TempChildAry2[$LinkCount3-1][0]."','".$TempChildAry2[$LinkCount3-1][1]."','".$TempChildAry2[$LinkCount3-1][2]."',".$TempChildAry2[$LinkCount3-1][3].",".$TempChildAry2[$LinkCount3-1][4].",".$TempChildAry2[$LinkCount3-1][5].");";
					}
				}
			}
		}
	}
?>
</script><script type="text/javascript" src="http://localhost/codesafe/js/menu_com.js"></script></td>
  </tr>
</table>