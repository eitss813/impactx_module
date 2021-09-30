<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoSettings.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_VideoSwipperSettings extends Engine_Form {

    public function init() {

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this
                ->setTitle('VideoSwiper Settings');

        $isEventModule = Engine_Api::_()->hasModuleBootstrap('siteevent');
        $desTble = $coreSettings->getSetting('sitevideo.videoswipper.destination');
        $tableId = $coreSettings->getSetting('sitevideo.videoswipper.model');
        $title = "";
        if(!empty($desTble) && !empty($tableId)){
            $item = null;
            if($desTble=='channel'){
                $item = Engine_Api::_()->getItem('sitevideo_channel', $tableId);
            }elseif($isEventModule && $desTble=='event'){
                $item = Engine_Api::_()->getItem('siteevent_event', $tableId);
            }
            if($item){
                $title = $item->getTitle();
            }
        }
        $options =array(
                'video' => 'Site ',
                'channel' => 'Channel ',
            );
        if($isEventModule){
                $options['event']= 'Event ';
        }
        $this->addElement('Select', 'sitevideo_videoswipper_destination', array(
            'label' => 'Videos Destination',
            'description' => 'Here, you can choose the destination for mass videos to be uploaded via VideoSwiper. Select ‘Site’ if you want to directly upload mass videos on your website from VideoSwiper without choosing a particular source i.e. Channel / Event.<br />[Note: Uploading mass videos in a particular event is dependent on the integration of Advanced Events Plugin with Advanced Videos / Channels / Playlists Plugin.]',
            'onchange' =>'getDestination(this.value)',
            'multiOptions' => $options,
            'value' => $coreSettings->getSetting('sitevideo.videoswipper.destination', array('channel'))
        ));
        $this->sitevideo_videoswipper_destination
                            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Text', 'model', array(
            'label' => 'Item Title ',
            'description' => 'Please enter the Event or Channel name here based on your above value selection. This is auto suggest text box.',
            'autocomplete' => 'off',
            'value' => $title
            )
        );
        
        $this->addElement('Hidden', 'toValues', array(
            'label' => '',
            'order' => 10001,
            'value' => $coreSettings->getSetting('sitevideo.videoswipper.model'),
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
