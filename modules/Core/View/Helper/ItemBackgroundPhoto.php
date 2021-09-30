<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: HtmlImage.php 9747 2017-02-22 02:08:08Z john $
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_View_Helper_ItemBackgroundPhoto extends Core_View_Helper_ItemPhoto
{
    protected $_classPrefix = 'bg_';
    protected $_backgroundClass = 'bg_item_photo';

    public function itemBackgroundPhoto($item, $type = 'thumb.profile', $alt = "", $attribs = array())
    {
        $tag = 'span';
        if (!empty($attribs['tag'])) {
            $tag = $attribs['tag'];
            unset($attribs['tag']);
        }

        $this->setAttributes($item, $type, $attribs);
        if ($alt && empty($this->_attribs['title'])) {
            $this->_attribs['title'] = $alt;
        }
        // issue : Error: [] operator not supported for strings in /var/www/html/application/modules/Core/View/Helper/ItemBackgroundPhoto.php:40
        //  if (!empty($this->_attribs['style']) && is_string($this->_attribs['style'])) {
        //    $this->_attribs['style'][] = $this->_attribs['style'];
        //   }
        //
        //  $this->_attribs['style'][] = 'background-image:url("' . $this->_url . '");';

        //fix for the above issue
        $style = $this->_attribs['style'];

        if (!empty($style) && is_string($style)) {
            $this->_attribs['style'] = [];
            $this->_attribs['style'][] = $style;
        }

        if(is_array($this->_attribs['style'])){
            $this->_attribs['style'][] = 'background-image:url("' . $this->_url . '");';
        }else{
            $this->_attribs['style'] = [];
            $this->_attribs['style'][] = $style;
            $this->_attribs['style'][] = 'background-image:url("' . $this->_url . '");';
        }
        //fix for the above issue

        $this->_attribs['class'] = $this->_backgroundClass . ' ' . $this->_attribs['class'];
        return '<' . $tag
        . $this->_htmlAttribs($this->_attribs)
        . '>'
        . '</'
        . $tag
        . '>';
    }
}
