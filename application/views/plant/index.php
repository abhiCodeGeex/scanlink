<script>
  $(document).ready(function() {


    $("#activation_start_date").datepicker({
      dateFormat: 'dd/mm/yy',
      onSelect: function() {


        if (($('#free_code').val() == 0 || ($('#free_code').val() == 1 && $('#renewal_required').val() == 1)) && validate_date_range($(this).val(), $('#hdn_exp_date').val()) == false) {
          alert("Date range can\'t be set until the code is renewed.");
          $(this).val('');
        }



        if ($("#activation_end_date").val() != "") {
          if (is_valid_date($("#activation_end_date").val()) == false) {
            alert("Please select valid end date");
            return false;
          } else if (validate_date_range($(this).val(), $("#activation_end_date").val()) == false) {
            alert("Please select valid date range");
            $(this).val('');
          }

        }

      }
    });

    $("#activation_end_date").datepicker({
      dateFormat: 'dd/mm/yy',
      onSelect: function() {

        if (($('#free_code').val() == 0 || ($('#free_code').val() == 1 && $('#renewal_required').val() == 1)) && validate_date_range($(this).val(), $('#hdn_exp_date').val()) == false) {
          alert("Date range can\'t be set until the code is renewed.");
          $(this).val('');
        }

        if ($("#activation_start_date").val() != "") {
          if (is_valid_date($("#activation_start_date").val()) == false) {
            alert("Please select valid start date");
            return false;
          } else if (validate_date_range($("#activation_start_date").val(), $(this).val()) == false) {
            alert("Please select valid date range");
            $(this).val('');
          }
        }
      }
    });

    $('.clear-dates').on('click', function() {
      $("#activation_start_date,#activation_end_date").val('');
    });

    $('#add_existing').on('click', function() {

      $(".forms_existing").colorbox({
        inline: true,
        width: "770",
        height: "90%",
        onClosed: function() {

        },
      });

      $("#tabs").tabs(); //for tab view
      $("#tabed_content_main").show();
      $("#tabed_content_other").hide();
      $('#preview_form').hide();
      $('#form_listing').show();

      $("#forms_existing_tag").click();
    });

    //hide help tooltip when mouseleave
    $('.help-div').on('mouseleave', function() {
      $(this).hide();
    });

    // show help tooltip
    $('.help-icon').on('mouseenter', function() {
      $(this).next().show();
    });

    //open help icon
    $('.help-div a').click(function(e) {
      $(this).parent().hide();

      e.preventDefault();
      var url = $(this).attr("href");
      if (url != '') {
        var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
        var result = (url.match(p)) ? RegExp.$1 : false;
        if (result) {
          $('#how_to_pop_up').html('<iframe src="' + url + '" frameborder="0" allowfullscreen wmode="transparent" width="480" height="300" ></iframe>');
          $('.black-tint').show();
          $('.player-window').show(500);
        } else {

          var substr = 'image#';
          if (url.indexOf(substr) !== -1) {
            url = url.replace(substr, "");


            $('.black-tint').show();
            $('.player-window-image').show(500);
            3
          } else {
            return false;
          }
        }
      } else {
        return false;
      }
    });


  });
</script>

<?php
echo $formbuilder_popup;
echo $formbuilder_existing_popup;
echo $add_edit_participant_list_popup;
echo $innermenu; ?>
<?php
if ($add_form_display) {
?>
  <?php echo Form::open(URL::base() . 'plant/index', array('id' => 'frmplant', 'name' => 'frmplant', 'method' => 'post', 'enctype' => 'multipart/form-data')) ?>
  <input type="hidden" id="profile_id" name="profile_id" value="<?php echo $profile_id; ?>" />
  <input type="hidden" id="hdn_email_arr" name="hdn_email_arr" value="" />
  <input type="hidden" id="hdn_form_name" name="hdn_form_name" value="" />
  <input type="hidden" id="form_email_tag" name="form_email_tag" value="" />
  <input type="hidden" id="hdn_exp_date" name="hdn_exp_date" value="<?php echo $expired_at; ?>" />
  <input type="hidden" id="free_code" name="free_code" value="<?php echo $free_code; ?>" />
  <input type="hidden" id="renewal_required" name="renewal_required" value="<?php echo $renewal_required; ?>" />
  <div class="scanlink-container">
    <section class="black-tint"></section>
    <section class="add-form-right">
      <!-- ADD FORM RIGHT -->
      <section class="iphone-preview">
      </section>
      <div class="clear"></div>
      <div class="form-builder-box">
        <div class="form-builder-title">Form Builder
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 117px;"><a href="https://www.youtube.com/embed/CfLEhcgvgrA?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>

          <div class="activate-for-profile">
            <span><label for="enable_form">Enable</label></span>
            <span><input type="checkbox" name="enable_form" value="1" id="enable_form" /></span>
          </div>
        </div>

        <div class="existing-item">
          <span><input type="checkbox" name="add_existing" id="add_existing" /></span>
          <span><label for="add_existing">Use an existing form</label></span>
        </div>

        <div class="existing-item">
          <span><input type="checkbox" name="enable_form_analytics" value="1" id="enable_form_analytics" /></span>
          <span><label for="enable_form_analytics">Enable Form Analytics <small>(Note: editing this form will not be possible once saved)</small></label></span>
        </div>

        <div class="existing-item">
          <span>Form submission format</span>

          <span><input type="radio" name="form_submission_format" value="0" checked="" id="email_only" /></span>
          <span><label for="email_only">Email only</label></span>

          <span><input type="radio" name="form_submission_format" value="1" id="email_with_pdf" /></span>
          <span><label for="email_with_pdf">Email notification with PDF</label></span>
        </div>

        <div class="clear">&nbsp;</div>
        <div id="iframe_container">
          <iframe id="iframe_frm_builder" width="448" frameborder="no" height="1114" scrolling="no" src="<?php echo URL::base(); ?>formbuilder/index/?profile_id=<?php echo $profile_id; ?>&enable_form_analytics=0"></iframe>
        </div>

        <div class="clear">&nbsp;</div>

      </div>
      <div class="footer-rouded"><span class="expand-reduce">Expand Window</span> <img src="<?php echo URL::base() ?>images/expand_window.png" class="expand-reduce" id="expand_reduce_img" width="25" align="absmiddle" alt=""> </div>
      <div class="clear">&nbsp;</div>
      <?php /* <section class="button-section">
    <a class="green-btn">Download PDF</a>
    <a class="gray-btn">Order Label</a>
    </section> */ ?>

    </section><!-- /ADD FORM RIGHT -->


    <section class="add-form-left">
      <!-- ADD FORM LEFT -->
      <div class="SectionTitleBox">
        <div class="SectionTitle">Code Profile Name</div>
        <ul class="form-view clearfix">
          <li>
            <?php echo Form::input('txt_code_profile_name', isset($post['txt_code_profile_name']) ? $post['txt_code_profile_name'] : '', array('id' => 'txt_code_profile_name', 'class' => 'text-fi', 'style' => 'width:450px;', 'maxlength' => '255')) ?>
            <?php echo Form::label('txt_code_profile_name', isset($errors['txt_code_profile_name']) ? $errors['txt_code_profile_name'] : '', array('class' => 'error', 'generated' => 'true')) ?>
          </li>
        </ul>

        <div class="clear"></div>
        <div class="set-activation-period">
          Set activation period (optional)<br>
          <span class="hint-info">Leave date fields blank if this does not apply</span>
        </div>
        <div class="activation-start-date">
          Start Date
          <br>
          <input type="text" id="activation_start_date" name="activation_start_date" value="" />
        </div>
        <div class="activation-end-date">
          End Date
          <br>
          <input type="text" id="activation_end_date" name="activation_end_date" value="" />
        </div>
        <div>
          <br>
          <img class="clear-dates" src="<?php echo url::base(); ?>images/clear_date.png" alt="Clear Dates" title="Clear Dates" />
        </div>
        <div class="clear"></div>

      </div>

      <div class="SectionTitleBox">
        <div class="SectionTitle">Logo
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 50px;"><a href="https://www.youtube.com/embed/hGrBYsys2Oo?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <label>Upload Company Logo: <span class="hint-info">(File type JPG, JPEG, PNG, GIF) (Max file size <?php echo UPLOAD_MAX_FILESIZE; ?>)</span></label>
            <iframe src="<?php echo url::base(); ?>test/logoUpload?profile_id=<?php echo $profile_id; ?>&type_id=<?php echo $type_id; ?>" id="logoUpload" scrolling="no" frameborder="0" width="400" height="50"></iframe>

            <?php if (isset($post["logo"]) && isset($post["logo"]["logo_name"]) && $post["logo"]["logo_name"] != "") { ?>
              <div id="logo_data">
                <img src="<?php echo URL::base() . $post["logo"]["logo_name"]; ?>" height="30" alt="<?php echo $post['txtname'] ?>" style="margin:5px" />
                <img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Logo" title="Remove Logo" class="tooltip delete_ico delete_img_logo" onClick="deleteLogo('<?php echo $profile_id; ?>','<?php echo URL::base(); ?>')" />
                <input type="hidden" id="uploaded" name="uploaded" value="1" />
              </div>
            <?php } else { ?>
              <input type="hidden" id="uploaded" name="uploaded" value="" />
            <?php
            } ?>
          </li>
        </ul>
      </div>

      <?php //if($video_per == 1 ){ 
      ?>
      <div class="SectionTitleBox">
        <div class="SectionTitle">Videos
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 65px;"><a href="https://www.youtube.com/embed/H33caspIlcc?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li <?php //echo $video_per == 0 ? 'style="display:none;"' : ''; 
              ?>>
            <label>Upload a Video:</label>
            <a href="<?php echo url::base() . "test"; ?>" class="iframe">Upload Video &nbsp;<img src="<?php echo url::base() . "images/upload-video-ico.png"; ?>" width="20" height="20" /></a>
            <input type="hidden" name="video" id="video" value="<?php echo isset($post['video']) ? $post['video'] : '' ?>" />
            <input type="hidden" name="videoTitle" id="videoTitle" value="" />

            <?php if (isset($post["videos"]) && count($post["videos"]) > 0) {
            ?>
              <div class="main-row">
                <div class="row">Current Videos</div>
                <div class="row-r">
                  <fieldset>
                    <table border="0" cellpadding="0" cellspacing="5" id="videos_display">
                      <?php
                      $i = 0;
                      $num = 0;
                      foreach ($post["videos"] as $video) : $i++;
                      ?>
                        <tr>
                          <td>
                            <?php echo $i; ?>)
                            <a href="<?php echo "https://www.youtube.com/watch?v=" . $video["video_name"]; ?>" target="_blank"><?php echo $video['title']; ?></a>
                            <img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Video" title="Remove Video" class="tooltip delete_ico" onClick="deleteVideo('<?php echo $video["id"]; ?>')" />
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </table>
                  </fieldset>
                </div>
              </div>
            <?php
            } ?>

          </li>
        </ul>
      </div>
      <?php // } 
      ?>

      <div class="SectionTitleBox">
        <div class="SectionTitle">Words
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 65px;"><a href="https://www.youtube.com/embed/CZx8xplEfoU?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <label>Make / Model:</label>
            <?php echo Form::input('txtname', isset($post['txtname']) ? $post['txtname'] : '', array('id' => 'txtname', 'class' => 'text-fi')) ?>
            <?php echo Form::label('txtname', isset($errors['txtname']) ? $errors['txtname'] : '', array('class' => 'error', 'generated' => 'true')) ?>
          </li>
          <li>
            <label>ID:</label>
            <?php echo Form::input('txtidenty', isset($post['txtidenty']) ? $post['txtidenty'] : '', array('id' => 'txtidenty', 'class' => 'text-fi')) ?>
            <?php echo Form::label('txtidenty', isset($errors['txtidenty']) ? $errors['txtidenty'] : '', array('class' => 'error', 'generated' => 'true')) ?>
          </li>
          <li>
            <label>Serial No.:</label>
            <?php echo Form::input('txtsrno', isset($post['txtsrno']) ? $post['txtsrno'] : '', array('id' => 'txtsrno', 'class' => 'text-fi')) ?>
            <?php echo Form::label('txtsrno', isset($errors['txtsrno']) ? $errors['txtsrno'] : '', array('class' => 'error', 'generated' => 'true')) ?>
          </li>
          <li>
            <label>Description:</label>
            <?php echo Form::textarea('txtdesc', isset($post['txtdesc']) ? $post['txtdesc'] : '', array("rows" => 1, "cols" => 40, "class" => "input")); ?>
            <?php echo Form::label('txtdesc', isset($errors['txtdesc']) ? $errors['txtdesc'] : '', array('class' => 'error', 'generated' => 'true')) ?>
          </li>
          <li>
            <label>Note:</label>
            <?php echo Form::textarea('txtnotes', isset($post['txtnotes']) ? $post['txtnotes'] : '', array("rows" => 1, "cols" => 40, "class" => "input")); ?>
            <?php echo Form::label('txtnotes', isset($errors['txtnotes']) ? $errors['txtnotes'] : '', array('class' => 'error', 'generated' => 'true')) ?>
          </li>

          <?php echo $contact_data; ?>
          <?php

          /*
      <li class="width-half">
        <label><?php echo Form::label('comp', 'CONTACT:') ?></label>
        <?php echo Form::input('txtcompname', isset($post['txtcompname']) ? $post['txtcompname'] : '', array('id' => 'txtcompname', 'class' => 'text-fi')) ?>
        <?php echo Form::label('txtcompname', isset($errors['txtcompname']) ? $errors['txtcompname'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li class="width-half">
        <label><?php echo Form::label('tele', 'TELEPHONE:', array('class' => 'pad')) ?></label>
        <?php echo Form::input('txttelephone', isset($post['txttelephone']) ? $post['txttelephone'] : '', array('id' => 'txttelephone', 'class' => 'text-fi')) ?>	
        <?php echo Form::label('txttelephone', isset($errors['txttelephone']) ? $errors['txttelephone'] : '', array('id' => 'errtele', 'class' => 'error', 'generated' => 'true')) ?>                      	
      </li>
      */
          ?>

          <?php /*
        <li class="width-half">
        <label><?php echo Form::label('comp', 'Mobile button back color:') ?></label>
        <?php echo Form::input('txtbuttonbackcolor', '', array('id' => 'txtbuttonbackcolor', 'class' => 'text-fi color {required:false}')) ?>
        <?php echo Form::label('txtbuttonbackcolor', isset($errors['txtbuttonbackcolor']) ? $errors['txtbuttonbackcolor'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
        </li>
        <li class="width-half">
        <label><?php echo Form::label('tele', 'Mobile button font color:', array('class' => 'pad')) ?></label>
        <?php echo Form::input('txtbuttonfontcolor', '', array('id' => 'txtbuttonfontcolor', 'class' => 'text-fi color {required:false}')) ?>
        <?php echo Form::label('txtbuttonfontcolor', isset($errors['txtbuttonfontcolor']) ? $errors['txtbuttonfontcolor'] : '', array('id' => 'errtele', 'class' => 'error', 'generated' => 'true')) ?>
        </li>
       */ ?>
        </ul>
      </div>

      <div class="SectionTitleBox">
        <div class="SectionTitle">Pictures
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 79px;"><a href="https://www.youtube.com/embed/GshHCp9F0wU?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <label>Upload a picture: <span class="hint-info">(File type JPG, JPEG, PNG, GIF) (Max file size <?php echo UPLOAD_MAX_FILESIZE; ?>)</span></label>
            <iframe src="<?php echo url::base(); ?>test/pictureUpload?profile_id=<?php echo $profile_id; ?>&type_id=<?php echo $type_id; ?>" id="imageUpload" scrolling="no" frameborder="0" height="50" width="400"></iframe>

            <?php if (isset($post["pictures"]) && count($post["pictures"]) > 0) {
            ?>
              <div class="main-row">
                <div class="row">Current Image</div>
                <div class="row-r">
                  <fieldset>
                    <table border="0" cellpadding="0" cellspacing="5" id="pictures_display">
                      <tr>
                        <?php
                        $i = 0;
                        foreach ($post["pictures"] as $picture) : $i++;
                        ?>
                          <td><img src="<?php echo URL::base() . $picture["picture_name"]; ?>" height="50" width="50" /><img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="tooltip delete_ico" alt="Remove Picture" title="Remove Picture" onClick="deletePicture('<?php echo $picture["id"]; ?>')" /></td>
                          <?php if ($i == 5) : $i = 0; ?>
                      </tr>
                      <tr>
                      <?php endif; ?>
                    <?php endforeach; ?>
                      </tr>
                    </table>
                  </fieldset>
                </div>
              </div>
            <?php
            } ?>

          </li>
        </ul>
      </div>

      <div class="SectionTitleBox">
        <div class="SectionTitle">Documents
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 105px;"><a href="https://www.youtube.com/embed/ujiEr65yg30?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <label>Upload a Document: <span class="hint-info">(File type DOC, DOCX, PDF, JPG, GIF, JPEG) (Max file size <?php echo UPLOAD_MAX_FILESIZE; ?>)</span></label>
            <iframe src="<?php echo url::base(); ?>test/docUpload?profile_id=<?php echo $profile_id; ?>&type_id=<?php echo $type_id; ?>" id="docUpload" scrolling="no" frameborder="0" height="50" width="400"></iframe>

            <?php if (isset($post["documents"]) && count($post["documents"]) > 0) {
            ?>
              <div class="main-row">
                <div class="row">Current Documents</div>
                <div class="row-r">
                  <fieldset>
                    <table border="0" cellpadding="0" cellspacing="5" id="documents_display">
                      <?php
                      $i = 0;
                      $num = 0;
                      foreach ($post["documents"] as $document) : $i++;
                      ?>
                        <tr>
                          <td><?php echo $i; ?>) <a href="<?php echo URL::base() . $document["doc_name"]; ?>"><?php echo $document["name"]; ?></a><img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Document" title="Remove Document" class="tooltip delete_ico" onClick="deleteDocument('<?php echo $document["id"]; ?>')" /></td>
                        </tr>
                      <?php endforeach; ?>
                    </table>
                  </fieldset>
                </div>
              </div>
            <?php
            } ?>

          </li>
        </ul>
      </div>

      <?php echo $web_link_data; ?>

      <?php /*
  <div class="SectionTitleBox">
    <div class="SectionTitle">Web Link</div>
    <ul class="form-view clearfix">

      <li>
        <div style="float: left; width: 350px;">
          <?php echo Form::checkbox('link_button', '1', false, array('id' => 'link_button')) ?>
          <?php echo Form::label('link_button', 'Add Button', array('style' => 'display: inline;')) ?>
        </div>
      </li>

      <li class="width-half">
        <label><?php echo Form::label('comp', 'Button Text:') ?></label>
        <?php echo Form::input('link_button_text', isset($post['link_button_text']) ? $post['link_button_text'] : '', array('id' => 'link_button_text', 'class' => 'text-fi')) ?>
        <?php echo Form::label('link_button_text', isset($errors['link_button_text']) ? $errors['link_button_text'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li class="width-half">
        <label><?php echo Form::label('comp', 'Button Link:') ?></label>
        <?php echo Form::input('link_button_url', isset($post['link_button_url']) ? $post['link_button_url'] : '', array('id' => 'link_button_url', 'class' => 'text-fi')) ?>
        <?php echo Form::label('link_button_url', isset($errors['link_button_url']) ? $errors['link_button_url'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li class="width-half">
        <label><?php echo Form::label('comp', 'Button color:') ?></label>
        <?php echo Form::input('link_button_color', '', array('id' => 'link_button_color', 'class' => 'text-fi color {required:false}')) ?>
        <?php echo Form::label('link_button_color', isset($errors['link_button_color']) ? $errors['link_button_color'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>

    </ul>
  </div>
  
  */ ?>

      <div class="SectionTitleBox">
        <div class="SectionTitle">Data Collection
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 133px;"><a href="https://www.youtube.com/embed/C_vH14MFtXA?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <label style="float: left;">Enable Data Collection Pop Up Window:</label>
            <div style="float: left; width: 350px;">
              <?php echo Form::radio('enable_data_collection', '1', ((isset($post["enable_data_collection"]) && $post["enable_data_collection"] == 1) ? true : false), array('class' => 'radi enable_data_collection')) ?>
              <?php echo Form::label('enable_data_collection', 'Yes', array('style' => 'display: inline;')) ?>
              <?php echo Form::radio('enable_data_collection', '0', ((!isset($post["enable_data_collection"]) ||  (isset($post["enable_data_collection"]) && $post["enable_data_collection"] == 0)) ? true : false), array('class' => 'radi enable_data_collection')) ?>
              <?php echo Form::label('enable_data_collection', 'No', array('style' => 'display: inline;')) ?>
            </div>
          </li>

          <li>
            <div style="float: left; width: 350px;">
              <?php
              echo Form::checkbox(
                'set_up_compulsory',
                '1',
                isset($post['set_up_compulsory']) ? ($post['set_up_compulsory'] == 1) ? true : false  : false,
                array(
                  'class' => 'radi enable_data_collection'
                )
              );
              ?>
              <?php
              echo Form::label(
                'set_up_compulsory',
                'Set as compulsory',
                array(
                  'style' => 'display: inline;'
                )
              );
              ?>
            </div>
          </li>

          <div class="data_collection_div">
            <li>
              <div style="float: left; width: 350px;">
                <?php echo Form::checkbox('data_collection_mobile', '1', (isset($post['data_collection_mobile'])  && $post['data_collection_mobile'] == 1) ? TRUE : FALSE, array('id' => 'data_collection_mobile')) ?>
                <?php echo Form::label('data_collection_mobile', 'Mobile', array('style' => 'display: inline;')) ?>

                <?php echo Form::checkbox('data_collection_email', '1', (isset($post['data_collection_email'])  && $post['data_collection_email'] == 1) ? TRUE : FALSE, array('id' => 'data_collection_email')) ?>
                <?php echo Form::label('data_collection_email', 'Email', array('style' => 'display: inline;')) ?>

                <?php echo Form::checkbox('data_collection_name', '1', (isset($post['data_collection_name'])  && $post['data_collection_name'] == 1) ? TRUE : FALSE, array('id' => 'data_collection_name')) ?>
                <?php echo Form::label('data_collection_name', 'Name', array('style' => 'display: inline;')) ?>

                <?php echo Form::checkbox('data_collection_surname', '1', (isset($post['data_collection_surname'])  && $post['data_collection_surname'] == 1) ? TRUE : FALSE, array('id' => 'data_collection_surname')) ?>
                <?php echo Form::label('data_collection_surname', 'Surname', array('style' => 'display: inline;')) ?>
              </div>
            </li>

            <li>
              <div>
                <?php echo Form::label('data_collection_content', 'Content') ?>
                <?php echo Form::textarea('data_collection_content', (isset($post['data_collection_content'])) ? $post['data_collection_content'] : '', array('id' => 'data_collection_content')); ?>
              </div>
            </li>
          </div>
        </ul>
      </div>

      <div class="SectionTitleBox">
        <div class="SectionTitle">Set Code Type
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 130px;"><a href="https://www.youtube.com/embed/jCeyQOfm7uc?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <label style="float: left;margin-top:3px;">Code Type:</label>
            <div style="float: left; width: 350px;">
              <?php echo Form::radio('code_type', '0', ((!isset($post["code_type"]) ||  (isset($post["code_type"]) && $post["code_type"] == 0)) ? true : false), array('class' => 'radi')) ?>
              <?php echo Form::label('code_type_qr', 'QR Code', array('style' => 'display: inline;')) ?>
              <?php echo Form::radio('code_type', '1', ((isset($post["code_type"]) && $post["code_type"] == 1) ? true : false), array('class' => 'radi')) ?>
              <?php echo Form::label('code_type_dm', 'Data Matrix', array('style' => 'display: inline;')) ?>
            </div>
          </li>
        </ul>
      </div>

      <div class="SectionTitleBox">
        <div class="SectionTitle">Header
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 73px;"><a href="https://www.youtube.com/embed/eJtzHbZoCPw?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <div style="float: left; width: 375px;">
              <?php echo Form::checkbox('show_header', '1', (isset($post['show_header'])  && $post['show_header'] == 1) ? TRUE : FALSE, array('id' => 'show_header')) ?>
              <?php echo Form::label('show_header', 'Display the code number at the top of mobile screen', array('style' => 'display: inline;')) ?>
            </div>
          </li>
        </ul>
      </div>


      <div class="SectionTitleBox">
        <div class="SectionTitle">User Access Security
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 184px;"><a href="https://www.youtube.com/embed/KcXJnxuMVyc?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <label style="float: left;margin-top:3px;">Password protect?:</label>
            <div style="float: left; width: 350px;">
              <?php echo Form::radio('protect', '1', ((isset($post["protect"]) && $post["protect"] == 1) ? true : false), array('class' => 'radi')) ?>
              <?php echo Form::label('protectyes', 'Yes', array('style' => 'display: inline;')) ?>
              <?php echo Form::radio('protect', '0', ((!isset($post["protect"]) ||  (isset($post["protect"]) && $post["protect"] == 0)) ? true : false), array('class' => 'radi')) ?>
              <?php echo Form::label('protectno', 'No', array('style' => 'display: inline;')) ?>
            </div>
          </li>

          <li>
            <label style="float: left;">Password:</label>
            <div>
              <?php echo Form::input('password', isset($post['password']) ? $post['password'] : '', array('id' => 'password', 'class' => 'text-fi')) ?>
              <?php echo Form::label('password', isset($errors['password']) ? $errors['password'] : '', array('class' => 'error', 'generated' => 'true')) ?>
            </div>
          </li>


        </ul>
      </div>

      <div class="SectionTitleBox">
        <div class="SectionTitle">Share
          <img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
          <div class="help-div" style="margin-left: 60px;"><a href="https://www.youtube.com/embed/qOi6tSBsII4?rel=0">Video Tutorial</a>
            <div class="arrow-bg"></div>
          </div>
        </div>
        <ul class="form-view clearfix">
          <li>
            <?php
            echo Form::checkbox(
              'display_share_link',
              '1',
              isset($post['display_share_link']) ? $post['display_share_link'] ? true : false  : false,
              array(
                'class' => 'radi enable_data_collection'
              )
            );
            ?>
            <?php
            echo Form::label(
              'display_share_link',
              'Display share links',
              array(
                'style' => 'display: inline;'
              )
            );
            ?>
          </li>
          <div class="clear"></div>
        </ul>
        <div class="shareNav">
          <a href="javascrit:void(0);" class="shareFB">Facebook</a>
          <a href="javascrit:void(0);" class="shareTWT">Twitter</a>
          <a href="javascrit:void(0);" class="shareEML">Email</a>
        </div>
      </div>

      <ul class="form-view clearfix">







        <?php /* if($checklist_option==1){ ?>
      <li>
      <label>Checklist Items:</label>
      <table id="checklist_item_container"></table>
      <a href="javascript:addNewChecklistItem('',0)">Add new Checklist Item</a>
      </li>
      <?php }else{ ?>
      <input type="hidden" name="checklist_item[]" />
      <?php } */ ?>




        <li>
          <?php echo Form::submit(NULL, '&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;', array('id' => 'save', 'class' => 'save', 'style' => 'width: 150px;', 'onclick' => 'javascript: beforeSave();')) ?>
        </li>
      </ul>
    </section>

    <?php echo Form::close() ?>

    <div id="progressbar_weblink">
      <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait.
    </div>


    <div id="progressbar">
      <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile...
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {

      jQuery('#data_collection_content').maxlength({
        slider: true,
        maxCharacters: 150
      });

      jQuery('.enable_data_collection').click(function() {
        if ($(this).val() == "0") {
          $('.data_collection_div').hide();
          $('#data_collection_mobile').attr('checked', false);
          $('#data_collection_email').attr('checked', false);
          $('#data_collection_content').val('');
        } else {
          $('.data_collection_div').show();
        }
      });


      jQuery(".iframe").colorbox({
        iframe: true,
        width: "750",
        height: "600",
        onClosed: function() {
          //			parent.location.reload()
        }
      });

      /*$("#addmore").click(function(){
    var copyclone = $('#docu').clone();
    $(copyclone).removeAttr("id").addClass("docminus").find("#addmore").removeAttr("id").addClass("thisminus").attr("src","images/minus.png");
    $("#docu").after(copyclone);
  });
  $(".thisminus").live('click',function(){
    $(this).parent().parent().remove();
  });*/
      /*$("#docminus").each(function(){
    $(this).bind("click", function(){
    alert($(this).html());
    //$(this).parent().parent().remove();
    });
  });*/

      $("#add_more_docu").unbind().bind('click', function(event) {
        event.preventDefault();

        var new_main_row = document.createElement("tr");
        new_main_row.className = "main-row";

        var new_row = document.createElement("div");
        new_row.className = "row";
        new_row.innerHTML = "Upload a Document";

        var new_row_r = document.createElement("div");
        new_row_r.className = "row-r";

        var new_row_table = document.createElement("table");
        new_row_table.cellPadding = 0;
        new_row_table.cellSpacing = 0;
        new_row_table.border = 0;
        new_row_table.width = "100%";

        var new_row_table_tr = document.createElement("tr");

        var new_input_name = document.createElement("input");
        new_input_name.id = "docu_name_" + 1;
        new_input_name.name = "docu_name[]";
        new_input_name.type = "text";

        var new_row_table_tr_td1 = document.createElement("td");
        new_row_table_tr_td1.appendChild(new_input_name);

        var new_input_file = document.createElement("input");
        new_input_file.id = "docu_file_" + 1;
        new_input_file.name = "docu_file[]";
        new_input_file.type = "file";
        new_input_file.generated = "true";

        var new_row_table_tr_td2 = document.createElement("td");
        new_row_table_tr_td2.appendChild(new_input_file);

        var new_rem_img = document.createElement("img");
        new_rem_img.src = "<?php echo URL::base(); ?>images/delete-icon.png";
        new_rem_img.className = "doc_input_delete";

        var new_row_table_tr_td3 = document.createElement("td");
        new_row_table_tr_td3.appendChild(new_rem_img);

        new_row_table_tr.appendChild(new_row_table_tr_td1);
        new_row_table_tr.appendChild(new_row_table_tr_td2);
        new_row_table_tr.appendChild(new_row_table_tr_td3);

        new_row_table.appendChild(new_row_table_tr);

        new_row_r.appendChild(new_row_table);

        new_main_row.appendChild(new_row);
        new_main_row.appendChild(new_row_r);

        document.getElementById("docu").appendChild(new_main_row);
      });


      $("#add_more_picture").unbind().bind('click', function(event) {
        event.preventDefault();

        var new_main_row = document.createElement("div");
        new_main_row.className = "main-row";

        var new_row = document.createElement("div");
        new_row.className = "row";
        new_row.innerHTML = "Upload a Picture";
        var new_row_r = document.createElement("div");
        new_row_r.className = "row-r";

        var new_input_file = document.createElement("input");
        new_input_file.name = "picture_file[]";
        new_input_file.type = "file";


        var new_rem_img = document.createElement("img");
        new_rem_img.src = "<?php echo URL::base(); ?>images/delete-icon.png";
        new_rem_img.className = "picture_input_delete";


        new_row_r.appendChild(new_input_file);
        new_row_r.appendChild(new_rem_img);

        new_main_row.appendChild(new_row);
        new_main_row.appendChild(new_row_r);

        document.getElementById("picture").appendChild(new_main_row);
      });


      $("#picture .main-row .row-r .picture_input_delete").live('click', function() {
        $(this).parent().parent().remove();
      });

      $("#docu .main-row .row-r .doc_input_delete").live('click', function() {
        $(this).parent().parent().parent().parent().parent().remove();
      });

      $("#frmplant").validate({



        rules: {
          logo_file: {
            accept: 'png|jpg|jpeg|gif'
          },
          txt_code_profile_name: "required"
        },

        messages: {
          logo_file: {
            accept: "Only png jpg jpeg or gif file is allowed"
          },
          txt_code_profile_name: "Please provide code profile name"
        },
        submitHandler: function(form) {
          var fileArray = document.getElementsByName('docu_file[]');
          var txtArray = document.getElementsByName('docu_name[]');
          for (var i = 0; i < fileArray.length; i++) {
            if (fileArray[i].value.split(" ").join("") != "" && txtArray[i].value.split(" ").join("") == "") {
              alert("Please enter document name for uploaded document.");
              txtArray[i].focus();
              return false;
            }
          }

          activation_start_date = $("#activation_start_date").val();
          activation_end_date = $("#activation_end_date").val();
          hdn_exp_date = $("#hdn_exp_date").val();
          free_code = $("#free_code").val();
          renewal_required = $("#renewal_required").val();


          if (activation_start_date != "" && is_valid_date(activation_start_date) == false) {
            alert("Please select valid start date");
            return false;
          }

          if ((free_code == 0 || (free_code == 1 && renewal_required == 1)) && activation_start_date != "" && validate_date_range(activation_start_date, hdn_exp_date) == false) {
            alert("Date range can\'t be set until the code is renewed.");
            return false;
          }

          if (activation_start_date != "" && activation_end_date == "") {
            alert("Please select end date");
            return false;
          }

          if (activation_end_date != "" && is_valid_date(activation_end_date) == false) {
            alert("Please select valid end date");
            return false;
          }

          if ((free_code == 0 || (free_code == 1 && renewal_required == 1)) && activation_end_date != "" && validate_date_range(activation_end_date, hdn_exp_date) == false) {
            alert("Date range can\'t be set until the code is renewed.");
            return false;
          }

          if (activation_start_date == "" && activation_end_date != "") {
            alert("Please select start date");
            return false;
          }

          if (activation_start_date != "" && activation_end_date != "" && validate_date_range(activation_start_date, activation_end_date) == false) {
            alert("Please select valid date range.");
            return false;
          }



          //check for recipient
          iframe = document.getElementById('iframe_frm_builder');
          form_iframe = iframe.contentDocument.frm;
          email_arr = '';
          form_name = '';
          var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

          var x = iframe.contentDocument.getElementsByName("email_recipient[]");

          var y = iframe.contentDocument.getElementsByName("email_tag");

          var email_tag = y[0].value;

          for (i = 0; i < (x.length); i++) {
            if (x[i].value.split(" ").join("") != "") {
              if (!emailRegexStr.test(x[i].value)) {
                alert('Enter a valid email.');
                return false;
              } else {
                if (email_arr == "")
                  email_arr = x[i].value;
                else
                  email_arr = email_arr + "," + x[i].value;

              }

            }
          }


          if ($('#enable_form').prop('checked') == true) {
            form_name = form_iframe.form_name.value;
            if (form_name.split(" ").join("") == "") {
              alert("Please enter form name");
              return false;
            }

            if (email_arr == "") {
              alert("Please enter at least one recipient email");
              return false;
            }
          }
          document.getElementById('hdn_email_arr').value = email_arr;
          document.getElementById('hdn_form_name').value = form_name;
          document.getElementById('form_email_tag').value = email_tag;

          //////// end check for recipient

          //check validation for participant function i.e. if participant notifi. email or participant name added user has to add participant name on form builder
          email_recipient_str = "";
          if ($(".email_recipient_participant").length > 0) {
            $(".email_recipient_participant").each(function(index) {
              email_val = $.trim($(this).val());

              if (email_val != "") {
                email_recipient_str = email_recipient_str + "::" + email_val;
              }
            });
          }

          if (email_recipient_str != "" || $(".due_date_span").length > 0) {
            var participant_name_ele = iframe.contentDocument.getElementsByName("participant_name_ele[]");
            if (participant_name_ele.length == "0") //no "participant name" control added to form
            {
              alert("This form has an active participant list but no Participant Name field has been added. Please add a Participant Name field to the form before saving.");
              return false;
            }
          }


          //end check validation for participant function i.e. if participant notifi. email or participant name added user has to add participant name on form builder



          document.getElementById("progressbar").style.display = "block";
          form.submit();
          return true;
        }
      });
    });

    function addNewChecklistItem(text, id) {
      var cnt = $(".checklist_item").length;

      var new_input_checklist_item = document.createElement("input");
      new_input_checklist_item.id = "checklist_item_" + (cnt + 1);
      new_input_checklist_item.name = "checklist_item[]";
      new_input_checklist_item.type = "text";
      new_input_checklist_item.maxlength = "50";
      new_input_checklist_item.size = "50";
      new_input_checklist_item.value = text;
      new_input_checklist_item.className = "checklist_item";
      if (id == 0) {
        $(new_input_checklist_item).attr("generated", "true");
      } else {
        var new_input_checklist_item_id = document.createElement("input");
        new_input_checklist_item_id.type = "hidden";
        new_input_checklist_item_id.className = "hidden_checklist_item_id";
        new_input_checklist_item_id.name = "checklist_item_id[]";
        new_input_checklist_item_id.value = id;
        document.getElementById("checklist_item_container").appendChild(new_input_checklist_item_id);
        $(new_input_checklist_item).attr("generated", id);
      }

      var new_tr = document.createElement("tr");
      var new_td = document.createElement("td");
      new_td.appendChild(new_input_checklist_item);
      new_tr.appendChild(new_td);

      var new_delete_img = document.createElement("img");
      new_delete_img.src = "<?php echo URL::base(); ?>images/delete2.png";
      new_delete_img.height = "15";
      new_delete_img.width = "15";
      new_delete_img.alt = "Remove Checklist Item";
      new_delete_img.title = "Remove Checklist Item";
      new_delete_img.className = "tooltip delete_ico";

      if (new_delete_img.addEventListener) {
        new_delete_img.addEventListener('click', function() {
          removeChecklistItem(new_delete_img);
        }, false);
      } else {
        new_delete_img.attachEvent('onclick', function() {
          removeChecklistItem(new_delete_img);
        });
      }

      var new_td = document.createElement("td");
      new_td.appendChild(new_delete_img);
      new_tr.appendChild(new_td);

      document.getElementById("checklist_item_container").appendChild(new_tr);

    }

    function removeChecklistItem(ele) {
      if ($(ele).parent().prev().children().attr('generated') != "true") {
        if (confirm("Are you sure to remove this checklist Item ? ")) {
          $.getJSON('<?php echo URL::base(); ?>plant/delete_checklist_item/' + $(ele).parent().prev().children().attr('generated'), function(data) {

            alert("Checklist Item is Deleted...");
            $(".hidden_checklist_item_id").each(function() {
              if ($(this).val() == data.id) {
                $(this).remove();
              }
            });
            ele.parentNode.parentNode.parentNode.removeChild(ele.parentNode.parentNode);

          });
        }
      } else {
        ele.parentNode.parentNode.parentNode.removeChild(ele.parentNode.parentNode);
      }
    }



    function deletePicture(picture_id) {
      if (confirm("Are you sure to remove this Picture ? ")) {
        $.getJSON('<?php echo URL::base(); ?>plant/delete_picture/' + picture_id, function(data) {
          var str = "";
          str += "<tr>";
          var j = 0;

          $('#pictures_display').empty();

          if (data.length == 0) {
            $('#pictures_display').append('<td align="center">No Images !</td>');
          }

          for (var i = 0; i < data.length; i++) {
            j++;
            str += '<td>' +
              '	  <img src="<?php echo URL::base(); ?>' + data[i].picture_name + '" height="50" />' +
              '	  <img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="delete_ico" onclick="deletePicture(\'' + data[i].id + '\')" />' +
              '</td>';

            if (j == 5) {
              j = 0;
              str += "</tr><tr>";
            }
          }
          str += "</tr>";
          $('#pictures_display').append(str);
        });
      }
    }

    function deleteDocument(document_id) {
      if (confirm("Are you sure to remove this Document ? ")) {
        $.getJSON('<?php echo URL::base(); ?>plant/delete_document/' + document_id, function(data) {
          var str = "";
          var j = 0;
          $('#documents_display').empty();

          if (data.length == 0) {
            $('#documents_display').append('<tr><td align="center">No Documents !</td><tr/>');
          }

          for (var i = 0; i < data.length; i++) {
            j++;
            str += '<tr><td>' +
              '	  ' + j + ') <a src="<?php echo URL::base(); ?>' + data[i].doc_name + '" height="50" />' + data[i].name + '</a>' +
              '	  <img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="delete_ico" onclick="deleteDocument(\'' + data[i].id + '\')" />' +
              '</td></tr>';
          }
          $('#documents_display').append(str);
        });
      }
    }

    function deleteVideo(video_id) {
      if (confirm("Are you sure to remove this Video ? ")) {
        var copyProgress = $("#progressbar").clone();
        copyProgress.attr('id', 'progressbar1');
        copyProgress.html('<img align="absmiddle" src="<?php echo URL::base(); ?>images/spinner_gray.gif"> Please Wait. Deleting Video...');
        $("#progressbar").after(copyProgress);
        $("#progressbar1").show();
        $.getJSON('<?php echo URL::base(); ?>plant/delete_video/' + video_id, function(data) {
          $("#progressbar1").remove();
          var str = "";
          var j = 0;
          $('#videos_display').empty();

          if (data.length == 0) {
            $('#videos_display').append('<tr><td align="center">No Video !</td><tr/>');
          }

          for (var i = 0; i < data.length; i++) {
            j++;
            str += '<tr><td>' +
              '	  ' + j + ') <a src="https://www.youtube.com/watch?v=' + data[i].video_name + '" target="_blank" height="50" />' + data[i].title + '</a>' +
              '	  <img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="delete_ico" onclick="deleteVideo(\'' + data[i].id + '\')" />' +
              '</td></tr>';
          }
          $('#videos_display').append(str);
        });
      }
    }

    $(function() {
      $('textarea').autogrow();
    });
  </script>
<?php } else { ?>
  <p> You can not add misc.kindly purchase the code.</p>
<?php } ?>