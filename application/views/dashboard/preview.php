 <?php	 $image = "";
		 switch ($profile_type)
		 {
		 	case "1" :// plant
				$profile_name = "Plant & Equipment Profile";
				$profile_desc = "Use this code profile template for a QR Code that provides information for a particular piece of equipment, plant, machine or vehicle, etc.<br>
				Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/plant.png";
				break;
			
			case "2" :// location
				$profile_name = "Location Profile";
				$profile_desc = "Use this code profile template for a QR Code that provides information for a location/site.<br>
				Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/location.png";
				break;
				
			case "3" :// person
				$profile_name = "Person/Business Profile";
				$profile_desc = "Use this code profile template for a QR Code that provides information for a person (eg business card) or a business.<br>
				Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/asset.png";
				break;
				
			case "4" :// product
				$profile_name = "Product Profile";
				$profile_desc = "Use this code profile template for a QR Code that provides information for a product.<br>
				Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/product.png";
				break;
				
			case "5" :// procedure
				$profile_name = "Procedure Profile";
				$profile_desc = "Use this code profile template for a QR Code that provides information for a particular procedure (eg 'How to').<br>
				Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/procedure.png";
				break;
				
			case "8" :// misc
				$profile_name = "Misc Profile";
				$profile_desc = "This is a miscellaneous code profile template that can be used for a variety of applications.<br>
				Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/misc.png";
				break;
				
			case "9" :// url only
				$profile_name = "URL Link Profile";
				$profile_desc = "Use this code profile template for a QR Code that opens a URL of your choice (eg. Your website, etc).<br>
				Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/url_link.png";
				break;
				
			case "10" :// Survey
				$profile_name = "Form/Survey/Checklist Profile";
				$profile_desc = "Use this code profile template for a QR Code that provides access to a form that can be completed on a mobile or tablet.<br>";
				$profile_desc.= "<small>Note: You can also create forms on all of the other code profile templates – a small once only activation fee applys for each profile. <br></small>";
				$profile_desc.= "Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/survey.png";
				break;
				
			case "11" :// exhibit
				$profile_name = "Exhibit Profile";
				$profile_desc = "Use this code profile template for a QR Code that provides information for a particular piece of art or exhibit. This template features extra content tiles and each tile can be moved into different positions providing more flexible layout options.<br>
				Each content tile is optional and the <img src='".URL::base()."images/help_icon.jpg' align='absmiddle' /> icon opens tutorials to explain how to use each one. ";
				
				$image = URL::base()."images/template_preview/exhibit.png";
				break;
			case "12" :// VOC
				$profile_name = "VOCC Profile";
				$profile_desc = "Verification of Competency & Compliance";
				
				$image = URL::base()."images/template_preview/voc.png";
				break;
		 }
		 
?>
<div class="close_btn">&nbsp;</div>
<div class="left-part">
		<?php echo $profile_name;?>
</div>
<div class="right-part">
		<?php echo $profile_desc;?>
</div>
<div class="clear">&nbsp;</div>
<img src="<?php echo $image;?>"/>