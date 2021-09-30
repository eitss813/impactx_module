<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: statistic.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate('Statistics for directory items / pages'); ?></h3>
        <p class="form-description"> <?php echo $this->translate('Below are some valuable statistics for the Pages submitted on this site.'); ?> </p>
        <table class='admin_table sitepage_admin_statistic' width="100%">
          <tbody>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Pages'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalSitepage) ?></td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Published Pages'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalPublish) ?></td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Pages in Draft'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalDrafted) ?></td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Closed Pages'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalClosed) ?></td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Open Pages'); ?> :</td>
              <td>
                <?php echo $this->locale()->toNumber($this->totalopen) ?>
              </td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Approved Pages'); ?> :</td>
              <td>
                <?php echo $this->locale()->toNumber($this->totalapproved) ?>
              </td>
            </tr>	            
            <tr>
              <td width="45%"><?php echo $this->translate('Total Disapproved Pages'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totaldisapproved) ?></td>
            </tr>            
            <tr>
              <td width="45%"><?php echo $this->translate('Total Featured Pages'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalfeatured) ?></td>
            </tr>            
            <tr>
              <td width="45%"><?php echo $this->translate('Total Sponsored Pages'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalsponsored) ?></td>
            </tr>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Reviews'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalreview) ?></td>
              </tr>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Discussions'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totaldiscussion) ?></td>
              </tr>
            <?php endif; ?>            
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Discussions Posts'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totaldiscussionpost) ?></td>
              </tr>
            <?php endif; ?>            
            <tr>
              <td width="45%"><?php echo $this->translate('Total Photos'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalphotopost) ?></td>
            </tr>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Albums'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalalbumpost) ?></td>
              </tr>
            <?php endif; ?>            
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Notes'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalnotepost) ?></td>
              </tr>
            <?php endif; ?>           
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Events'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totaleventpost) ?></td>
              </tr>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Documents'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totaldocumentpost) ?></td>
              </tr>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Videos'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalvideopost) ?></td>
              </tr>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Polls'); ?> :</td>
                <td>
                  <?php echo $this->locale()->toNumber($this->totalpollpost) ?></td>
              </tr>
            <?php endif; ?>                       
            <tr>
              <td width="45%"><?php echo $this->translate('Total Comments Posts'); ?> :</td>
              <td>
                <?php if (!empty($this->totalcommentpost)) : ?>
                  <?php echo $this->locale()->toNumber($this->totalcommentpost) ?></td>
              <?php else : ?>
                <?php echo 0 ?>
              <?php endif; ?>
            </tr> 

            <tr>
              <td width="45%"><?php echo $this->translate('Total Likes'); ?> :</td>
              <td>
                <?php if (!empty($this->totallikepost)) : ?>
                  <?php echo $this->locale()->toNumber($this->totallikepost) ?></td>
              <?php else : ?>
                <?php echo 0 ?>
              <?php endif; ?>
            </tr> 

            <tr>
              <td width="45%"><?php echo $this->translate('Total Views'); ?> :</td>
              <td>
                <?php if (!empty($this->totalviewpost)) : ?>
                  <?php echo $this->locale()->toNumber($this->totalviewpost) ?></td>
              <?php else : ?>
                <?php echo 0 ?>
              <?php endif; ?>
            </tr>

            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Offers'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalofferpost) ?></td>
              </tr>
            <?php endif; ?> 
                        
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Playlist'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalplaylists) ?></td>
              </tr>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Songs'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalsongs) ?></td>
              </tr>
            <?php endif; ?>
            
		        <?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN. ?>
							<?php
								$sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules',
									'core')->isModuleEnabled('sitepageintegration');
								if(!empty($sitepageintegrationEnabled)) :
								?>
								<?php
								$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' ,
								'sitepageintegration')->getIntegrationItems();
								foreach($mixSettingsResults as $modNameValue): ?>
									<?php if ($modNameValue['resource_type'] == 'sitereview_listing') : ?>
										<?php $countResults = Engine_Api::_()->getDbtable( 'contents' , 'sitepageintegration'
									)->getCountResults($modNameValue['resource_type'], $modNameValue['listingtype_id']); ?>
										<tr>
											<td width="45%"><?php echo $this->translate("Total " . $modNameValue['item_title']); ?> :</td>
											<td><?php echo $this->locale()->toNumber($countResults) ?></td>
										</tr>
									<?php else : ?>
									<?php $countResults = Engine_Api::_()->getDbtable( 'contents' , 'sitepageintegration'
									)->getCountResults($modNameValue['resource_type']); ?>
										<tr>
											<td width="45%"><?php echo $this->translate("Total " . $modNameValue['item_title']); ?> :</td>
											<td><?php echo $this->locale()->toNumber($countResults) ?></td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
            <?php //END FOR INRAGRATION WORK WITH OTHER PLUGIN. ?>

          </tbody>
        </table>
    </form>
  </div>
</div>
