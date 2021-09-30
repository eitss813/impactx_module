<?php $base_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'synchronous-confirmation')); ?>
<h2>
  <?php echo $this->translate('Directory / Pages Plugin - Configure Plans, Layout and Mapping with Profile Types / Member Levels') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>
<?php endif; ?>

<?php if( count($this->subnavigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->subnavigation)->render();
    ?>
</div>
<?php endif; ?>
<?php 
if( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0  ) 
{
  echo "<ul class='form-errors'><li>Payment gateways not enabled or configured properly.</li></ul>";
  // return ;
}
?>
<?php $option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.view', 1);
if($option == 2): ?>
<form method="post" class="global_form_popup">
<div class="admin_seaocore_files_wrapper">
  <ul class="admin_seaocore_files seaocore_faq">
       <br/>
        <?php echo $this->translate("If you want to autofill the fields for Custom Feature Sets, click on the “Synchronize” Button. Once you click on this button, your previous fields will be deleted and new fields according to the Package Information in the Global Settings, activated template and features of that package will be created for all the available packages."); ?>
        <br/><br/>
    <div class="plan_subscriptions_button">
        <button type="button" name="demo" id="demo" onclick="sendForm(this.id);">
                <?php 
                echo $this->translate('Synchronize'); ?>
        </button>
    </div>
    <br/>
  </ul>
</div>
<?php else:?>
    <div class="tip">
                    <span><?php echo $this->translate("Please enable the Custom option for Package View in the Global Settings "); ?></span>
    </div>
<?php endif;?>
</form>
<script type="text/javascript">     

function sendForm(id) {
    
    //console.log(1);
    //document.getElementById("demo").submit();
    var Obj_Url = '<?php echo $base_url; ?>';
    Smoothbox.open(Obj_Url); 
    
  }   

</script>
