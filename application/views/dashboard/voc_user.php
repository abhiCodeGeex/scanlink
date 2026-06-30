<?php if ($errormessage != "") : ?>
  <div class="row-r"><label class="error"><?php echo $errormessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php if ($successmessage != "") : ?>
  <div class="row-r"><label class="success"><?php echo $successmessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>

<section class="search-right" style="float:left;">
  <!-- PAGE RIGHT SEARCH BOX -->
  <ul class="form-view" id="voc_list">
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
      <input type="text" onKeyUp="javascript:getResults(1,this.value,document.getElementById('sorton').value,document.getElementById('sortby').value)" value="" name="filter" id="filter" class="text-fi" />
    </li>




  </ul>
</section><!-- /PAGE RIGHT SEARCH BOX -->


<?php if (isset($profile) && !empty($profile)) { ?>
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

  <div class="clear"></div>
  <table width="100%" cellpadding="0" cellspacing="0" class="listing-table-voc" id="table">
    <thead>
      <tr>
        <th width="10%" class="left-round" align="center"><strong>Profile No.</strong></th>
        <th width="38%" align="left"><strong class="txt-left">Code Profile Name</strong></th>
        <th width="20%" align="center"><strong class="txt-left">Code Expiry Date</strong></th>
        <th width="10%" align="center"><strong class="txt-left">Select</strong></th>
        <th width="15%" class="right-round"><strong class="txt-left">Tools</strong></th>
        <th width="7%"></th>
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
    location.href = redirect_url + profile_id;
  }

  <?php /* added two parameter in getresults() */ ?>
  $(document).ready(function() {
    getResults(1, '', '', 'asc')

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
      alert("Please select the voc to be renew.");
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
        params: {
          type_id: 12,
          deleted: 0,
          client_id: 0,
          voc_user_id: <?php echo $voc_user_id; ?>
        }
      }
    });

    request.done(function(data) {
      var html = "";

      is_sub_user = document.getElementById('is_sub_user').value;
      access_edit = document.getElementById('access_edit').value;
      access_analytics = document.getElementById('access_analytics').value;
      access_form_submission = document.getElementById('access_form_submission').value;
      access_download = document.getElementById('access_download').value;
      access_label = document.getElementById('access_label').value;
      access_log = document.getElementById('access_log').value;
      access_delete = document.getElementById('access_delete').value;

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

        //below code check for inactive code
        in_active = "&nbsp;";
        if (data.results[i].activation_start_date != "0000-00-00" && data.results[i].activation_end_date != "0000-00-00") {
          if ((data.results[i].interval_from_start_date * 1) > 0)
            in_active = "<img height='15' src='<?php echo URL::base(); ?>images/blocked.png' alt='Inactive' title='Inactive'>" + " ";
          else if ((data.results[i].interval_from_end_date * 1) < 0)
            in_active = "<img height='15' src='<?php echo URL::base(); ?>images/blocked.png' alt='Inactive' title='Inactive'>" + " ";
        }

        html += '<tr >' +
          '<td class="' + my_class + '"><table width="100%"><tr><td  width="30%" align="center">' + in_active + '</td><td>' + data.results[i].id + '</td></tr></table></td>' +
          '<td class="product-title ' + my_class + '">' + data.results[i].code_profile_name + '</td>' +

          '<td align="center" class="' + my_class + '">' + exp_date + '</td>' +
          '<td align="center" class="' + my_class + '"><input type="checkbox" class="selectprofile" ' + checkbox_disabled + ' id="selectprofile' + data.results[i].id + '" name="selectprofile[]" value="' + data.results[i].id + '" /></td>' +
          '<td class="right-border ' + my_class + '"><table align="center" width="' + table_width + '%" cellspacing="0" cellpadding="0" class="table-n">' +
          '<tbody><tr>';

        html += '<td align="center" class="border-none"><a class="Box_recycle tooltip Edit" href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>voc/vocedit/&quot;,' + data.results[i].id + ',&quot;' + my_class + '&quot;)"  title="Edit"></a></td>';



        html += '</tr></tbody>' +
          '</table></td>';
        html += '<td style="border:none;">';
        if (data.results[i].voc_doc_expired > 0) {
          html += '<img src="<?php echo URL::base(); ?>images/expired_doc.png" alt="A document has expired" title="A document has expired">&nbsp;';
        }
        if (data.results[i].voc_doc_near_expiry_count > 0) {
          html += '<img src="<?php echo URL::base(); ?>images/near_expiry_doc.png" alt="A document will expire in 30 days or less" title="A document will expire in 30 days or less">';
        }
        html += '</td>';

        html += '</tr>';
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

  function export_data() {
    location.href = '<?php echo URL::base() ?>voc/export_voc_document/';
  }
</script>