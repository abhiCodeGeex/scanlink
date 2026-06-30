<style type="text/css">

.main-row {
    float: left;
    padding: 3px 0;
    width: 100%;
}
.main-row .row {
    color: #545454;
    float: left;
    font-family: Arial,Helvetica,sans-serif;
    font-size: 14px;
    margin: 0 5px 0 0;
    padding: 5px;
    width: 150px;
}
.main-row .row-r {
    float: left;
    margin: 0;
    padding: 3px;
    width: 380px;
}
select{ padding:7px 5px; margin:0; background:#fff;-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;webkit-box-shadow: inset 0 1px 3px -1px #666; -moz-box-shadow: inset 0 1px 3px -1px #666; box-shadow: inset 0 1px 3px -1px #666; border:1px solid #c6c6c6; width:90%; font-size:12px;}
.black-bt{
 line-height:25px;
 background-image: url(../images/black-bg.jpg)!important;
 background-repeat: repeat-x;
 background-position: center top;
 border: 1px solid #7a7a7a;
 -webkit-border-radius: 6px;
 -moz-border-radius: 6px;
 border-radius: 6px;
 float:left;
 padding-right: 10px;
 padding-left: 10px;
 font-size: 11px;
 font-weight: bold;
 color: #FFF;
 text-decoration: none;
 font-family: Verdana, Geneva, sans-serif;
 width:100px;
 height:26px;
}
</style>
<form action="<?php echo URL::base().'product/add';?>" onsubmit="callForm(this);" target="_top" method="post">
<div class="main-row">
<div class="row">Select Profile Type</div>
<div class="row-r">
<select name="etypes" id="etypes">
<?php
foreach($types as $key=>$type){	
?>
<option value="<?php echo $type['id'];?>"><?php echo $type['name'];?></option>
<?php }?>
</select>
</div>
</div>
<div class="main-row">
<div class="row">Select Client</div>
<div class="row-r">
<select name="clientid" id="clientid">
<?php
foreach($clients as $key=>$client){
?>
<option value="<?php echo $client['id'];?>"><?php echo $client['email'];?></option>
<?php }?>
</select>
</div>
</div>
<div class="main-row">
<input type="submit" name="btnsubmit" id="btnsubmit" class="black-bt" value="Add Profile" />
</div>
</form>
<script type="text/javascript">
function callForm(obj){
//	obj.submit();
	//parent.$.fn.colorbox.close();
}
</script>