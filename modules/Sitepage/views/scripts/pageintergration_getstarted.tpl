<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pageintergration_getstarted.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$viewer = Engine_Api::_()->user()->getViewer();
$addableCheck = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'addable.integration');
$createPrivacy = 1;
$sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules',
'core')->isModuleEnabled('sitepageintegration');
if(!empty($sitepageintegrationEnabled)) :
	$getHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
	$getPageType = Engine_Api::_()->sitepageintegration()->getPageType($getHost);
	if( !empty($getPageType)):
		$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitepageintegration')->getIntegrationItems(null, $isDashboard = true);

		$shownTypesArray = array();
		foreach($mixSettingsResults as $modNameValue):

			// CUSTOM WORK FOR SEPARATNG ADDING AND LINKING OF LISTING
			if ($modNameValue['resource_type'] == 'sitereview_listing') {
				if (strpos($modNameValue['listingtype_id'], 'link') !== false) {
					$modNameValue['listingtype_id'] = str_replace('link', '', $modNameValue['listingtype_id']);
				}  else {
					continue;
				}
				if (in_array($modNameValue['listingtype_id'], $shownTypesArray)) {
					continue;
				}
				$shownTypesArray[] = $modNameValue['listingtype_id'];
			}
			// CUSTOM WORK FOR SEPARATNG ADDING AND LINKING OF LISTING

			if($addableCheck == 1) :
			  $Params = Engine_Api::_()->sitepageintegration()->integrationParams($modNameValue["resource_type"], $modNameValue['listingtype_id']);
				$createPrivacy =  $Params['create_privacy'] ;
			endif;
			if (Engine_Api::_()->sitepage()->hasPackageEnable()) :
				if($createPrivacy) :
					if (Engine_Api::_()->sitepage()->allowPackageContent($this->subject->package_id,
					"modules", $modNameValue["resource_type"] . '_' . $modNameValue['listingtype_id'])) :  ?>
						<li> <?php $canShowMessage = false;?>
							<div class="sitepage_getstarted_num">
								<div>
									<?php echo $i; $i++;?>
								</div>
							</div>
							<div class="sitepage_getstarted_des">
								<?php 
									if ($modNameValue["resource_type"] == 'sitereview_listing') :
									$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $modNameValue['listingtype_id'])->toarray(); 
                  $titleSinUc = ucfirst($listingType['title_singular']);
                  $titleSinLc = strtolower($listingType['title_singular']);
                  ?>
									<b><?php echo $this->translate("$titleSinUc Listings"); ?></b>
								<?php else: ?>
									<b><?php echo $this->translate($modNameValue["item_title"]); ?></b>
								<?php endif; ?>
								<p><?php $item_title = $this->translate(strtolower($modNameValue["item_title"]));
								if ($modNameValue["resource_type"] == 'sitereview_listing') :
									echo $this->translate("Post new $titleSinLc listings to this page."); ?><?php
								else:
									echo $this->translate("Add $item_title to this page.");
								endif;
								?></p>
								<div class="sitepage_getstarted_btn">
									<?php if ($modNameValue["resource_type"] == 'sitereview_listing') : ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'page_id' => $this->page_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitepageintegration_create', true) ?>'><?php echo $this->translate("Post / Manage $titleSinUc Listings"); ?></a>
									<?php else: ?>
                   <?php $item_title = $this->translate($modNameValue["item_title"]);?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'page_id' => $this->page_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitepageintegration_create', true) ?>'><?php echo $this->translate("Add / Manage $item_title");?></a>
									<?php endif; ?>
								</div>
							</div>
						</li>
					<?php endif; ?>
	      <?php	endif;  ?>
			<?php else : ?>
				<?php
				if($createPrivacy) :
					$isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($this->subject,
					$modNameValue["resource_type"] . '_' . $modNameValue['listingtype_id']);
					if (!empty($isPageOwnerAllow)) : ?>
						<li> <?php $canShowMessage = false;?>
							<div class="sitepage_getstarted_num">
								<div>
									<?php echo $i; $i++;?>
								</div>
							</div>
							<div class="sitepage_getstarted_des">
								<?php 
									if ($modNameValue["resource_type"] == 'sitereview_listing') :
									$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $modNameValue['listingtype_id'])->toarray(); 
                  $titleSinUc = ucfirst($listingType['title_singular']);
                  $titleSinLc = strtolower($listingType['title_singular']);
                  ?>
									<b><?php echo $this->translate("$titleSinUc Listings"); ?></b>
								<?php else: ?>
									<b><?php echo $this->translate($modNameValue["item_title"]); ?></b>
								<?php endif; ?>
								<p><?php $item_title = strtolower($modNameValue["item_title"]);
								if ($modNameValue["resource_type"] == 'sitereview_listing') :
									echo $this->translate("Post new $titleSinLc listings to this page."); ?><?php
								else:
									echo $this->translate("Add $item_title to this page.");
								endif;
								?></p>
								<div class="sitepage_getstarted_btn">
									<?php if ($modNameValue["resource_type"] == 'sitereview_listing') : ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'page_id' => $this->page_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitepageintegration_create', true) ?>'><?php echo $this->translate("Post / Manage $titleSinUc Listings"); ?></a>
									<?php else: ?>
                    <?php $item_title = $modNameValue["item_title"];?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'page_id' => $this->page_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitepageintegration_create', true) ?>'><?php echo $this->translate("Add / Manage $item_title");?></a>
									<?php endif; ?>
								</div>
							</div>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif;?>
		<?php	endforeach;  ?>
	<?php endif; ?>
<?php endif;	?>