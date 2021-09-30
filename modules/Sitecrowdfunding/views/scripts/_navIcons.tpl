<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _navIcons.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul>
    <?php foreach ($this->container as $link): ?>
        <li>
            <?php $data_smoothboxValue = ''; ?>
            <?php if (strpos($link->getClass(), 'data_SmoothboxSEAOClass') !== false): ?>
                <?php
                echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
                    'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
                    'style' => 'background-image: url(' . $link->get('icon') . ');',
                    'target' => $link->get('target')
                ));
                ?>
            <?php else: ?>
                <?php
                echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
                    'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
                    'style' => 'background-image: url(' . $link->get('icon') . ');',
                    'target' => $link->get('target'),
                ));
                ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>