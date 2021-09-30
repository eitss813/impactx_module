<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _navIcons.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<ul>
    <?php foreach ($this->container as $link): ?>
        <li>
            <?php $data_smoothboxValue = ''; ?>
            <?php if (strpos($link->getClass(), 'data_SmoothboxSEAOClass') !== false): ?>

                <?php if (Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()): ?>
                    <?php
                    echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
                        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
                        'style' => 'background-image: url(' . $link->get('icon') . ');',
                        'target' => $link->get('target'),
                        'data-SmoothboxSEAOClass' => 'seao_add_photo_lightbox'
                    ));
                    ?>
                <?php else: ?>
            
            <?php 
                $smoothboxClass = @explode(' ', $link->getClass());
                
                if(in_array('seao_smoothbox', $smoothboxClass)) {
                   unset($smoothboxClass[1]);
                   unset($smoothboxClass[0]);
                   $class = implode(' ' , $smoothboxClass);
                   
                } else {
                    $class = $link->getClass();
                }
                ?>
                    <?php
                    echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
                        'class' => 'buttonlink' . ( $class ? ' ' . $class : '' ),
                        'style' => 'background-image: url(' . $link->get('icon') . ');',
                        'target' => $link->get('target')
                    ));
                    ?>
                <?php endif; ?>
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