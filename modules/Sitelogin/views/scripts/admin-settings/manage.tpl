<h2>
    <?php echo $this->translate("Social Login and Sign-up Plugin") ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
   </div>
<?php endif; ?>
<h3>
  Manage Social Sites Services
</h3>
<p>
  Here, you can manage all the integrated social sites altogether. Enable / Disable for the Login / Signup Pages and Quick Signup individualy from the form given below : 
</p>
<br />

<br />
<?php if(!empty($this->result)): ?>
<form id='manage_services_form' method="post" action="<?php echo $this->url();?>"  >
		<div id="error_box" style="color:red;"></div>
		<table class='admin_table' width="100%">
			<thead>
				<tr>
					<th><?php echo $this->translate("Service Name") ?></th>
                                        <th align="center"><?php echo $this->translate("Integrated") ?></th>
					<th align="center"><?php echo $this->translate("Show on Login") ?></th>
					<th align="center"><?php echo $this->translate("Show on Signup") ?></th>
					<th align="center"><?php echo $this->translate("Quick Signup") ?></th>
					<th align="center"><?php echo $this->translate("Options") ?></th>

				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->result as $key => $value):?>
                                
					<tr>
						<td><?php echo $this->socialSites[$key]; ?></td>
	
						<!--        If module enable then display disable and vice-versa.-->
						<td class="admin_table_centered">
                                                    <?php echo ( ($value['enable']) ? $this->translate('Yes'):$this->translate('No')) ?>
						</td>
                                                
						<td class="admin_table_centered">
                                                    <?php if(isset($value['login']) && $value['login']): ?>
                                                        <input type="checkbox" name="<?php echo $key?>_login" id="<?php echo $key?>_login" checked="checked" >
                                                    <?php else: ?>
                                                        <input type="checkbox" name="<?php echo $key?>_login" id="<?php echo $key?>_login" >
                                                    <?php endif; ?>
                                                </td>
                                                
						<td class="admin_table_centered">
                                                    <?php if(isset($value['signup']) && $value['signup']): ?>
                                                        <input type="checkbox" name="<?php echo $key?>_signup" id="<?php echo $key?>_signup" checked="checked">
                                                    <?php else: ?>
                                                        <input type="checkbox" name="<?php echo $key?>_signup" id="<?php echo $key?>_signup" >
                                                    <?php endif; ?>
                                                </td>
						<td class="admin_table_centered">
                                                    <?php if(in_array($key,array('twitter','instagram','flickr','yahoo','pinterest'))): 
                                                        echo $this->translate("Option Not Available");
                                                    else: 
                                                        if(isset($value['quickenable']) && $value['quickenable']): ?>
                                                            <input type="checkbox" name="<?php echo $key?>_quicksignup" id="<?php echo $key?>_quicksignup" checked="checked">
                                                        <?php else: ?>
                                                            <input type="checkbox" name="<?php echo $key?>_quicksignup" id="<?php echo $key?>_quicksignup" >
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
						<td class="admin_table_centered">
                                                    <?php $URL = $this->baseUrl() . "/admin/sitelogin/settings/".$key;
                                                    ?>
                                                    <a href="<?php echo $URL ?>" target="_blank">Edit</a> 
						</td>
		
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<center>
			<div id="submit-wrapper" class="form-wrapper">
				<div id="submit-label" class="form-label">&nbsp;</div>
				<div id="submit-element" class="form-element">
					<button name="submit" id="submit" type="submit">Save Changes</button>
				</div>
			</div>
		</center>
		<br />
	</form>
<?php endif; ?>
