<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Specialchannels.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_Widget_Specialchannels extends Engine_Form {

    public function init() {

        $this->setMethod('post');
        $this->setTitle('Special Channels')
                ->setDescription('Displays special Channels as selected by you from the auto-suggest box below.');


        //SHOW PREFIELD START AND END DATETIME
        $httpReferer = $_SERVER['HTTP_REFERER'];
        $params = $toValues = $toValuesArray = array();
        $toValuesString = '';
        if (!empty($httpReferer) && strstr($httpReferer, '?page=')) {
            $httpRefererArray = explode('?page=', $httpReferer);
            $page_id = (int) $httpRefererArray['1'];
        } elseif (!empty($httpReferer) && strstr($httpReferer, 'admin/content') && !strstr($httpReferer, 'admin/content?')) {
            $page_id = 3; //FOR HOME PAGE
        }

        if (!empty($page_id) && is_numeric($page_id)) {

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');

            //GET CONTENT
            $params = $tableContent->select()
                    ->from($tableContentName, array('params'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'sitevideo.special-channels')
                    ->query()
                    ->fetchColumn();

            if (!empty($params)) {
                $params = Zend_Json_Decoder::decode($params);
            }
        }

        $this->addElement('Text', 'channel_ids', array(
            'autocomplete' => 'off',
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '/application/modules/Sitevideo/views/scripts/admin-settings/add-special-channels.tpl',
                        'thisObject' => $this,
                        'class' => 'form element')))
        ));
        Engine_Form::addDefaultDecorators($this->channel_ids);

        $this->addElement('Hidden', 'toValues', array(
            'label' => '',
            'order' => 10001,
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        // Start time
        $start = new Engine_Form_Element_CalendarDateTime('starttime');
        $start->setLabel("Start Time");
        $start->setAllowEmpty(false);
        $this->addElement($start);

        // End time
        $end = new Engine_Form_Element_CalendarDateTime('endtime');
        $end->setLabel("End Time");
        $end->setAllowEmpty(false);
        $this->addElement($end);

        if (isset($params['starttime']) && !empty($params['starttime'])) {
            $start->setValue($params['starttime']);
        }

        if (isset($params['endtime']) && !empty($params['endtime'])) {
            $end->setValue($params['endtime']);
        }

        $this->addElement(
                'Text', 'columnWidth', array(
            'label' => 'Column width for Grid View.',
            'value' => '180',
                )
        );
        $this->addElement(
                'Text', 'columnHeight', array(
            'label' => 'Column height for Grid View.',
            'value' => '328',
                )
        );
        $this->addElement(
                'MultiCheckbox', 'channelInfo', array(
            'label' => 'Choose the options that you want to display for the Channels in this block.',
            'multiOptions' => array_merge(array(
                'title' => 'Channel Title',
                'owner' => 'Owner',
                'like' => 'Likes',
                'comment' => 'Comments',
                'favourite' => 'Favourite',
                'numberOfVideos' => 'Number of Videos',
                'subscribe' => 'Subscribe',
                'facebook' => 'Facebook [Social Share Link]',
                'twitter' => 'Twitter [Social Share Link]',
                'linkedin' => 'LinkedIn [Social Share Link]',
                'googleplus' => 'Google+ [Social Share Link]'
                    )
            )
                )
        );
        $this->addElement(
                'Text', 'itemCount', array(
            'label' => 'Number of channels to show.',
            'value' => 3,
                )
        );
        $this->addElement(
                'Text', 'titleTruncation', array(
            'label' => 'Tuncation limit for channel title.',
            'value' => 16,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
                )
        );
    }

}
