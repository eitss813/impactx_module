<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: WidgetController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitepage_WidgetController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $content_id = $this->_getParam('content_id');
    $view = $this->_getParam('view');
    $show_container = $this->_getParam('container', true);
    $params = $this->_getAllParams();
    $subjectGuid = $this->_getParam('subject');
    $getItem = Engine_Api::_()->getItemByGuid($subjectGuid);    
    
    
    // Render by content row
    if( null !== $content_id ) {

      $select = Engine_Api::_()->getDbtable('contentpages', 'sitepage')->select()->where('page_id =?', $getItem->getIdentity());
      
      $row = Engine_Api::_()->getDbtable('contentpages', 'sitepage')->fetchRow($select);
      
      if( null !== $row ) {
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');

        // Build full structure from children
        $contentpage_id = $row->contentpage_id;
        $page_id = $getItem->getIdentity();
        $pageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
        $content = $contentTable->fetchAll($contentTable->select()->where('contentpage_id = ?', $contentpage_id));
        $contenttoArray = $content->toArray();
        if($contenttoArray) {
            $row = $contentTable->find($content_id)->current();
            $structure = $pageTable->createElementParams($row);
            $children = $pageTable->prepareContentArea($content, $row);
            if( !empty($children) ) {
              $structure['elements'] = $children;
            }
            $structure['request'] = $this->getRequest();
            $structure['action'] = $view;

            // Create element (with structure)
            $element = new Engine_Content_Element_Container(array(
              'elements' => array($structure),
              'decorators' => array(
                'Children'
              )
            ));

            // Strip decorators
            if( !$show_container ) {
              foreach( $element->getElements() as $cel ) {
                $cel->clearDecorators();
              }
            }

            $content = $element->render();
            $this->getResponse()->setBody($content);
        } else {
				$contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
				$row = $contentTable->find($content_id)->current();
        // Build full structure from children
        $pagesTable = Engine_Api::_()->getDbtable( 'pages' , 'core' ) ;
        $page_id = $row->page_id;
       
        $content = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $page_id));
        $structure = $contentTable->createElementParams($row);
        $children = $contentTable->prepareContentArea($content, $row);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = $view;

        // Create element (with structure)
        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

        // Strip decorators
        if( !$show_container ) {
          foreach( $element->getElements() as $cel ) {
            $cel->clearDecorators();
          }
        }

        $content = $element->render();
        $this->getResponse()->setBody($content);
        }
        
      }  else {
				$contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
				$row = $contentTable->find($content_id)->current();
        // Build full structure from children
        $page_id = $row->page_id;
        $content = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $page_id));
        $structure = $contentTable->createElementParams($row);
        $children = $contentTable->prepareContentArea($content, $row);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = $view;

        // Create element (with structure)
        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

        // Strip decorators
        if( !$show_container ) {
          foreach( $element->getElements() as $cel ) {
            $cel->clearDecorators();
          }
        }

        $content = $element->render();
        $this->getResponse()->setBody($content);
      }

      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    // Render by widget name
    $mod = $this->_getParam('mod');
    $name = $this->_getParam('name');
    if( null !== $name ) {
      if( null !== $mod ) {
        $name = $mod . '.' . $name;
      }
      $structure = array(
        'type' => 'widget',
        'name' => $name,
        'request' => $this->getRequest(),
        'action' => $view,
      );

      // Create element (with structure)
      $element = new Engine_Content_Element_Container(array(
        'elements' => array($structure),
        'decorators' => array(
          'Children'
        )
      ));

      $content = $element->render();
      $this->getResponse()->setBody($content);

      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    $this->getResponse()->setBody('Aw, shucks.');
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }
}