<div class="title">
  <h1>Lorem ipsum dolor sit amet</h1>
</div>
<div class="content">
  <div class="nav-menu"><?php echo $innermenu;?> </div>
  <div class="penal">
    <div class="penal-tab">
      <div id="TabbedPanels1" class="TabbedPanels">
        <ul class="TabbedPanelsTabGroup">
          <li class="TabbedPanelsTab" tabindex="0">Plant & Equipment</li>
          <li class="TabbedPanelsTab" tabindex="0">Facility Asset</li>
          <li class="TabbedPanelsTab" tabindex="0">Location</li>
          <li class="TabbedPanelsTab" tabindex="0">Product</li>
        </ul>
        <div class="TabbedPanelsContentGroup">
          <div class="TabbedPanelsContent"> <?php echo Form::open(URL::base().'plant/add', array('id'=>'frmplant','name'=>'frmplant','method'=>'post','enctype' => 'multipart/form-data')) ?>
            <div class="fild-outer">
              <div class="main-row">
                <div class="row">Upload Company logo</div>
                <div class="row-r"> <?php echo Form::input('logo_file','', array('id'=>'logo_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Make / Model</div>
                <div class="row-r"> <?php echo Form::input('txtname','', array('id'=>'txtname')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Identification</div>
                <div class="row-r"> <?php echo Form::input('txtidenty','', array('id'=>'txtidenty')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Serial No.</div>
                <div class="row-r"> <?php echo Form::input('txtsrno','', array('id'=>'txtsrno')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Description</div>
                <div class="row-r"> <?php echo Form::input('txtdesc','', array('id'=>'txtdesc')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Notes</div>
                <div class="row-r"> <?php echo Form::input('txtnotes','', array('id'=>'txtnotes')) ?> </div>
              </div>
              <div class="main-row padbot">
                <div class="row padbot padtop2">Key contacts</div>
                <div class="row-r padbot"> <?php echo Form::label('comp', 'NAME / COMPANY') ?> <?php echo Form::label('tele', 'TELEPHONE',array('class'=>'pad')) ?> </div>
              </div>
              <div class="main-row padtop">
                <div class="row padtop">&nbsp;</div>
                <div class="row-r padtop"> <?php echo Form::input('txtcompname','', array('id'=>'txtcompname','class'=>'input-w')) ?> <?php echo Form::input('txttelephone','', array('id'=>'txttelephone','class'=>'input-w')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a picture</div>
                <div class="row-r"> <?php echo Form::input('picture_file','', array('id'=>'picture_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row" id="docu">
                <div class="row">Upload a Document</div>
                <div class="row-r"> <?php echo Form::input('docu_file','', array('id'=>'docu_file','type'=>'file')) ?>
                  <?php /*?><img src="<?php echo URL::base();?>images/addmore.png" id="addmore" style="cursor:pointer;" />	<?php */?>
                  <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a Video</div>
                <div class="row-r"> <?php echo Form::input('video_file','', array('id'=>'video_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
            </div>
            <?php echo Form::close() ?>
            <div class="clear"></div>
          </div>
          <div class="TabbedPanelsContent"> <?php echo Form::open(URL::base().'asset/add', array('id'=>'frmasset','name'=>'frmasset','method'=>'post','enctype' => 'multipart/form-data')) ?>
            <div class="fild-outer">
              <div class="main-row">
                <div class="row">Upload Company logo</div>
                <div class="row-r"> <?php echo Form::input('logo_file','', array('id'=>'logo_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Facility name</div>
                <div class="row-r"> <?php echo Form::input('txtname','', array('id'=>'txtname')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Address</div>
                <div class="row-r"><a href="#" class="view-map">View Map</a> </div>
              </div>
              <div class="main-row">
                <div class="row">GPS coordinates</div>
                <div class="row-r"> <?php echo Form::input('txtgpscoord','', array('id'=>'txtgpscoord')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Identification</div>
                <div class="row-r"> <?php echo Form::input('txtidenty','', array('id'=>'txtidenty')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Description</div>
                <div class="row-r"> <?php echo Form::input('txtdesc','', array('id'=>'txtdesc')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Notes</div>
                <div class="row-r"> <?php echo Form::input('txtnotes','', array('id'=>'txtnotes')) ?> </div>
              </div>
              <div class="main-row padbot">
                <div class="row padbot padtop2">Key contacts</div>
                <div class="row-r padbot"> <?php echo Form::label('comp', 'NAME / COMPANY') ?> <?php echo Form::label('tele', 'TELEPHONE',array('class'=>'pad')) ?> </div>
              </div>
              <div class="main-row padtop">
                <div class="row padtop">&nbsp;</div>
                <div class="row-r padtop"> <?php echo Form::input('txtcompname','', array('id'=>'txtcompname','class'=>'input-w')) ?> <?php echo Form::input('txttelephone','', array('id'=>'txttelephone','class'=>'input-w')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a picture</div>
                <div class="row-r"> <?php echo Form::input('picture_file','', array('id'=>'picture_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a Document</div>
                <div class="row-r"> <?php echo Form::input('docu_file','', array('id'=>'docu_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a Video</div>
                <div class="row-r"> <?php echo Form::input('video_file','', array('id'=>'video_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
            </div>
            <?php echo Form::close() ?>
            <div class="clear"></div>
          </div>
          <div class="TabbedPanelsContent"> <?php echo Form::open(URL::base().'location/add', array('id'=>'frmlocation','name'=>'frmlocation','method'=>'post','enctype' => 'multipart/form-data')) ?>
            <div class="fild-outer">
              <div class="main-row">
                <div class="row">Upload Company logo</div>
                <div class="row-r"> <?php echo Form::input('logo_file','', array('id'=>'logo_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Location name</div>
                <div class="row-r"> <?php echo Form::input('txtname','', array('id'=>'txtname')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Address</div>
                <div class="row-r"><a href="#" class="view-map">View Map</a> </div>
              </div>
              <div class="main-row">
                <div class="row">GPS coordinates</div>
                <div class="row-r"> <?php echo Form::input('txtgpscoord','', array('id'=>'txtgpscoord')) ?> </div>
              </div>
              <?php /*?><div class="main-row">
                    <div class="row">Identification</div>
                    <div class="row-r">
                      <?php echo Form::input('txtidenty','', array('id'=>'txtidenty')) ?>
                    </div>
                  </div><?php */?>
              <div class="main-row">
                <div class="row">Description</div>
                <div class="row-r"> <?php echo Form::input('txtdesc','', array('id'=>'txtdesc')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Notes</div>
                <div class="row-r"> <?php echo Form::input('txtnotes','', array('id'=>'txtnotes')) ?> </div>
              </div>
              <div class="main-row padbot">
                <div class="row padbot padtop2">Key contacts</div>
                <div class="row-r padbot"> <?php echo Form::label('comp', 'NAME / COMPANY') ?> <?php echo Form::label('tele', 'TELEPHONE',array('class'=>'pad')) ?> </div>
              </div>
              <div class="main-row padtop">
                <div class="row padtop">&nbsp;</div>
                <div class="row-r padtop"> <?php echo Form::input('txtcompname','', array('id'=>'txtcompname','class'=>'input-w')) ?> <?php echo Form::input('txttelephone','', array('id'=>'txttelephone','class'=>'input-w')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a picture</div>
                <div class="row-r"> <?php echo Form::input('picture_file','', array('id'=>'picture_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a Document</div>
                <div class="row-r"> <?php echo Form::input('docu_file','', array('id'=>'docu_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a Video</div>
                <div class="row-r"> <?php echo Form::input('video_file','', array('id'=>'video_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
            </div>
            <?php echo Form::close() ?>
            <div class="clear"></div>
          </div>
          <div class="TabbedPanelsContent"> <?php echo Form::open(URL::base().'product/add', array('id'=>'frmproduct','name'=>'frmproduct','method'=>'post','enctype' => 'multipart/form-data')) ?>
            <div class="fild-outer">
              <div class="main-row">
                <div class="row">Upload Company logo</div>
                <div class="row-r"> <?php echo Form::input('logo_file','', array('id'=>'logo_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Product name</div>
                <div class="row-r"> <?php echo Form::input('txtname','', array('id'=>'txtname')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Description</div>
                <div class="row-r"> <?php echo Form::input('txtdesc','', array('id'=>'txtdesc')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Notes</div>
                <div class="row-r"> <?php echo Form::input('txtnotes','', array('id'=>'txtnotes')) ?> </div>
              </div>
              <div class="main-row padbot">
                <div class="row padbot padtop2">Key contacts</div>
                <div class="row-r padbot"> <?php echo Form::label('comp', 'NAME / COMPANY') ?> <?php echo Form::label('tele', 'TELEPHONE',array('class'=>'pad')) ?> </div>
              </div>
              <div class="main-row padtop">
                <div class="row padtop">&nbsp;</div>
                <div class="row-r padtop"> <?php echo Form::input('txtcompname','', array('id'=>'txtcompname','class'=>'input-w')) ?> <?php echo Form::input('txttelephone','', array('id'=>'txttelephone','class'=>'input-w')) ?> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a picture</div>
                <div class="row-r"> <?php echo Form::input('picture_file','', array('id'=>'picture_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a Document</div>
                <div class="row-r"> <?php echo Form::input('docu_file','', array('id'=>'docu_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
              <div class="main-row">
                <div class="row">Upload a Video</div>
                <div class="row-r"> <?php echo Form::input('video_file','', array('id'=>'video_file','type'=>'file')) ?> <span>File type XXX XXX<br>
                  Size 00px X 00px</span> </div>
              </div>
            </div>
            <?php echo Form::close() ?>
            <div class="clear"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="penal-qr">
      <input name="save" id="save" type="submit" class="save" value="Save">
      <div class="qr-bg"><img src="<?php echo URL::base();?>images/QR-code.jpg" alt="" /></div>
      <div class="btn"> <a href="#">DOWNLOAD PDF</a> <a href="#">ORDER LABELS</a> </div>
    </div>
  </div>
</div>
<script type="text/javascript">
var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");
$(document).ready(function(){
	$("#save").click(function(){
		$('.TabbedPanelsContentGroup').find(".TabbedPanelsContentVisible form").submit();
	});
	
	$("#frmplant").validate({
		rules: {
			logo_file: { required: true, accept: 'png|jpg|jpeg|gif'},
			txtname: {required: true},
			txtidenty: {required: true},
			txtsrno: {required: true},
			txtdesc: {required: true},
			txtnotes: {required: true},
			txtcompname: {required: true},
			txttelephone: {required: true},
			picture_file: {required: true},			
			docu_file: {required: true, accept: 'doc|docx|pdf'},
			video_file: {required: true, accept: 'png|jpg|jpeg|gif'},
		},
		errorElement: "div",
		messages: {
			logo_file: {
				required: "sdf",
				accept: "png jpg"		
			},
			txtname: "Please Enter Password",
			txtidenty: "Please Enter Confirm Password",
			txtsrno: "txtsrno",
			txtdesc: "txtdesc",
			txtnotes: "txtnotes",
			txtcompname: "txtcompname",
			txttelephone: "txttelephone",
			picture_file: "picture_file"  
		}
	});
	$("#frmasset").validate({
		rules: {
			logo_file: { required: true, accept: 'png|jpg|jpeg|gif', filesize: 31744 },
			txtname: {required: true},
			txtgpscoord: {required: true},
			txtidenty: {required: true},
			txtdesc: {required: true},
			txtnotes: {required: true},
			txtcompname: {required: true},
			txttelephone: {required: true},
			picture_file: {required: true}
		},
		errorElement: "div",
		messages: {
			logo_file: {
				required: "sdf",
				accept: "png jpg",
				filesize: "maximum 30000"			
			},
			txtname: "Please Enter Password",
			txtidenty: "Please Enter Confirm Password",
			txtsrno: "txtsrno",
			txtdesc: "txtdesc",
			txtnotes: "txtnotes",
			txtcompname: "txtcompname",
			txttelephone: "txttelephone",
			picture_file: "picture_file"  
		}
	});
	$("#frmlocation").validate({
		rules: {
			logo_file: { required: true, accept: 'png|jpg|jpeg|gif', filesize: 31744 },
			txtname: {required: true},
			txtsrno: {required: true},
			txtdesc: {required: true},
			txtnotes: {required: true},
			txtcompname: {required: true},
			txttelephone: {required: true},
			picture_file: {required: true}
		},
		errorElement: "div",
		messages: {
			logo_file: {
				required: "sdf",
				accept: "png jpg",
				filesize: "maximum 30000"			
			},
			txtname: "Please Enter Password",
			txtidenty: "Please Enter Confirm Password",
			txtsrno: "txtsrno",
			txtdesc: "txtdesc",
			txtnotes: "txtnotes",
			txtcompname: "txtcompname",
			txttelephone: "txttelephone",
			picture_file: "picture_file" 
		}
	});
	$("#frmproduct").validate({
		rules: {
			logo_file: { 
				required: true
			},
			txtname: {required: true},			
			txtdesc: {required: true},
			txtnotes: {required: true},
			txtcompname: {required: true},
			txttelephone: {required: true},
			picture_file: {required: true},			
			docu_file: {required: true},
			video_file: {required: true},
		},
		errorElement: "div",
		messages: {
			logo_file: {
				required: "sdf"		
			},
			txtname: "Please Enter Password",			
			txtdesc: "txtdesc",
			txtnotes: "txtnotes",
			txtcompname: "txtcompname",
			txttelephone: "txttelephone",
			picture_file: "picture_file",			
			docu_file: "docu_file",
			video_file: "video_file"      
		}
	});
	
});
</script>