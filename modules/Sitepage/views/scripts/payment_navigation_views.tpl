<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment_navigation_views.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$this->max = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageshow.navigation.tabs', 8);
$headding = "PAGE_NAVIGATION_NAME";
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>


<div class="headline">
  <h2>
<?php echo $this->translate($headding); ?>
  </h2>
    <?php if (count($this->navigation)) { ?>
    <div class='tabs'>
    <?php //echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
      <ul class='navigation'>
      <?php $key = 0; ?>
        <?php foreach ($this->navigation as $nav): ?>			
          <?php if ($key < $this->max): ?>
            <li 
            <?php if ($nav->active): echo "class='active'";
            endif; ?> >
              <?php if ($nav->route == 'sitepage_general' || $nav->action): ?>
                <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
              <?php else : ?>
                <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
              <?php endif; ?>
            </li>
          <?php else: ?>
            <?php break; ?>
          <?php endif; ?>
          <?php $key++ ?>
        <?php endforeach; ?>

        <?php if (count($this->navigation) > $this->max): ?>
          <li class="tab_closed more_tab" onclick="moreTabSwitchSitepage($(this));">
            <div class="tab_pulldown_contents_wrapper">
              <div class="tab_pulldown_contents">          
                <ul>
                  <?php $key = 0; ?>
                  <?php foreach ($this->navigation as $nav): ?>
                    <?php if ($key >= $this->max): ?>
                      <li <?php if ($nav->active): echo "class='active'";
              endif; ?> >
                        <?php if ($nav->route == 'sitepage_general' || $nav->action): ?>
                          <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                        <?php else : ?>
                          <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                        <?php endif; ?>
                      </li>
                    <?php endif; ?>
                    <?php $key++ ?>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  <?php } ?>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function() {
   
    var moreTabSwitchSitepage = window.moreTabSwitchSitepage = function(el) {
      el.toggleClass('seaocore_tab_open active');
      el.toggleClass('tab_closed');
    }
  });
</script>
