<?php header("Access-Control-Allow-Origin: *");
 $con = mysqli_connect("galadb1.croyirckzbgv.ap-southeast-2.rds.amazonaws.com","scanlink","RN4AS4a82h7P92uR","scanlink") or die ("could not connect database");
 mysqli_set_charset($con, 'utf8mb4'); 
?>