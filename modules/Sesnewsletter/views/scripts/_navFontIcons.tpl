<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _navFontIcons.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>

<ul class="navigation">
  <?php foreach( $this->container as $link ): ?>
    <li class="<?php echo $link->get('active') ? 'active' : '' ?>">
      <a href='<?php echo $link->getHref() ?>' class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
        <?php if( $link->get('target') ): ?>target='<?php echo $link->get('target') ?>' <?php endif; ?> >
        <i class="fa <?php echo $link->get('icon') ? $link->get('icon') : 'fa-star' ?>"></i>
        <span><?php echo $this->translate($link->getlabel()) ?></span>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
