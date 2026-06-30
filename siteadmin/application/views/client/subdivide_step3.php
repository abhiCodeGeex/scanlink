<div class="container">
<div class="contant inner-box">

<?php echo Form::open('', array('id'=>'frm_subdivide_step3_client','name'=>'frm_subdivide_step3_client','method'=>'post','class'=>'subdivide' )) ?>
<input type="hidden" id="client" name="client" value="<?php echo $client; ?>" />
<?php foreach($profiles as $key => $profile_id){ ?>
<input type="hidden" id="profiles_<?php echo $key; ?>" name="profiles[]" value="<?php echo $profile_id; ?>" />
<?php } ?>
<div class="penal">
<h3>
     <span class="fleft">*&nbsp;Please Provide a Details for new Client</span>
     <span class="fright">&nbsp;</span>
	<div class="clear"></div>
</h3>
<?php if( ($errors || $succmsg)){ ?>
<div class="main-row">
	<div class="row"></div>
	<div class="row-r">
		<div class="mymsg" style=" <?php echo $succmsg ? 'color:#35D10A' : 'color:#F00';?>; <?php echo $succmsg ? 'border-color:#35D10A' : 'border-color:#F00';?>;"><?php if( ($errors || $succmsg)){ echo $errors ? $errors: ($succmsg ? $succmsg: 'k'); }?></div>
	</div>
</div>
<?php } ?>
<div class="main-row">
	<div class="row">Client Name:</div>
	<div class="row-r">
		<?php echo Form::input('new_client[client_name]', $post ? $post['client_name'] : '', array('id'=>'txtClientname')) ?>
	</div>
</div>
          

<div class="main-row">
	<div class="row">Address:</div>
	<div class="row-r">
          <?php echo Form::input('new_client[address]', $post ? $post['address'] : '', array('id'=>'txtAddress')) ?>
     </div>
</div>

<div class="main-row">
	<div class="row">Telephone:</div>
	<div class="row-r">
          <?php echo Form::input('new_client[telephone]', $post ? $post['telephone'] : '', array('id'=>'txtTelephone')) ?>
     </div>
</div>

<div class="main-row">
	<div class="row">Contact Person:</div>
	<div class="row-r">
          <?php echo Form::input('new_client[contact_person]', $post ? $post['contact_person'] : '', array('id'=>'txtContactperson')) ?>
          </div></div>         
	
<div class="main-row">
	<div class="row">Registration date:</div>
	<div class="row-r">
          <?php echo Form::input('new_client[regi_date]', $post ? $post['regi_date'] : '', array('id'=>'txtRegidate')) ?>
     </div>
</div>          

<div class="main-row">
	<div class="row">Email:</div>
	<div class="row-r">
          <?php echo Form::input('new_client[email]', $post ? $post['email'] : '', array('id'=>'txtEmail')) ?>
     </div>
</div>          

<div class="main-row">
	<div class="row">Password:</div>
	<div class="row-r">
          <?php echo Form::input('new_client[password]', $post ? $post['password'] : '',array('type'=>'text','id'=>'txtPassword')) ?>
     </div>
</div>

<div class="main-row">
	<div class="row">URL:</div>
	<div class="row-r">
          <?php echo Form::input('new_client[url]', $post ? $post['url'] : $client_details['url'], array('id'=>'txtUrl','readonly'=>'readonly')) ?>
	</div>
</div>


<div class="main-row">
	<div class="row"></div>
	<div class="row-r">
          (NOTE : Only characters,numbers,underscore and dash are allowed.)
	</div>
</div>

<div class="main-row">
	<div class="row"></div>
	<div class="row-r">
     	<button type="submit" value="Save details and go to next step" name="next" id="next">Save details and go to next step</button>
     </div>
</div>
          
</div>
<?php echo Form::close() ?>
</div>
</div>