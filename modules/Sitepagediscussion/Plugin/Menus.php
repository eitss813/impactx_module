<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagediscussion_Plugin_Menus {

  public function onMenuInitialize_SitepagediscussionTopicWatch() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitepage = $subject->getParentSitepage();
    $isWatching = null;

    $canPost = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');;

    if(!$canPost && !$viewer->getIdentity())
      return false;

		$topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitepage');
		$isWatching = $topicWatchesTable
						->select()
						->from($topicWatchesTable->info('name'), 'watch')
						->where('resource_id = ?', $sitepage->getIdentity())
						->where('topic_id = ?', $subject->getIdentity())
						->where('user_id = ?', $viewer->getIdentity())
						->limit(1)
						->query()
						->fetchColumn(0)
		;

		if (false === $isWatching) {
			$isWatching = null;
		} else {
			$isWatching = (bool) $isWatching;
		}

    if(!$isWatching) {
      return array(
        'label' => 'Watch Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'sitepage',
            'controller' => 'topic',
            'action' => 'watch',
            'watch' => 1,
            'topic_id' => $subject->getIdentity()
        )
			);
    } else {
			return array(
        'label' => 'Stop Watching Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'sitepage',
            'controller' => 'topic',
            'action' => 'watch',
            'watch' => 0,
            'topic_id' => $subject->getIdentity()
        )
			);
    }

	}

  public function onMenuInitialize_SitepagediscussionTopicRename() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitepage = $subject->getParentSitepage();
    $canEdit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if(!$canEdit && !$viewer->getIdentity())
      return false;

		return array(
			'label' => 'Rename',
			'route' => 'default',
      'class' => 'smoothbox ui-btn-default ui-btn-action',
			'params' => array(
					'module' => 'sitepage',
					'controller' => 'topic',
					'action' => 'rename',
					'topic_id' => $subject->getIdentity()
			)
		);

	}

  public function onMenuInitialize_SitepagediscussionTopicDelete() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitepage = $subject->getParentSitepage();
    $canEdit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

		return array(
			'label' => 'Delete Topic',
			'route' => 'default',
      'class' => 'smoothbox ui-btn-default ui-btn-danger',
			'params' => array(
					'module' => 'sitepage',
					'controller' => 'topic',
					'action' => 'delete',
					'topic_id' => $subject->getIdentity()
			)
		);

	}

  public function onMenuInitialize_SitepagediscussionTopicOpen() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitepage = $subject->getParentSitepage();
    $canEdit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

    if(!$subject->closed) {
			return array(
				'label' => 'Close',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'sitepage',
						'controller' => 'topic',
						'action' => 'close',
						'topic_id' => $subject->getIdentity(),
            'closed'=> 1
				)
			);
    } else {
			return array(
				'label' => 'Open',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'sitepage',
						'controller' => 'topic',
						'action' => 'close',
						'topic_id' => $subject->getIdentity(),
            'closed'=> 0
				)
			);
    }

	}

  public function onMenuInitialize_SitepagediscussionTopicSticky() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitepage = $subject->getParentSitepage();
    $canEdit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

    if(!$subject->sticky) {
			return array(
				'label' => 'Make Sticky',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'sitepage',
						'controller' => 'topic',
						'action' => 'sticky',
						'topic_id' => $subject->getIdentity(),
            'sticky'=> 1
				)
			);
    } else {
			return array(
				'label' => 'Remove Sticky',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'sitepage',
						'controller' => 'topic',
						'action' => 'sticky',
						'topic_id' => $subject->getIdentity(),
            'sticky'=> 0
				)
			);
    }

	}

}
