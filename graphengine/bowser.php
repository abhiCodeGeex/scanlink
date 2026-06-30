 {
    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],
    "DATA":[
    <?php if(isset($_REQUEST['pid']))
{  
include_once("db.php");  
$itemid=$_REQUEST['pid'];
$q=mysqli_query($con,"SELECT DISTINCT id_browser from ana_item_analytics where id_item='".$itemid."' order by id desc");
//echo $q;

$i=1;
$arraylength=mysqli_num_rows($q);
//echo $arraylength;
//$noofcase=mysqli_num_rows($r);
while ($row=mysqli_fetch_array($q)){
$state=mysqli_query($con,"SELECT DISTINCT id_platform from ana_item_analytics where  id_item='".$itemid."' and id_browser='".$row["id_browser"]."' order by id desc");
				$j=1;
$arraylengthstate=mysqli_num_rows($state);
if (is_numeric($row["id_browser"]))
{
$fetchdevicbrowser=mysqli_query($con,"SELECT * from ana_browsers where id='".$row["id_browser"]."'");
}
else
{
	$fetchdevicbrowser=mysqli_query($con,"SELECT * from ana_item_analytics where id_item='".$itemid."'");

}
$rowbrowser=mysqli_fetch_array($fetchdevicbrowser);
$r=mysqli_num_rows(mysqli_query($con,"SELECT id_browser from ana_item_analytics where id_item='".$itemid."' and id_browser='".$row["id_browser"]."' order by id desc"));

 ?>
		["Browser Statistics","<?php if (is_numeric($row["id_browser"]))
{echo $rowbrowser["browser_name"]; } else {echo $row["id_browser"]; } ?>",<?php echo $r; ?>,"<?php echo $rowbrowser["browser_name"] ?> ^ ",
			{
			    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],
			    "DATA":[
                
                <?php 
while ($rowstate=mysqli_fetch_array($state)){
$city=mysqli_query($con,"SELECT DISTINCT browser_version from ana_item_analytics where id_item='".$itemid."' and id_browser='".$row["id_browser"]."' and id_platform='".$rowstate["id_platform"]."' order by id desc");
				$k=1;
$arraylengthcity=mysqli_num_rows($city);
$fetchdevicsplatform=mysqli_query($con,"SELECT * from ana_platforms where id='".$rowstate["id_platform"]."'");	
$rowplatfrom=mysqli_fetch_array($fetchdevicsplatform);
$s=mysqli_num_rows(mysqli_query($con,"SELECT id_browser from ana_item_analytics where id_item='".$itemid."' and id_browser='".$row["id_browser"]."' and id_platform='".$rowstate["id_platform"]."' order by id desc"));
 ?>
				["Devics Statistics","<?php echo $rowplatfrom["platform_name"] ?>",<?php echo $s; ?>,"<?php echo $rowplatfrom["platform_name"] ?> ^ ",
					{
					    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],
					    "DATA":[
                        
                        <?php 
while ($rowcity=mysqli_fetch_array($city)){
	$s=mysqli_num_rows(mysqli_query($con,"SELECT id_browser from ana_item_analytics where id_item='".$itemid."' and id_browser='".$row["id_browser"]."' and id_platform='".$rowstate["id_platform"]."' and browser_version='".$rowcity["browser_version"]."'  order by id desc"));
	
	 ?>
						[" Browser Version Statistics","<?php echo $rowbrowser["match_word"] ?><?php echo $rowcity["browser_version"] ?>",<?php echo $s;?>,"<?php echo $rowbrowser["match_word"] ?><?php echo $rowcity["browser_version"] ?>  ^ "]
                        <?php  if($arraylengthcity==$k) {} else {echo ",";} $k++; }?>
					    ]
					}
				]
               <?php if($arraylengthstate==$j) {} else {echo ",";} $j++; } ?> 
                
                
                
                
			    ]
			}		
		]
        
        <?php if($arraylength==$i || $arraylength==1) {} else {echo ",";} $i++; }   } else {} ?>
	
    ]
}
