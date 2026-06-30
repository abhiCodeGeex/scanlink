<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#CCC;">
  <tbody>
    <tr>
      <td style="padding:10px 0px;"><a href="<?php echo URL::base();?>dashboard/index">&copy; copyright ScanLink.</a></td>
    </tr>
  </tbody>
</table>
<?php 
if($scripts):
	foreach ($scripts as $javascript):
		echo html::script($javascript, NULL, FALSE)."\t\t";
	endforeach;
endif;
?>