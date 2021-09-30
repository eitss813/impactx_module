<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteloginconnect
 * @copyright  Copyright 2017-2018 SocialEngineAddons
 * @license    http://www.socialengine.com/license/
 * @version    $Id: map.tpl 9747 2018-02-21 02:08:08Z SocialEngineAddons $
 * @author     SocialEngineAddons
 */
?>
<h2>
    <?php echo $this->translate("Social Connect & Profile Sync Extension") ?>
</h2>

<?php if( count($this->navigation) ): ?>
      <div class='seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
<?php endif; ?>
<style>
.accordion {
    background-color: #eee;
    color: #444;
    cursor: pointer;
    padding: 18px;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    font-size: 15px;
    margin-top: 20px;
    transition: 0.4s;
}

.activetab, .accordion:hover {
    background-color: #ccc;
}

.accordion:after {
    content: '\002B';
    color: #777;
    font-weight: bold;
    float: right;
    margin-left: 5px;
}

.activetab:after {
    content: "\2212";
}
[id^=Select_] {
    padding: 0 18px !important;
    background-color: white;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.2s ease-out;
}
</style>
<script type="text/javascript">

window.addEvent('domready', function() {
	var acc = document.getElementsByClassName("accordion");
	var i;
	for (i = 0; i < acc.length; i++) {
	  acc[i].addEventListener("click", function() {
	    this.classList.toggle("activetab");
	    var panel = this.nextElementSibling;
	    if (panel.style.maxHeight){
	      panel.style.maxHeight = null;
	    } else {
	      panel.style.maxHeight = panel.scrollHeight + "px";
	    } 
	  });
	}  
});


function selectSocialSite(el) {
	console.log(el.value);
    $("profile_type").value ='';
	$("Siteloginconnect_map_profilefields").submit();
}

function saveMapping() {
	$("Siteloginconnect_map_profilefields").submit();
}

function changefieldvalues(el){

	var elid=el.id;
	var profileid=elid.split('_')[1];

	if($("profile_type").value != "") {
			selectedProfileTypes = $("profile_type").value.split(",");
			selectedProfileTypes.push(profileid);
			$("profile_type").value = selectedProfileTypes.join(",");
	} else {
		$("profile_type").value=profileid;
	}
}
</script>

<div class="settings">
	<?php echo $this->form->render($this); ?>
</div>