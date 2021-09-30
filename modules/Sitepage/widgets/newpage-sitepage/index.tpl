<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
    include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php if ($this->creationLink && count($this->quickNavigation) > 0): ?>
  <div class="quicklinks">
    <?php
			echo $this->navigation()
            ->menu()
            ->setContainer($this->quickNavigation)
            ->render();
    ?>
  </div>
<?php endif; ?>