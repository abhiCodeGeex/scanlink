 {
    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],
    "DATA":[
    <?php if(isset($_REQUEST['pid']))
{  
include_once("db.php");  
$itemid=$_REQUEST['pid'];
$q=mysqli_query($con,"SELECT DISTINCT country_name from ana_item_analytics where id_item='".$itemid."' order by id desc");
$countcountry=mysqli_query($con,"SELECT * from ana_item_analytics where id_item='".$itemid."' order by id desc");
$counthit=mysqli_num_rows($countcountry);
//echo $q;
//$r=mysqli_query($con,"SELECT * from ana_item_analytics where id_item='".$itemid."' order by id desc");

$i=1;
$arraylength=mysqli_num_rows($q);
//echo $arraylength;
//$noofcase=mysqli_num_rows($r);
while ($row=mysqli_fetch_array($q)){
$state=mysqli_query($con,"SELECT DISTINCT region_name from ana_item_analytics where id_item='".$itemid."' and country_name='".$row["country_name"]."' order by id desc");
				$j=1;
$arraylengthstate=mysqli_num_rows($state);
$r=mysqli_num_rows(mysqli_query($con,"SELECT country_name from ana_item_analytics where id_item='".$itemid."' and country_name='".$row["country_name"]."' order by id desc"));

 ?>
		["Country Statistics","<?php echo $row["country_name"];  ?>",<?php echo $r; ?>,"<?php echo $row["country_name"];  ?> ^ ",
			{
			    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],
			    "DATA":[
                
                <?php 
while ($rowstate=mysqli_fetch_array($state)){
$state2=mysqli_query($con,"SELECT * from ana_item_analytics where id_item='".$itemid."' and country_name='".$row["country_name"]."' and region_name='".$rowstate["region_name"]."'  order by id desc");
$statehit=mysqli_num_rows($state2);

$city=mysqli_query($con,"SELECT DISTINCT city_name from ana_item_analytics where id_item='".$itemid."' and country_name='".$row["country_name"]."' and region_name='".$rowstate["region_name"]."' order by id desc");

				$k=1;
$arraylengthcity=mysqli_num_rows($city);
 ?>
				["State Statistics","<?php echo $rowstate["region_name"] ?>",<?php echo $statehit; ?>,"<?php echo $rowstate["region_name"] ?> ^ ",
					{
					    "COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],
					    "DATA":[
                        
                        <?php 
while ($rowcity=mysqli_fetch_array($city)){ 

$city2=mysqli_query($con,"SELECT * from ana_item_analytics where id_item='".$itemid."' and country_name='".$row["country_name"]."' and region_name='".$rowstate["region_name"]."' and city_name='".$rowcity["city_name"]."' order by id desc");

$cityhit=mysqli_num_rows($city2);

?>
						["City Statistics","<?php echo $rowcity["city_name"] ?>",<?php echo $cityhit;?>,"<?php echo $rowcity["city_name"] ?>  ^ "]
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
