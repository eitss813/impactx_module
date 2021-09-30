<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?> 
    <div class="headline sesblog_browse_menu">
  <?php $countMenu = 0; ?>
  <?php if( count($this->navigation) > 0): ?>
    <div class="tabs">
    <h2><?php echo (isset($this->params['title'])) ? $this->translate($this->params['title']) : $this->translate('Blogs'); ?></h2>
      <ul class="navigation">
        <?php foreach( $this->navigation as $navigationMenu ): ?>
          <?php if( $countMenu < $this->max ): ?>
            <li <?php if ($navigationMenu->active): ?><?php echo "class='active'";?><?php endif; ?>>
              <?php if ($navigationMenu->action): ?>
                <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo empty($navigationMenu->uri) ? $this->url(array('action' => $navigationMenu->action), $navigationMenu->route, true) : $navigationMenu->uri ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
              <?php else : ?>
                <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo empty($navigationMenu->uri) ? $this->url(array(), $navigationMenu->route, true) : $navigationMenu->uri ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
              <?php endif; ?>
            </li>
          <?php else:?>
            <?php break;?>
          <?php endif;?>
          <?php $countMenu++;?>
        <?php endforeach; ?>
        <?php if (count($this->navigation) > $this->max): ?>
          <?php $countMenu = 0; ?>
          <li class="sesbasic_browse_nav_tab_closed sesbasic_browse_nav_pulldown" onclick="moreTabSwitch($(this));">
            <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
            <div class="tab_pulldown_contents_wrapper sesbasic_bxs">
              <div class="tab_pulldown_contents">
                <ul>
                  <?php foreach( $this->navigation as  $navigationMenu ): ?>
                    <?php if ($countMenu >= $this->max): ?>
                      <?php $urlNavigation = empty($navigationMenu->uri) ? $this->url(array('action' => $navigationMenu->action), $navigationMenu->route, true) : $navigationMenu->uri ?>
                      <?php $http_https = isset($_SERVER['HTTPS']) ? 'https://' : 'http://'; ?>
                      <li <?php if ($navigationMenu->active): ?><?php echo "class='active'";?><?php endif; ?> <?php if ($urlNavigation == "$http_https$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"): ?><?php echo "class='active'";?><?php endif; ?>  >
                      <?php if ($navigationMenu->action): ?>
                        <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo $urlNavigation ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
                      <?php else : ?>
                        <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo empty($navigationMenu->uri) ? $this->url(array(), $navigationMenu->route, true) : $navigationMenu->uri ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
                      <?php endif; ?>
                      </li>
                    <?php endif;?>
                    <?php $countMenu++;?>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </li>
        <?php endif;?>
      </ul>
    </div>
     <?php if($this->createButton && $this->createBlog) { ?>
          <div class="sesblog_create_right_btn"><a href="<?php echo $this->url(array('action' => 'create'), 'sesblog_general', true); ?>"><?php echo $this->translate("Write New Blog"); ?></a></div>
        <?php } ?>
  <?php endif; ?>
  </div>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var moreTabSwitch = window.moreTabSwitch = function(el) {
      el.toggleClass('sesbasic_browse_nav_tab_open');
      el.toggleClass('sesbasic_browse_nav_tab_closed');
    }
  });
</script>
