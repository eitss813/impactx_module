<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _navIcons.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>

<ul>
  <?php foreach( $this->container as $link ): ?>
    <li>
      <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
        'style' => $link->get('icon') ? 'background-image: url('.$link->get('icon').');' : '',
        'target' => $link->get('target'),
      )) ?>
    </li>
  <?php endforeach; ?>
</ul>
