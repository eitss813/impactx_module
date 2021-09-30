<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSubcategory.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class = form_head id= "form_head">
	<h3>
		Add a Button to Your Page
	</h3>
</div>
<div class = "error_msg" id = "error_msg">
</div>
<form method="Post" id="form1">
	<div class = main_container id ="main_container">
		<h3>Which Button do you want to people to see?</h3>
		<div class="form1_container">
			<div class="cat">
				<a href="javascript:void(0)" onclick="show_suboption('first')" id='first'>
					<?php echo $this->translate('Make a booking with you');?>
					<img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif" class='icon' border="0" /></a>
					<div class = "bttns" id = "button_first" style="display: none">
						<label><input type="radio" name="label" value="Book Now" <?php echo ($this->label == 'Book Now') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Book Now</label><br>
					</div>
			</div>

			<div class="cat">
				<a href="javascript:void(0)" onclick="show_suboption('second')" id='second'>
					<?php echo $this->translate('Learn More');?>
					<img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif" class='icon' border="0" /></a>
					<div class = "bttns" id = "button_second" style="display: none">
						<label><input type="radio" name="label" value="Watch Video" <?php echo ($this->label == 'Watch Video') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Watch Video</label><br>
						<label><input type="radio" name="label" value="Learn More" <?php echo ($this->label == 'Learn More') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Learn More</label><br>
					</div>
			</div>

			<div class="cat">
				<a href="javascript:void(0)" onclick="show_suboption('third')" id='third'>
					<?php echo $this->translate('Contact You');?>
					<img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif" class='icon' border="0" /></a>
					<div class = "bttns" id = "button_third" style="display: none">
						<label><input type="radio" name="label" value="Contact Us" <?php echo ($this->label == 'Contact Us') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Contact Us</label><br>
						<label><input type="radio" name="label" value="Send Email" <?php echo ($this->label == 'Send Email') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Send Email</label><br>
						<label><input type="radio" name="label" value="Send Message" <?php echo ($this->label == 'Send Message') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Send Message</label><br>
						<label><input type="radio" name="label" value="Sign Up" <?php echo ($this->label == 'Sign Up') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Sign Up</label><br>
					</div>
			</div>

			<div class="cat">
				<a href="javascript:void(0)" onclick="show_suboption('fourth')" id='fourth'>
					<?php echo $this->translate('Shop with you or make a donation');?>
					<img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif" class='icon' border="0" /></a>
					<div class = "bttns" id = "button_fourth" style="display: none">
						<label><input type="radio" name="label" value="Shop Now" <?php echo ($this->label == 'Shop Now') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Shop Now</label><br>
						<label><input type="radio" name="label" value="Donate" <?php echo ($this->label == 'Donate') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Donate</label><br>
					</div>
			</div>
			<div class="cat">
				<a href="javascript:void(0)" onclick="show_suboption('fifth')" id='fifth'>
					<?php echo $this->translate('Play your Game or Download your App');?>
					<img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif" class='icon' border="0" /></a>
					<div class = "bttns" id = "button_fifth" style="display: none">
						<label><input type="radio" name="label" value="Use App" <?php echo ($this->label == 'Use App') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Use App</label><br>
						<label><input type="radio" name="label" value="Play Game" <?php echo ($this->label == 'Play Game') ?  "checked" : "" ;  ?> onclick ="change_bttn()" >Play Game</label><br>
					</div>
			</div>
			<div class="cat">
				<a href="javascript:void(0)" onclick="show_suboption('sixth')" id='Sixth'>
					<?php echo $this->translate('Enter another Name');?>
					<img  src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif" class='icon' border="0" /></a>
					<div class = "bttns" id = "button_sixth" style="display: none">
						<input type="text" name="label1" id ="txt" placeholder="Enter the button name" onkeyup="change_bttn('1')"><br>
					</div>
			</div>
		</div>
		<div class="add_button_butns">
			<button type="button" onclick="form_close()">Close</button>
			<button type="button" onclick="show_next()">Next</button>
		</div>
	</div>
	<div class="sub_form" id = "sub_form" style="display: none">
		<h5>Where would you like to send users when they click this button ?</h5><br>
		<input type="text" name="url" placeholder="Enter a URL" id= "url" value="<?php echo $this->url; ?>"><br>
		<div class="add_button_butns">
			<button type="button" onclick="show_back()">Back</button>
			<button type="button" onclick="validate_data()">Save</button>
		</div>
	</div>
	<div class="form_steps" id ="form_steps">
		Step 1 of 2
	</div>
</form>

<script type="text/javascript">
	window.addEvent('load', function() {
		if($$('input[name=label]:checked').length == 0) {
			$('txt').value = "<?php echo $this->label;?>";
			$('button_sixth').style.display = 'block';
		}
		label = "<?php echo $this->label;?>"
		if(label) {
			if(label == "Book Now")
				show_suboption('first');
			else if(label == "Watch Video" || label == "Learn More")
				show_suboption('second');
			else if(label == "Sign Up" || label == "Send Message" || label == "Send Email" || label == "Contact Us")
				show_suboption('third');
			else if(label == "Shop Now" || label == "Donate")
				show_suboption('second');
			else if(label == "Use App" || label == "Play Game")
				show_suboption('second');
		}

	});
	function show_suboption(add_id)
	{
		if($('button_'+add_id).style.display == 'none') {
			var divsToHide = document.getElementsByClassName("bttns"); 
			for(var i = 0; i < divsToHide.length; i++){
				divsToHide[i].style.display = "none";
			}
			$('button_'+add_id).style.display = 'block';
		}
		else if($('button_'+add_id).style.display == 'block') {
			$('button_'+add_id).style.display = 'none';
		}
	}
	function show_next() {
		var div = document.getElementById('error_msg');
		var steps = document.getElementById('form_steps');
		if($$('input[name=label]:checked').length == 0 && $('txt').value == "") {
			div.innerHTML = "Please select one option";
			div.style.display = 'block';
		} else {
			div.innerHTML = "";
			div.style.display = 'none';
			steps.innerHTML = "Step 2 of 2";
			$('main_container').style.display = 'none';
			$('sub_form').style.display = 'block';
		}
		parent.Smoothbox.instance && parent.Smoothbox.instance.doAutoResize();
	}
	function show_back() {
		var steps = document.getElementById('form_steps');
		steps.innerHTML = "Step 1 of 2"
		$('sub_form').style.display = 'none';
		$('main_container').style.display = 'block';
		parent.Smoothbox.instance && parent.Smoothbox.instance.doAutoResize();
	}
	function validate_data() {
		var div = document.getElementById('error_msg');
		var re = /^(ftp|http|https):\/\/[^ "]+$/;
		if (!re.test($('url').value)) { 
			div.innerHTML = "Enter a valid Url. Example : https://www.example.com";
			div.style.display = 'block';
		} else {
			document.getElementById("form1").submit();
		}
	}
	function form_close() {
		parent.Smoothbox.close();
	}
	function change_bttn(value) {
		if(value == 1 ) {
			var radList = document.getElementsByName('label');
			for (var i = 0; i < radList.length; i++) {
				if(radList[i].checked) radList[i].checked = false;
			}
		} else {
			$('txt').value ="";
		}
	}
</script>
<style type="text/css">
	.error_msg {
		display: none;
	}
</style>