<div class="title">
  <p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */ ?></p>
</div>
<div class="content">
<div class="simple-rounded-box fl">
  <div class="nav-menu"><?php echo $innermenu;?> </div>
	<?php echo Form::open(URL::base().'customqr/index', array('id'=>'frmcustomqr','name'=>'frmcustomqr','method'=>'post','enctype' => 'multipart/form-data')) ?>
    <div class="data2">
        <div class="penal-tab">
          <div class="TabbedPanels">
            <div class="TabbedPanelsContentGroup">
              <div class="TabbedPanelsContent">
                <div class="fild-outer">                  
                  <div class="main-row">
                    <div class="row">Url</div>
                    <div class="row-r">
                      <?php echo Form::input('txturl','', array('id'=>'txturl', 'class'=>'text-fi')) ?>
					<?php echo Form::label('txturl', isset($errors['txturl']) ? $errors['txturl']:'', array('class'=>'error','generated'=>'true')) ?>
                    </div>
                  </div>
                </div>                
                <div class="clear"></div>
              </div>
            </div>
          </div>
        </div>
				<div>&nbsp;</div>
        <div class="penal-qr">
          <div style="width: 100px;"><input name="save" id="save" type="submit" class="save" value="Save"></div>
          <div class="qr-bg" style="position:relative"><img alt="" src="<?php echo url::base(); ?>images/loading.gif" class="img-loader" style="position:absolute;left:140px;top:220px;display:none"><img alt="" src="" class="img-qr"></div>
          <!--<div class="btn"> <a href="#">DOWNLOAD PDF</a></div>-->
        </div>
      </div>
	<?php echo Form::close() ?>
  </div>
  </div>
<div id="progressbar">
<img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile...
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#txturl').keyup(function(){
		$('.qr-bg .img-loader').show();
		$.ajax({
			type:'post',
			url:'realtimeqr',
			data:{qrtext:$(this).val()},
			success:function(res){
				$('.qr-bg .img-loader').hide();
				$('.qr-bg .img-qr').attr('src','<?php echo URL::base();?>'+res);
			}
		});
	});
	$("#frmcustomqr").validate({
		rules: {
			txturl: "required"
		},
		errorElement: "label",
		messages: {
			txturl: "Please provide url for CustomQR"
		},
		submitHandler: function(form) {
			document.getElementById("progressbar").style.display = "block";
			form.submit();
			return true;
		}
	});
});
</script>