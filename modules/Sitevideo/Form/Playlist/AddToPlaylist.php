<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddToPlaylist.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Playlist_AddToPlaylist extends Engine_Form {

    public function init() {

        // Init form
        $this
                ->setAttrib('id', 'form-upload')
                ->setAttrib('name', 'playlist_edit')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    }

}
