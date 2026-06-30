<style type="text/css">#product_display td { font-size:12px; vertical-align:top; }
#product_display{ border:1px solid black; margin-top:10px; margin-bottom:10px; }
#product_display td{ border-bottom: 1px solid #CCC; }

</style>
<br />
<br />
<br />
<table border="0" cellpadding="5" cellspacing="1" align="center" width="80%" id="product_display">

    <tr>
    	<td width="200">Title:</td>
        <td>
					<?php echo $post[0]["title"]; ?>
				</td>
    </tr>

	<tr>
    	<td>Video:</td>
        <td>
					<?php echo $post[0]["video"]; ?>
				</td>
    </tr>
</table>