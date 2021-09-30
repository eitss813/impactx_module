<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitepage_admin_popup"> 
  <div>
    <h3><?php echo $this->translate('Page Details'); ?></h3>
    <br />
    <table cellpadding="0" cellspacing="0" class="sitepage-view-detail-table">
      <tr>
        <td>
          <table cellpadding="0" cellspacing="0" width="350">
            <tr>
              <td width="120"><b><?php echo $this->translate('Title:'); ?></b></td>
              <td>
                 <?php echo $this->htmlLink($this->item('sitepage_page', $this->sitepageDetail->page_id)->getHref(), $this->translate($this->sitepageDetail->title), array('target' => '_blank')) ?>&nbsp;&nbsp;

              </td>
            <tr>
              <td><b><?php echo $this->translate('Owner:'); ?></b></td>
              <td>
                <?php echo $this->htmlLink($this->sitepageDetail->getOwner()->getHref(), $this->sitepageDetail->getOwner()->getTitle(), array('target' => '_blank')) ?>
              </td>
            </tr>

            <?php if ($this->manageAdminEnabled): ?>
              <tr>
                <td><b><?php echo $this->translate('Total Admins:'); ?></b></td>
                <td><?php echo $this->admin_total; ?></td>
              </tr>
            <?php endif; ?>

            <?php //if ($this->sitepageDetail->member_count): ?>
            <?php $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember'); ?>
            <?php if ($sitepagememberEnabled): ?>
              <tr>
                <td><b><?php echo $this->translate('Total Members:'); ?></b></td>
                <td><?php echo $this->sitepageDetail->member_count; ?></td>
              </tr>
             <?php endif; ?>
            <?php //endif; ?>

            <tr>
              <?php if ($this->category_name != '') : ?>
                <td><b><?php echo $this->translate('Category:'); ?></b></td> 
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name), array('target' => '_blank')) ?>
                </td>	    
              <?php endif; ?>
            </tr>	
            <tr>
              <?php if ($this->subcategory_name != '') : ?>
                <td><b><?php echo $this->translate('Subcategory:'); ?></b></td> 
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name)), 'sitepage_general_subcategory'), $this->translate($this->subcategory_name), array('target' => '_blank')) ?>
                </td>	    
              <?php endif; ?>
            </tr>
            <tr>
              <?php if ($this->subsubcategory_name != '') : ?>
                <td><b><?php echo $this->translate('3%s Level Category:', "<sup>rd</sup>"); ?></b></td>
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subsubcategory_name)), 'sitepage_general_subsubcategory'), $this->translate($this->subsubcategory_name), array('target' => '_blank')) ?>
                </td>
              <?php endif; ?>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Creation Date:'); ?></b></td>
              <td>
                <?php echo $this->translate(gmdate('M d,Y g:i A', strtotime($this->sitepageDetail->creation_date))); ?>
              </td>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Approved:'); ?></b></td>
              <td>
                <?php
                if ($this->sitepageDetail->approved)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");
                ?>
              </td>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Approved Date:'); ?></b></td>
              <td>
                <?php if (!empty($this->sitepageDetail->aprrove_date)): ?>
                  <?php echo $this->translate(date('M d,Y g:i A', strtotime($this->sitepageDetail->aprrove_date))); ?>
                <?php else: ?>
                  <?php echo $this->translate('-'); ?>
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Featured:'); ?></b></td>
              <td> <?php
                if ($this->sitepageDetail->featured)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");?>
              </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Sponsored:'); ?></b></td>
              <td> <?php
                if ($this->sitepageDetail->sponsored)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");?>
              </td>
            </tr>
            <?php $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1); ?>
            <?php if ($this->sitepageDetail->price && $enablePrice): ?>
              <tr>
                <td><b><?php echo $this->translate('Price:'); ?></b></td>
                <td><?php echo $this->locale()->toCurrency($this->sitepageDetail->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></td>
              </tr>
            <?php endif; ?>
            <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1); ?>
            <?php if ($this->sitepageDetail->location && $enableLocation): ?>
              <tr>
                <td><b><?php echo $this->translate('Location:'); ?></b></td>
                <td><?php echo $this->sitepageDetail->location ?></td>
              </tr>
            <?php endif; ?>

            <tr>
              <td><b><?php echo $this->translate('Views:'); ?></b></td>
              <td><?php echo $this->translate($this->sitepageDetail->view_count); ?> </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Comments:'); ?></b></td>
              <td><?php echo $this->translate($this->sitepageDetail->comment_count); ?> </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Likes:'); ?></b></td>
              <td><?php echo $this->translate($this->sitepageDetail->like_count); ?> </td>
            </tr>

            <?php if ($this->isEnabledSitepagereview && $this->sitepageDetail->rating > 0): ?>

            <tr>
              <td><b><?php echo $this->translate('Reviews:'); ?></b></td>
              <td><?php echo $this->translate($this->sitepageDetail->review_count); ?> </td>
            </tr>

              <?php
              $currentRatingValue = $this->sitepageDetail->rating;
              $difference = $currentRatingValue - (int) $currentRatingValue;
              if ($difference < .5) {
                $finalRatingValue = (int) $currentRatingValue;
              } else {
                $finalRatingValue = (int) $currentRatingValue + .5;
              }
              ?>

              <tr>           
                <td><b><?php echo $this->translate('Rating:'); ?></b></td>
                <td> <?php for ($x = 1; $x <= $this->sitepageDetail->rating; $x++): ?>
                    <span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>"></span>
                  <?php endfor; ?>
                  <?php if ((round($this->sitepageDetail->rating) - $this->sitepageDetail->rating) > 0): ?>
                    <span class="rating_star_generic rating_star_half" title="<?php echo $this->sitepageDetail->rating . $this->translate(' rating'); ?>"></span>
                  <?php endif; ?>
                  <?php if (empty($this->sitepageDetail->rating))
                  echo $this->translate("-"); ?>
                </td>
              </tr>
              <?php endif; ?>
          </table>
        </td>
        <td align="right">
<?php echo $this->htmlLink($this->sitepageDetail->getHref(), $this->itemPhoto($this->sitepageDetail, 'thumb.icon', '', array('align' => 'right')), array('target' => '_blank')) ?>
        </td>	
      </tr>
    </table>		
    <br />
    <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
  </div>
</div>	

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>