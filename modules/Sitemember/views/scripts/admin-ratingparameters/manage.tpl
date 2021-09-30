<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<h2><?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Profile Type Based Rating Parameters") ?></h3>
          <table class='admin_table sm_mapping_table' width="100%">
            <thead>
              <tr>
              	<th>
	                <div class="sm_mapping_table_name fleft"><b class="bold"><?php echo $this->translate("Profile Type") ?></b></div>
	                <div class="sm_mapping_table_value fleft"><b class="bold"><?php echo $this->translate("Review Parameters") ?></b></div>
	                <div class="sm_mapping_table_option fleft"><b class="bold"><?php echo $this->translate("Options") ?></b></div>
                </th>
              </tr>
            </thead>
          <tbody>
             
            <?php $ratingParamsTable = Engine_Api::_()->getDbtable('ratingparams', 'sitemember');?>
            <?php $profileTypes = $this->topLevelOptions; ?>
            <?php foreach ($profileTypes as $profileTypeskey => $profileTypesValue): ?>                
              <tr>
                <td>
                  <div class="sm_mapping_table_name fleft">
                    <span><b class="bold"><?php echo $profileTypesValue;?></b></span>
                  </div>                     
									
                  <div class="sm_mapping_table_value fleft">
	                  <ul class="admin-review-cat">
	                    <?php $reviewcat_exist = 0;?>
                       <?php $ratingParameters = $ratingParamsTable->memberParams(array($profileTypeskey), 'user');?>
                       
	                      <?php foreach($ratingParameters as $ratingParams): ?>  
	                        <?php $reviewcat_exist = 1;?>
	                        <li><?php echo $ratingParams['ratingparam_name']; ?></li>
	                      <?php endforeach; ?>
	                  </ul>
	                  <?php if($reviewcat_exist == 0):?>
	                    ---
	                  <?php endif;?>
                	</div>
                	<div class="sm_mapping_table_option fleft">
	                  <?php if($reviewcat_exist < 1):?>
	                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'ratingparameters', 'action' => 'create', 'profiletype_id' => $profileTypeskey), $this->translate('Add'), array(
	                    'class' => 'smoothbox',
	                  )) ?> 
	                  <?php endif; ?>
	
	                  <?php if($reviewcat_exist == 1):?>	
	                   <?php if($reviewcat_exist < 1):?> | <?php endif; ?><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'ratingparameters', 'action' => 'edit', 'profiletype_id' => $profileTypeskey), $this->translate('Edit'), array(
	                      'class' => 'smoothbox',
	                    )) ?>
	
	                    | <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'ratingparameters', 'action' => 'delete', 'profiletype_id' => $profileTypeskey), $this->translate('Delete'), array(
	                      'class' => 'smoothbox',
	                    )) ?>
	                  <?php endif; ?>
	              	</div>    
                </td>
              </tr>
            <?php endforeach; ?>                  
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>