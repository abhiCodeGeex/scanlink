<?php
if (isset($pending_codes) && $pending_codes == 0){
?>
<div style='display:none;'>
  <div id='inline_content' style='padding:10px; background:#fff;'>
    <p>There are no codes available in your account .
      Please click the button below to add more codes to your account.</p>
    <div style="float:right;width:98px;overflow:hidden;">
      <input type="submit" onclick="javascript: window.location.href='<?php echo URL::base(); ?>profile/purchase'" value="Purchase Codes" />
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $(".inline").colorbox({inline:true, width:"25%"});
  });
</script>
<?php } ?>