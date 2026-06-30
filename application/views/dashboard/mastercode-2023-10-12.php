<?php if ($errormessage != "") : ?>
  <div class="row-r"><label class="error"><?php echo $errormessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php if (!empty($SuccessMessage)) : ?>
  <div class="row-r"><label class="success"><?php echo $SuccessMessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php echo $innermenu; ?>

<style>
  .loader {
    background: #fff;
    width: 195px;
    height: 350px;
    display: none;
    filter: alpha(opacity=100);
    -moz-opacity: 0.7;
    -khtml-opacity: 0.7;
    opacity: 0.5;
    position: absolute;
  }

  .loader img {
    text-align: center;
    margin: 50% 35%;
  }
</style>
<div class="loader">
  <img src="<?php echo url::base() . 'images/ajax_loader.gif'; ?>" />
</div>
<h4><strong>Master Code List</strong></h4>
<section class="search-right">
  <!-- PAGE RIGHT SEARCH BOX -->
  <ul class="form-view">

    <!--- WI_MS START ---->
    <li>
      <label>Sort By</label>
      <select id="sorton" name="sorton" onchange="javascript:getResults(1,document.getElementById('filter').value,this.value,document.getElementById('sortby').value)">
        <option value="">Select sort on option</option>
        <option value="code">Code</option>
        <option value="code_profile_name">Code Profile Name</option>
        <option value="exiprydate">Expiry Date</option>
      </select>
      <div class="AscDec">
        <select id="sortby" name="sortby" onchange="javascript:getResults(1,document.getElementById('filter').value,document.getElementById('sorton').value,this.value)">
          <option value="asc">Asc</option>
          <option value="desc">Desc</option>
        </select></div>
    </li>
    <!--- WI_MS START ---->
    <li>
      <label>Search</label>
      <input type="text" onKeyUp="javascript:getResults(1,this.value,document.getElementById('sorton').value,document.getElementById('sortby').value)" value="" name="filter" id="filter" class="text-fi searchcodewidth" />
    </li>
    <li>
      <div id="multi_code_analytic" class="multiple_code_analytic_btn" name="multi_code_analytic"></div>
    </li>
    <li>
      <?php if (isset($profile) && !empty($profile)) { ?>
        <input type="button" id="renew_code" name="renew_code" value="Renew Selected Codes" />
      <?php } else { ?>
        <input type="button" disabled="disabled" value="Renew Selected Codes" />
      <?php } ?>
    </li>

    <?php echo $code_not_available; ?>
    <?php echo $orderlabel_popup; ?>
    <?php echo $purchase_code_popup; ?>
    <?php echo $expired_popup;

    $show_code_on_profile = DB::select('*')->from('users')
      ->where('id', '=', $_SESSION["clientuserid"])
      ->order_by('id', 'desc')
      ->execute()
      ->as_array();



    $code_profile_ids  = !empty($show_code_on_profile[0]['show_code_profile_id_to_acc_user']) ? $show_code_on_profile[0]['show_code_profile_id_to_acc_user'] : '';

    $user = $show_code_on_profile[0];

    ?>

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
<?php if (isset($profile) && !empty($profile)) { ?>


  <?php


  echo Form::open(
    URL::base() . 'code/renewCode',
    array(
      'id' => 'frmdashcode',
      'name' => 'frmdashcode',
      'method' => 'post',
      'enctype' => 'multipart/form-data',
    )
  );
  ?>
  <div class="clear"></div>
  <input type="hidden" name="is_sub_user" id="is_sub_user" value="<?php echo $_SESSION['is_sub_user']; ?>" />
  <input type="hidden" name="access_edit" id="access_edit" value="<?php echo $user["access_edit"]; ?>" />
  <input type="hidden" name="access_analytics" id="access_analytics" value="<?php echo $user["access_analytics"]; ?>" />
  <input type="hidden" name="access_form_submission" id="access_form_submission" value="<?php echo $user["access_form_submission"]; ?>" />
  <input type="hidden" name="access_download" id="access_download" value="<?php echo $user["access_download"]; ?>" />
  <input type="hidden" name="access_label" id="access_label" value="<?php echo $user["access_label"]; ?>" />
  <input type="hidden" name="access_log" id="access_log" value="<?php echo $user["access_log"]; ?>" />
  <input type="hidden" name="access_delete" id="access_delete" value="<?php echo $user["access_delete"]; ?>" />
  <input type="hidden" name="checkbox_val" id="checkbox_val">
  <input type="hidden" name="show_profiles_code" id="show_profiles_code" value="<?php echo $user["show_code_profile_id_to_acc_user"]; ?>" />
  <table width="100%" cellspacing="0" cellpadding="0" id="table" class="listing-table">
    <thead>
      <tr>
        <th width="10%" align="center" class="left-round"><strong>Code No.</strong></th>
        <th width="45%" align="left"><strong class="txt-left">Code Profile Name</strong></th>
        <th width="10%" align="center"><strong class="txt-left">Code expiry date</strong></th>
        <th width="10%" align="center"><strong class="txt-left">Select</strong></th>
        <th width="25%" class="right-round">Tools</th>
      </tr>
    </thead>

    <tbody id="records">
    </tbody>

    <tr>
      <td colspan="6" align="center" id="paging"></td>
    </tr>
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
      alert("Please select the code to be renew.");
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
        show_profiles_code: "<?php echo $code_profile_ids ?>",
        params: {
          deleted: 0,
          client_id: <?php echo $client_id ?>
        }
      }

    });

    request.done(function(data) {
      var html = "";

      var is_sub_user = $('#is_sub_user').val();
      var access_edit = $('#access_edit').val();
      var access_analytics = $('#access_analytics').val();
      var access_form_submission = $('#access_form_submission').val();
      var access_download = $('#access_download').val();
      var access_label = $('#access_label').val();
      var access_log = $('#access_log').val();
      var access_delete = $('#access_delete').val();
      var access_count = 0;

      console.log(is_sub_user, access_edit, access_analytics);
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
      for (var i = 0; i < data.results.length; i++) {

        var imgact = data.results[i].approve == 1 ? "active" : "blocked";
        var opp = (imgact == "active") ? "blocked" : "active";
        var myclass = "";


        if (data.results[i].free_code == "1" && data.results[i].renewal_required == "0") {
          exp_date = "N/A";
          my_class = "green";
          checkbox_disabled = 'disabled="disabled"';
        } else {
          exp_date = data.results[i].expired_at;
          my_class = data.results[i].myclass;
          checkbox_disabled = '';
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

        if (data.results[i].code_profile_name != '') {
          html += '<tr class="' + my_class + '">' +
            '<td><table width="100%"><tr><td width="30%" align="center">' + in_active + '</td><td>' + data.results[i].id + '</td></tr></table></td>' +
            '<td class="product-title">' + data.results[i].code_profile_name + '</td>' +
            '<td align="center">' + exp_date + '</td>' +
            '<td align="center"><input type="checkbox" class="selectprofile" ' + checkbox_disabled + ' id="selectprofile' + data.results[i].id + '" name="selectprofile[]" ' + checked + ' value="' + data.results[i].id + '" /></td>' +
            '<td class="right-border"><table width="100%" cellspacing="0" cellpadding="0" class="table-n">' +
            '<tbody><tr>';
        } else {
          html += '<tr class="' + my_class + '">' +
            '<td><table width="100%"><tr><td width="30%" align="center">' + in_active + '</td><td>' + data.results[i].id + '</td></tr></table></td>' +
            '<td class="product-title">' + data.results[i].name + '</td>' +
            '<td align="center">' + exp_date + '</td>' +
            '<td align="center"><input type="checkbox" class="selectprofile" ' + checkbox_disabled + ' id="selectprofile' + data.results[i].id + '" name="selectprofile[]" ' + checked + ' value="' + data.results[i].id + '" /></td>' +
            '<td class="right-border"><table width="100%" cellspacing="0" cellpadding="0" class="table-n">' +
            '<tbody><tr>';
        }
        var edit_url = '<?php echo URL::base(); ?>';
        var delete_url = '<?php echo URL::base(); ?>';

        switch (data.results[i].type_id) {
          case '1':
            edit_url += 'plant/edit/';
            delete_url += 'plant/delete/' + data.results[i].id;
            break;
          case '2':
            edit_url += 'location/edit/';
            delete_url += 'location/delete/' + data.results[i].id;
            break;
          case '3':
            edit_url += 'asset/edit/';
            delete_url += 'asset/delete/' + data.results[i].id;
            break;
          case '4':
            edit_url += 'product/edit/';
            delete_url += 'product/delete/' + data.results[i].id;
            break;
          case '5':
            edit_url += 'procedure/edit/';
            delete_url += 'procedure/delete/' + data.results[i].id;
            break;
          case '6':
            edit_url += 'code/index/';
            delete_url += 'code/delete/' + data.results[i].id;
            break;
          case '7':
            edit_url += 'code/index/';
            delete_url += 'code/delete/' + data.results[i].id;
            break;
          case '8':
            edit_url += 'misc/edit/';
            delete_url += 'misc/delete/' + data.results[i].id;
            break;
          case '9':
            edit_url += 'code/index/';
            delete_url += 'code/delete/' + data.results[i].id;
            break;
          case '10':
            edit_url += 'survey/edit/';
            delete_url += 'survey/delete/' + data.results[i].id;
            break;
          case '11':
            edit_url += 'exhibit/edit/';
            delete_url += 'exhibit/delete/' + data.results[i].id;
            break;
          case '12':
            edit_url += 'voc/edit/';
            delete_url += 'voc/delete/' + data.results[i].id;
            break;
        }
        var onclick_delete = '';
        if (my_class == 'red') {
          onclick_delete = '';
        } else {
          onclick_delete = 'onclick="return confirm(\'Are you sure you want to delete this code?\')"';
        }

        // html += '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;' + edit_url + '&quot;,' + data.results[i].id + ',&quot;' + my_class + '&quot;)" class="Box_recycle tooltip edtplant Edit" title="Edit"></a></td>';


        // html += '<td align="center" class="border-none"><a href="<?php echo URL::base(); ?>dashboard/scanalytics/' + data.results[i].id + '" class="Box_recycle tooltip Scanalytics" title="Scanalytics"></a></td>' +
        //   '<td align="center" class="border-none"><a href="<?php echo URL::base(); ?>formbuilder/form_submissions/' + data.results[i].id + '" class="Box_recycle tooltip Submissions" title="Form Submissions"></a></td>' +
        //   '<td align="center" class="border-none"><a target="_blank" href="<?php echo URL::base(); ?>dashboard/getQR/' + data.results[i].qr_id + '" class="Box_recycle tooltip Download" title="Download QR/DM Code"></a></td>' +
        //   '<td align="center" class="border-none"><a href="<?php echo URL::base(); ?>dashboard/orderLabel/' + data.results[i].qr_id + '" class="Box_recycle tooltip OrderLabels" title="Order Labels"></a></td>' +
        //   '<td align="center" class="border-none"><a class="Box_recycle tooltip MSExcel" href="<?php echo URL::base(); ?>dashboard/visitorlog/' + data.results[i].id + '" title="View visitor log"></a></td>' +
        //   '<td align="center" class="border-none"><a ' + onclick_delete + ' href="' + delete_url + '" class="Box_recycle tooltip Delete" title="Delete"></a></td>' +
        //   '</tr></tbody>' +
        //   '</table></td>' +
        //   '</tr>';
        if (is_sub_user == 0 || access_edit == 1) {
          html += '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;' + edit_url + '&quot;,' + data.results[i].id + ',&quot;' + my_class + '&quot;)" class="Box_recycle tooltip edtplant Edit" title="Edit"></a></td>';
        }


        if (is_sub_user == 0 || access_analytics == 1) {

          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip Scanalytics" href="<?php echo URL::base(); ?>dashboard/scanalytics/' + data.results[i].id + '" title="Scanalytics"></a></td>';
        }

        if (is_sub_user == 0 || access_form_submission == 1) {

          html += '<td align="center" class="border-none"><a href="<?php echo URL::base(); ?>formbuilder/form_submissions/' + data.results[i].id + '" class="Box_recycle tooltip Submissions" title="Form Submissions"></a></td>';
        }

        if (is_sub_user == 0 || access_download == 1) {

          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip Download" href="<?php echo URL::base(); ?>dashboard/getQR/' + data.results[i].qr_id + '" title="Download QR/DM Code"></a></td>';
        }

        if (is_sub_user == 0 || access_label == 1) {

          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip OrderLabels" href="<?php echo URL::base(); ?>dashboard/orderLabel/' + data.results[i].qr_id + '" title="Order Labels"></a></td>';
        }

        if (is_sub_user == 0 || access_log == 1) {

          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip MSExcel" href="<?php echo URL::base(); ?>dashboard/visitorlog/' + data.results[i].id + '" title="View visitor log"></a></td>';
        }

        if (is_sub_user == 0 || access_delete == 1) {

          html += '<td align="center" class="border-none"><a class="Box_recycle tooltip Delete" href="' + delete_url + '" title="Delete" ' + onclick_delete + ' ></a></td>';
        }

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