<style type="text/css">#product_display td { font-size:12px; vertical-align:top; }
  #product_display{ border:1px solid black; margin-top:10px; margin-bottom:10px; }
  #product_display td{ border-bottom: 1px solid #CCC; }

</style>
<br />
<br />
<br />
<table border="0" cellpadding="5" cellspacing="1" align="center" width="80%" id="product_display">

  <tr>
    <td width="200">Profile Type</td>
    <td><?php echo $eq_type[0]["name"]; ?></td>
  </tr>

  <tr>
    <td>Company logo</td>
    <td><?php if (isset($post["logo"]) && isset($post["logo"]["logo_name"]) && $post["logo"]["logo_name"] != "") { ?><img src="<?php echo url::base() . "../" . $post["logo"]["logo_name"]; ?>" height="30" alt="<?php echo $post['name'] ?>" style="margin:5px" /><?php } else {
  echo "No Logo";
} ?></td>
  </tr>

  <tr>
    <td>Name :</td>
    <td><?php echo $post['name']; ?></td>
  </tr>

<?php if (isset($post['identification'])) { ?>
    <tr>
      <td>Identification</td>
      <td><?php echo $post['identification']; ?></td>
    </tr>
  <?php } ?>

<?php if (isset($post['serial_no'])) { ?>
    <tr>
      <td>Serial No.</td>
      <td><?php echo $post['serial_no']; ?></td>
    </tr>
  <?php } ?>

<?php if (isset($post['address'])) { ?>
    <tr>
      <td>Address</td>
      <td><?php echo $post['address']; ?></td>
    </tr>
<?php } ?>
  <tr>
    <td>Description</td>
    <td><?php echo $post['description']; ?></td>
  </tr>
  <tr>
    <td>Notes</td>
    <td><?php echo $post['notes']; ?></td>
  </tr>
  <tr>
    <td>Key Contact Name</td>
    <td><?php echo $post['name_company']; ?></td>
  </tr>
  <tr>
    <td>Telephone</td>
    <td><?php echo $post['telephone']; ?></td>
  </tr>
  <tr>
    <td>Pictures</td>
    <td><fieldset><table border="0" cellpadding="0" cellspacing="5" id="pictures_display" >
          <tr>
            <?php if (count($post["pictures"]) == 0): ?>
              <td align="center">No Images !</td>
<?php endif; ?>
            <?php $i = 0;
            foreach ($post["pictures"] as $picture): $i++; ?>

              <td><img src="<?php echo url::base() . "../" . $picture["picture_name"]; ?>" height="50" /></td>
              <?php if ($i == 5): $i = 0; ?>
              </tr><tr>
  <?php endif; ?>
<?php endforeach; ?>
          </tr>
        </table></fieldset></td>
  </tr>
  <tr>
    <td>Documents</td>
    <td><fieldset><table border="0" cellpadding="0" cellspacing="5" id="documents_display" >
          <?php if (count($post["documents"]) == 0): ?>
            <tr>
              <td align="center">No Documents !</td>
            </tr>
          <?php endif; ?>
<?php $i = 0;
$num = 0;
foreach ($post["documents"] as $document): $i++; ?>
            <tr><td><?php echo $i; ?>) <a href="<?php echo url::base() . "../" . $document["doc_name"]; ?>" ><?php echo $document["name"]; ?></a></td></tr>
          <?php endforeach; ?>
        </table></fieldset></td>
  </tr>
  <tr>
    <td>Videos</td>
    <td><fieldset><table border="0" cellpadding="0" cellspacing="5" id="videos_display" >
          <?php if ((isset($post["videos"]) && count($post["videos"]) == 0) || !isset($post["videos"])): ?>
            <tr>
              <td align="center">No Videos !</td>
            </tr>
<?php else: ?>
  <?php $i = 0;
  $num = 0;
  foreach ($post["videos"] as $video): $i++; ?>
              <tr><td><?php echo $i; ?>) <a href="<?php echo "http://www.youtube.com/watch?v=" . $video["video_name"]; ?>" target="_blank" >Video <?php echo $i; ?></a></td></tr>
        <?php endforeach; ?>
      <?php endif; ?>
        </table></fieldset></td>
  </tr>
  <tr>
    <td>Password protect?</td>
    <td><?php
      if ($yes)
        echo "YES";
      else
        echo "NO";
      ?></td>
  </tr>
<?php if (isset($qrimg) && isset($qrimg['qrimg_name'])) { ?>
    <tr>
      <td>QR Img</td>
      <td><img src="<?php echo isset($qrimg) && url::base() . "../" . $qrimg['qrimg_name'] ? url::base() . "../" . $qrimg['qrimg_name'] : ''; ?>" alt="" /></td>
    </tr>
<?php } ?>
</table>
<br /><br />