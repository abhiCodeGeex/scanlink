	<div class="MobileInnerList">
		<ul>
			<?php if(count($documents)!=0){ ?>
    	<?php foreach($documents as $document): ?>
				<li><a href="<?php echo url::base().$document['doc_name']; ?>"><?php echo $document["name"]; ?></a></li>
      <?php endforeach; ?>
	    <?php }else{ ?>
				<li>No Documents</li>
	    <?php } ?>
		</ul>
	</div>