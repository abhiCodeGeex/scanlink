<?php if ($errormessage != "") : ?>
  <div class="row-r"><label class="error"><?php echo $errormessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php if ($successmessage != "") : ?>
  <div class="row-r"><label class="success"><?php echo $successmessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php echo $innermenu; ?>

<section class="search-right">
  <!-- PAGE RIGHT SEARCH BOX -->
  <ul class="form-view">
    <li>
      <label>Sort By</label>
      <select id="sorton" name="sorton" onchange="javascript:getResults(1,document.getElementById('filter').value,this.value,document.getElementById('sortby').value)">
        <option value="">Select sort on option</option>
        <option value="code">Profile No.</option>
        <option value="code_profile_name">Code Profile Name</option>
        <option value="exiprydate">Expiry Date</option>
      </select>
      <div class="AscDec">
        <select id="sortby" name="sortby" onchange="javascript:getResults(1,document.getElementById('filter').value,document.getElementById('sorton').value,this.value)">
          <option value="asc">Asc</option>
          <option value="desc">Desc</option>
        </select>
      </div>
    </li>
    <li>
      <label>Search</label>
      <input type="text" onKeyUp="javascript:getResults(1,this.value,document.getElementById('sorton').value,document.getElementById('sortby').value)" value="" name="filter" id="filter" class="text-fi w-160" />
    </li>
    <li>
      <div id="multi_code_analytic" class="multiple_code_analytic_btn" name="multi_code_analytic"></div>
    </li>
    <?php
    if ($is_sub_user == "0") {
    ?>
      <li>
        <?php if (isset($profile) && !empty($profile)) { ?>
          <input type="button" id="renew_code" name="renew_code" value="Renew Selected Codes" />
        <?php } else { ?>
          <input type="button" disabled="disabled" value="Renew Selected Codes" />
        <?php } ?>
      </li>
    <?php
    } //end of if ($is_sub_user=="0")
    ?>

    <li>
      <?php
      if ($user["access_addcode"] == "1" || $is_sub_user == "0") :
        if (isset($pending_codes) && $pending_codes == 0) :
      ?>
          <a class='inline' href="#inline_content" style="padding: 0;"><input class="inline" type="button" id="addsurvey" value="Add a new code" /></a>
        <?php else : ?>
          <input type="submit" onclick="javascript: window.location.href='<?php echo URL::base(); ?>survey/index'" value="Add a new code" />
      <?php endif;
      endif; ?>

    </li>
    <?php


    echo $code_not_available; ?>
    <?php echo $orderlabel_popup; ?>
    <?php echo $purchase_code_popup; ?>
    <?php echo $renew_code_popup; ?>
    <?php echo $expired_popup; ?>
  </ul>
  <div style="display:none"><a class='expried' href="#noprofileselect" style="padding: 0;" id="noprofiletag"><input class="expried" type="button" id="noprofilebutton" value="expried div" /></a></div>
  <div style='display:none;'>
    <div id='noprofileselect' style='padding:10px; background:#fff;'>
      <div style="float: left;width: 20%">
        <img src="<?php echo URL::base(); ?>images/attention-graphic.jpg" height="50" width="50" />
      </div>
      <span style="float: left;width: 80%;margin-top: 20px;">
        <p>No code profiles have been selected</p>
      </span>

    </div>
  </div>
  <div style="display:none"><a class='expried' href="#profilenotmatch" style="padding: 0;" id="noprofilematchtag"><input class="expried" type="button" id="noprofilematchbutton" value="expried div" /></a></div>
  <div style='display:none;'>
    <div id='profilenotmatch' style='padding:10px; background:#fff;'>
      <div style="float: left;width: 20%">
        <img src="<?php echo URL::base(); ?>images/attention-graphic.jpg" height="50" width="50" />
      </div>
      <span id="span_no_profile_match" style="float: left;width: 80%;margin-top: 10px;"></span>

    </div>
  </div>
</section><!-- /PAGE RIGHT SEARCH BOX -->
<form action="<?php echo URL::base() . 'dashboard/cumulativeAnalytics'; ?>" method="post" name="frmcumulative" id="frmcumulative">
  <input type="hidden" name="cumulative_code_profile" id="cumulative_code_profile">
</form>

<?php

if (isset($profile) && !empty($profile) || true) { ?>

  <?php
  echo Form::open(
    URL::base() . 'code/renewCode',
    array(
      'id' => 'frmdashcode',
      'name' => 'frmdashcode',
      'method' => 'post',
      'enctype' => 'multipart/form-data'
    )
  );
  ?>

  <input type="hidden" name="is_sub_user" id="is_sub_user" value="<?php echo $is_sub_user; ?>" />
  <input type="hidden" name="access_edit" id="access_edit" value="<?php echo $user["access_edit"]; ?>" />
  <input type="hidden" name="access_analytics" id="access_analytics" value="<?php echo $user["access_analytics"]; ?>" />
  <input type="hidden" name="access_form_submission" id="access_form_submission" value="<?php echo $user["access_form_submission"]; ?>" />
  <input type="hidden" name="access_download" id="access_download" value="<?php echo $user["access_download"]; ?>" />
  <input type="hidden" name="access_label" id="access_label" value="<?php echo $user["access_label"]; ?>" />
  <input type="hidden" name="access_log" id="access_log" value="<?php echo $user["access_log"]; ?>" />
  <input type="hidden" name="access_delete" id="access_delete" value="<?php echo $user["access_delete"]; ?>" />
  <input type="hidden" name="checkbox_val" id="checkbox_val">
  <input type="hidden" name="show_profiles_code" id="show_profiles_code" value="<?php echo $user["show_code_profile_id_to_acc_user"]; ?>" />
  <div class="clear"></div>
  <table width="100%" cellpadding="0" cellspacing="0" class="listing-table" id="table">
    <thead>
      <tr>
        <th width="10%" class="left-round" align="center"><strong>Profile No.</strong></th>
        <th width="45%" align="left"><strong class="txt-left">Code Profile Name</strong></th>
        <th width="10%" align="center"><strong class="txt-left">Code expiry date</strong></th>
        <th width="10%" align="center"><strong class="txt-left">Select</strong></th>
        <th width="25%" class="right-round"><strong class="txt-left">Tools</strong></th>
      </tr>
    </thead>
    <tbody id="records">
    </tbody>
  </table>
  <?php echo Form::close() ?>
  <table width="100%" cellpadding="0" cellspacing="0" id="table">
    <tr>
      <td colspan="6" align="center" id="paging"></td>
    </tr>
  </table>



<?php
} else {
  echo '<div class="clear"></div><div style="text-align:center;color:#5286BE;">No Profile Found..!</div>';
}
?>



<script type="text/javascript">
  function remove_temp_files(redirect_url, profile_id, tr_class) {
    if (tr_class != 'red') //i.e. not expired profile
      location.href = '<?php echo URL::base() ?>profile/delete_temp_files/?redirect_url=' + redirect_url + '&profile_id=' + profile_id;
  }

  <?php /* added two parameter in getresults() */ ?>
  $(document).ready(function() {
    getResults(1, '', '', 'asc');
  });

  $('#renew_code').click(function() {

    var isSelected = false;
    $('.selectprofile').each(function() {
      if ($(this).is(':checked')) {
        isSelected = true;
      }
    });
    if (isSelected) {
      $('#frmdashcode').submit();
    } else {
      alert("Please select the survey to be renew.");
      return false;
    }
    return false;
  });

  $('#multi_code_analytic').click(function() {

    var isSelected = false;
    $('.selectprofile').each(function() {
      if ($(this).is(':checked')) {
        isSelected = true;
      }
    });
    var profile = $("#checkbox_val").val();
    if (profile != "") {
      profileIDs = $('#checkbox_val').val().split(',');
      var redProfile = $("#table input:checkbox:checked").map(function() {
        return $(this).closest('tr').attr('class');
      }).get();

      if ($.inArray('red', redProfile) != -1) {
        alert("Please don't select expired profile");
      } else {
        var request = $.ajax({
          url: '<?php echo URL::base(); ?>formbuilder/getCumulativeProfile',
          type: "POST",
          dataType: "json",
          data: {
            profiles: profileIDs,
            params: {
              type_id: 1,
              deleted: 0,
              client_id: <?php echo $client_id ?>
            }
          }
        });
        request.done(function(data) {
          if (data.success) {
            $("#cumulative_code_profile").val(data.success);
            $("#frmcumulative").submit();
          } else {
            $("#span_no_profile_match").html(data);
            $(".expried").colorbox({
              inline: true,
              width: "35%"
            });
            $("#noprofilematchtag").click();
          }
        });
        request.fail(function(jqXHR, textStatus) {
          alert("Request failed: " + textStatus);
        });
      }
    } else {
      $(".expried").colorbox({
        inline: true,
        width: "25%"
      });
      $("#noprofiletag").click();
      return false;
    }
    return false;
  });

  function getResults(page, filter, sorton, sortby) {
    if (sorton == '') {
      sorton = 'code_profile_name';
    }
    var show_profiles_code_values = $('#show_profiles_code').val();
    var request = $.ajax({
      url: '<?php echo URL::base(); ?>test/getData',
      type: "POST",
      dataType: "json",
      data: {
        curpage: page,
        perpage: 50,
        entity: 'profiles',
        filter: filter,
        filter_on: sorton,
        filter_by: sortby,
        show_profiles_code: show_profiles_code_values,
        params: {
          type_id: 10,
          deleted: 0,
          client_id: <?php echo $client_id ?>
        }
      }

    });

    request.done(function(data) {

      var html = "";

      is_sub_user = $('#is_sub_user').val();
      access_edit = $('#access_edit').val();
      access_analytics = $('#access_analytics').val();
      access_form_submission = $('#access_form_submission').val();
      access_download = $('#access_download').val();
      access_label = $('#access_label').val();
      access_log = $('#access_log').val();
      access_delete = $('#access_delete').val();

      for (var i = 0; i < data.results.length; i++) {

        var imgact = data.results[i].approve == 1 ? "active" : "blocked";
        var opp = (imgact == "active") ? "blocked" : "active";

        if (data.results[i].free_code == "1" && data.results[i].renewal_required == "0") {
          exp_date = "N/A";
          my_class = "green";
          checkbox_disabled = 'disabled="disabled"';
        } else {
          exp_date = data.results[i].expired_at;
          my_class = data.results[i].myclass;
          checkbox_disabled = '';
        }

        var onclick_delete = '';
        if (my_class == 'red') {
          onclick_delete = '';
        } else {
          onclick_delete = 'onclick="return confirm(\'Are you sure you want to delete this code?\')"';
        }

        access_count = 0;

        if (access_edit == 1)
          access_count++;

        if (access_analytics == 1)
          access_count++;

        if (access_form_submission == 1)
          access_count++;

        if (access_download == 1)
          access_count++;

        if (access_label == 1)
          access_count++;

        if (access_log == 1)
          access_count++;

        if (access_delete == 1)
          access_count++;


        if (is_sub_user == 0)
          table_width = "100";
        else {
          if (access_count > 5)
            table_width = "100";
          else
            table_width = "60";
        }
        var profile = $("#checkbox_val").val();
        if (profile != "") {
          profileIDs = $('#checkbox_val').val().split(',');
          var checked = "";
          if ($.inArray(data.results[i].id, profileIDs) == -1) {
            checked = "";
          } else {
            checked = "checked=checked";
          }
        }

        //below code check for inactive code
        in_active = "&nbsp;";
        if (data.results[i].activation_start_date != "0000-00-00" && data.results[i].activation_end_date != "0000-00-00") {
          if ((data.results[i].interval_from_start_date * 1) > 0)
            in_active = "<img height='15' src='<?php echo URL::base(); ?>images/blocked.png' alt='Inactive' title='Inactive'>" + " ";
          else if ((data.results[i].interval_from_end_date * 1) < 0)
            in_active = "<img height='15' src='<?php echo URL::base(); ?>images/blocked.png' alt='Inactive' title='Inactive'>" + " ";
        }

        html += '<tr class="' + my_class + '">' +
          '<td><table width="100%"><tr><td width="30%" align="center">' + in_active + '</td><td>' + data.results[i].id + '</td></tr></table></td>' +
          '<td class="product-title">' + data.results[i].code_profile_name + '</td>' +
          '<td align="center">' + exp_date + '</td>' +
          '<td align="center"><input type="checkbox" class="selectprofile" ' + checkbox_disabled + ' id="selectprofile' + data.results[i].id + '" name="selectprofile[]" ' + checked + ' value="' + data.results[i].id + '" /></td>' +
          '<td class="right-border"><table align="center" width="' + table_width + '%" cellspacing="0" cellpadding="0" class="table-n">' +
          '<tbody><tr>';

        if (is_sub_user == 0 || access_edit == 1)
          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip Edit"href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>survey/edit/&quot;,' + data.results[i].id + ',&quot;' + my_class + '&quot;)" title="Edit"></a></td>';

        if (is_sub_user == 0 || access_analytics == 1)
          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip Scanalytics" href="<?php echo URL::base(); ?>dashboard/scanalytics/' + data.results[i].id + '" title="Scanalytics"></a></td>';

        if (is_sub_user == 0 || access_form_submission == 1)
          html += '<td align="center" class="border-none"><a href="<?php echo URL::base(); ?>formbuilder/form_submissions/' + data.results[i].id + '" class="Box_recycle tooltip Submissions" title="Form Submissions"></a></td>';

        if (is_sub_user == 0 || access_download == 1)
          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip Download" href="<?php echo URL::base(); ?>dashboard/getQR/' + data.results[i].qr_id + '" title="Download QR/DM Code"></a></td>';

        if (is_sub_user == 0 || access_label == 1)
          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip OrderLabels" href="<?php echo URL::base(); ?>dashboard/orderLabel/' + data.results[i].qr_id + '" title="Order Labels"></a></td>';

        if (is_sub_user == 0 || access_log == 1)
          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip MSExcel" href="<?php echo URL::base(); ?>dashboard/visitorlog/' + data.results[i].id + '" title="View visitor log"></a></td>';

        if (is_sub_user == 0 || access_delete == 1)
          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip Delete" href="<?php echo URL::base(); ?>survey/delete/' + data.results[i].id + '" title="Delete" ' + onclick_delete + ' ></a></td>';

        html += '</tr></tbody>' +
          '</table></td>' +
          '</tr>';
      }
      $("#records").html(html);
      $("input:checkbox").uniform();
      var html = [];
      for (var i = 0; i < data.links.length; i++) {
        html.push('<a href="javascript:' + data.links[i].href + '" class="' + data.links[i].myClass + '">' + data.links[i].label + '</a>');
      }
      if (data.results.length > 0) {
        $("#paging").html(html.join(""));
      } else {
        $("#paging").html("<h3>No Results Found</h3>");
      }

    });

    request.fail(function(jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
  }
</script>
<script type="text/javascript">
  $(document).ready(function() {
    $(".inline").colorbox({
      inline: true,
      width: "25%"
    });
    $(".register").colorbox({
      inline: true,
      width: "25%"
    });
    <?php if ((isset($orderlabel_popup_show) && $orderlabel_popup_show)) { ?>
      $("#registertag").click();
    <?php } ?>
  });
</script>
<script type="text/javascript">
  $(document).on('click', '.selectprofile', function() {
    //alert(this.checked);
    chkId = this.value;
    if (this.checked == true) {
      id_arr = $('#checkbox_val').val().split(',');
      item_exists = 0;
      for (i = 0; i < id_arr.length; i++) {
        if (id_arr[i] == chkId) {
          item_exists = 1;
          break;
        }
      }

      var tr_class = $(this).closest('div').closest('tr').attr('class');
      if (item_exists == 0 && tr_class != "red") {
        current_id_val = $('#checkbox_val').val();

        if (current_id_val != "") {
          $('#checkbox_val').val(current_id_val + "," + chkId);
        } else {
          $('#checkbox_val').val(chkId);
        }
      }
    } else {
      id_arr = $('#checkbox_val').val().split(',');

      new_id_arr = "";

      for (i = 0; i < id_arr.length; i++) {
        if (id_arr[i] != chkId) {
          if (new_id_arr != "") {
            new_id_arr = new_id_arr + "," + id_arr[i];
          } else {
            new_id_arr = id_arr[i];
          }
        }
      }

      $('#checkbox_val').val(new_id_arr);
    }

  });
</script>