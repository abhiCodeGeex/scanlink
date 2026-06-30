 {
    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],
    "DATA":[
    <?php if(isset($_REQUEST['pid']))
{  
include_once("db.php");  
$itemid=$_REQUEST['pid'];
$q=mysqli_query($con,"SELECT DISTINCT id_platform from ana_item_analytics where id_item='".$itemid."' order by id desc");
//echo $q;
//$r=mysqli_query($con,"SELECT * from ana_item_analytics where id_item=".$itemid." order by id desc");

$r=mysqli_num_rows(mysqli_query($con,"SELECT * from ana_item_analytics where id_item=".$itemid." and id_platform='1' order by id desc"));
$s=mysqli_num_rows(mysqli_query($con,"SELECT * from ana_item_analytics where id_item=".$itemid." and id_platform='2' order by id desc"));
$t=mysqli_num_rows(mysqli_query($con,"SELECT * from ana_item_analytics where id_item=".$itemid." and id_platform='3' order by id desc"));

//echo $r;
$i=1;
//$devicshit=mysqli_num_rows($r);
//echo $arraylength;
$arraylength=mysqli_num_rows($q);
while ($row=mysqli_fetch_array($q)){
	
	$fetchdevicsname=mysqli_query($con,"SELECT * from ana_platforms where id='".$row["id_platform"]."'");

$state=mysqli_query($con,"SELECT DISTINCT id_device from ana_item_analytics where id_item=".$itemid." and id_platform='".$row["id_platform"]."' order by id desc");
				$j=1;
$arraylengthstate=mysqli_num_rows($state);
$rowdivcs=mysqli_fetch_array($fetchdevicsname);
$devicshitp=mysqli_num_rows($fetchdevicsname);

 ?>
		["Devics Statistics","<?php echo $rowdivcs["platform_name"];  ?>",<?php if($row["id_platform"]=='1'){echo $r;} else if($row["id_platform"]=='2'){ echo $s;}else if($row["id_platform"]=='3'){echo $t; } ?>,"<?php echo $rowdivcs["platform_name"];  ?> Platfrom ^ ",
			{
			    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],
			    "DATA":[
                
                <?php 
while ($rowstate=mysqli_fetch_array($state)){
	
$fetchdevicsplatform=mysqli_query($con,"SELECT * from ana_devices where id='".$rowstate["id_device"]."'");	
$city=mysqli_query($con,"SELECT DISTINCT id_browser from ana_item_analytics where id_item='".$itemid."' and id_platform='".$row["id_platform"]."' and id_device='".$rowstate["id_device"]."' order by id desc");
				$k=1;
$arraylengthcity=mysqli_num_rows($city);
$rowplatfrom=mysqli_fetch_array($fetchdevicsplatform);
$arrdevics=mysqli_num_rows($fetchdevicsplatform);

$a=mysqli_num_rows(mysqli_query($con,"SELECT * from ana_item_analytics where id_item=".$itemid." and id_device='".$rowstate["id_device"]."' order by id desc"));


 ?>
				["Platfrom Statistics","<?php echo $rowplatfrom["device_name"] ?>",<?php echo $a; ?>,"<?php echo $rowplatfrom["device_name"] ?> ^ ",
					{
					    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],
					    "DATA":[
                        
                        <?php 
while ($rowcity=mysqli_fetch_array($city)){ 
if (is_numeric($rowcity["id_browser"]))
{
$fetchdevicbrowser=mysqli_query($con,"SELECT * from ana_browsers where id='".$rowcity["id_browser"]."'");
}
else
{
	$fetchdevicbrowser=mysqli_query($con,"SELECT * from ana_item_analytics where id_item='".$itemid."'");

}
$rowbrowser=mysqli_fetch_array($fetchdevicbrowser);
$b=mysqli_num_rows(mysqli_query($con,"SELECT * from ana_item_analytics where id_item=".$itemid." and id_device='".$rowstate["id_device"]."' and id_browser='".$rowcity["id_browser"]."' order by id desc"));

?>
						["Devics Browser Statistics","<?php if (is_numeric($rowcity["id_browser"]))
{echo $rowbrowser["match_word"];} else {echo $rowcity["id_browser"]; } ?>",<?php echo $b;?>,"<?php if (is_numeric($rowcity["id_browser"]))
{echo $rowbrowser["match_word"];} else {echo $rowcity["id_browser"]; } ?>  ^ "]
                        <?php  if($arraylengthcity==$k) {} else {echo ",";} $k++; }?>
					    ]
					}
				]
               <?php if($arraylengthstate==$j) {} else {echo ",";} $j++; } ?> 
                
                
                
                
			    ]
			}		
		]
        
        <?php if($arraylength==$i) {} else {echo ",";} $i++; }   } else {} ?>
	
    ]
}
