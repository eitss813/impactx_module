<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminContentController.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_AdminContentController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_managetemplates');

    // Get page param
    $page = $this->_getParam('page', 'sesnewsletter_default_template');
    $pageTable = Engine_Api::_()->getDbtable('templates', 'sesnewsletter');
    $contentTable = Engine_Api::_()->getDbtable('content', 'sesnewsletter');

    // Get current page
    $this->view->templateObject = $templateObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('template_id = ?', $page));
    if( null === $templateObject ) {
      $page = 'sesnewsletter_default_template';
      $templateObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page));
      if( null === $templateObject ) {
        throw new Engine_Exception('Home page is missing');
      }
    }
    $this->view->page = $page;
    $this->view->templateObject = $templateObject;

    // Make page form
    $this->view->pageForm = $pageForm = new Sesnewsletter_Form_Admin_Template_Template();
    if( !$templateObject->custom ) {
//       $pageForm->removeElement('levels');
//       $pageForm->removeElement('search');
    }

    $pageForm->populate($templateObject->toArray());
//     $levels = $pageForm->getElement('levels');
//     if( $levels && !empty($templateObject->levels) ) {
//       $levels->setValue(Zend_Json_Decoder::decode($templateObject->levels));
//     } else if( $levels ) {
//       $levels->setValue(array_keys($levels->getMultiOptions()));
//     }

    // Get page list
    $pageSelect = $pageTable->select()
        ->order('fragment DESC')
        ->order('custom ASC')
        //->where('template_id NOT IN (?)', array(1,2))
        ->order('displayname ASC');
    $pageList = $pageTable->fetchAll($pageSelect);
    $pageListAssoc = array(
      'fragment' => array(),
      'main' => array(),
      'mobile' => array(),
      'custom' => array()
    );
    foreach( $pageList as $pageRow ) {
      if( false !== stripos($pageRow->displayname, 'mobile') ) {
        $pageListAssoc['mobile'][] = $pageRow;
      } else if( $pageRow->fragment ) {
        $pageListAssoc['fragment'][] = $pageRow;
      } else if( $pageRow->custom ) {
        $pageListAssoc['custom'][] = $pageRow;
      } else {
        $pageListAssoc['main'][] = $pageRow;
      }
    }
    $this->view->pageListAssoc = $pageListAssoc;
    $this->view->pageList = $pageList;

    // Get available content blocks
    $contentAreas = $this->buildCategorizedContentAreas($this->getContentAreas());
    if( !$this->_getParam('show-all', true) ) { // @todo change default to false when ready to roll out
      $contentAreas = $this->filterContentAreasByRequirements($contentAreas, $templateObject->provides);
    }
    $this->view->contentAreas = $contentAreas;

    // Re-index by name
    $contentByName = array();
    foreach( $contentAreas as $category => $categoryAreas ) {
      foreach( $categoryAreas as $info ) {
        $contentByName[$info['name']] = $info;
      }
    }
    $this->view->contentByName = $contentByName;

    // Get registered content areas
    $contentRowset = $contentTable->fetchAll($contentTable->select()->where('template_id = ?', $templateObject->template_id));
    $contentStructure = $pageTable->prepareContentArea($contentRowset);

    // Validate structure
    // Note: do not validate for header or footer
    $error = false;
    if( substr($templateObject->name, 0, 6) !== 'header' && substr($templateObject->name, 0, 6) !== 'footer' ) {
      foreach( $contentStructure as &$info1 ) {
        if( !in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container' ) {
          $error = true;
          break;
        }
        foreach( $info1['elements'] as &$info2 ) {
          if( !in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container' ) {
            $error = true;
            break;
          }
        }
        // Re order second-level elements
        usort($info1['elements'], array($this, '_reorderContentStructure'));
      }
    }

    if( $error ) {
      throw new Exception('page failed validation check');
    }

    // Assign structure
    $this->view->contentRowset = $contentRowset;
    $this->view->contentStructure = $contentStructure;
  }

  public function updateAction()
  {
    $pageTable = Engine_Api::_()->getDbtable('templates', 'sesnewsletter');
    $contentTable = Engine_Api::_()->getDbtable('content', 'sesnewsletter');
    $db = $pageTable->getAdapter();
    $db->beginTransaction();

    try {

      // Get page
      $page = $this->_getParam('page');
      $templateObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('template_id = ?', $page));
      if( null === $templateObject ) {
        throw new Engine_Exception('Template is missing');
      }

      // Update layout
      if( null !== ($newLayout = $this->_getParam('layout')) ) {
        $templateObject->layout = $newLayout;
        $templateObject->save();
      }

      // Get registered content areas
      $contentRowset = $contentTable->fetchAll($contentTable->select()->where('template_id = ?', $templateObject->template_id));

      // Get structure
      $strucure = $this->_getParam('structure');
      if( is_string($strucure) ) {
        $strucure = Zend_Json::decode(trim($strucure, '()'));
      }
      if( !is_array($strucure) ) {
        throw new Engine_Exception('Structure is not an array or valid json structure');
      }

      // Diff
      $orderIndex = 1;
      $newRowsByTmpId = array();
      $existingRowsByContentId = array();

      foreach( $strucure as $element ) {

        // Get info
        $content_id = @$element['identity'];
        $tmp_content_id = @$element['tmp_identity'];
        $parent_id = @$element['parent_identity'];
        $tmp_parent_id = @$element['parent_tmp_identity'];

        $newOrder = $orderIndex++;

        // Sanity
        if( empty($content_id) && empty($tmp_content_id) ) {
          throw new Exception('content id and tmp content id both empty');
        }
        //if( empty($parent_id) && empty($tmp_parent_id) ) {
        //  throw new Exception('parent content id and tmp parent content id both empty');
        //}

        // Get existing content row (if any)
        $contentRow = null;
        if( !empty($content_id) ) {
          $contentRow = $contentRowset->getRowMatching('content_id', $content_id);
          if( null === $contentRow ) {
            throw new Exception('content row missing');
          }
        }

        // Get existing parent row (if any)
        $parentContentRow = null;
        if( !empty($parent_id) ) {
          $parentContentRow = $contentRowset->getRowMatching('content_id', $parent_id);
        } else if( !empty($tmp_parent_id) ) {
          $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
        }

        // Existing row
        if( !empty($contentRow) && is_object($contentRow) ) {
          $existingRowsByContentId[$content_id] = $contentRow;

          // Update row
          if( !empty($parentContentRow) ) {
            $contentRow->parent_content_id = $parentContentRow->content_id;
          }
          if( empty($contentRow->parent_content_id) ) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }

          // Set params
          if( isset($element['params']) && is_array($element['params']) ) {
            $contentRow->params = $element['params'];
          }

          if( $contentRow->type == 'container' ) {
            $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow->order = $newOrder;
          $contentRow->save();
        }

        // New row
        else
        {
          if( empty($element['type']) || empty($element['name']) ) {
            throw new Exception('missing name and/or type info');
          }

          if( $element['type'] == 'container' ) {
            $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow = $contentTable->createRow();
          $contentRow->template_id = $templateObject->template_id;
          $contentRow->order = $newOrder;
          $contentRow->type = $element['type'];
          $contentRow->name = $element['name'];

          // Set parent content
          if( !empty($parentContentRow) ) {
            $contentRow->parent_content_id = $parentContentRow->content_id;
          }
          if( empty($contentRow->parent_content_id) ) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }

          // Set params
          if( isset($element['params']) && is_array($element['params']) ) {
            $contentRow->params = $element['params'];
          }

          $contentRow->save();

          $newRowsByTmpId[$tmp_content_id] = $contentRow;
        }
      }

      // Delete rows that were not present in data sent back
      $deletedRowIds = array();
      foreach( $contentRowset as $contentRow ) {
        if( empty($existingRowsByContentId[$contentRow->content_id]) ) {
          $deletedRowIds[] = $contentRow->content_id;
          $contentRow->delete();
        }
      }
      $this->view->deleted = $deletedRowIds;

      // Send back new content info
      $newData = array();
      foreach( $newRowsByTmpId as $tmp_id => $newRow ) {
        $newData[$tmp_id] = $pageTable->createElementParams($newRow);
      }
      $this->view->newIds = $newData;

      $this->view->status = true;
      $this->view->error = false;

      $db->commit();

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }
  }

  public function createAction()
  {

    // Check if Duplicating
    $old_template_id = $this->_getParam('duplicate');

    // Get page param
    $page = $this->_getParam('page');
    $pageTable = Engine_Api::_()->getDbtable('templates', 'sesnewsletter');
    $contentTable = Engine_Api::_()->getDbtable('content', 'sesnewsletter');

    // Make new page
    if( ($page == 'new' || $page === null) && $this->getRequest()->isPost() ) {
      $templateObject = $pageTable->createRow();
      $templateObject->displayname = ( null !== ($name = $this->_getParam('name')) ? $name : 'Untitled' );
      $templateObject->provides = 'no-subject';
      $templateObject->save();
      $new_template_id = $templateObject->template_id;

      if( $old_template_id != 'null') {

        $db = Engine_Db_Table::getDefaultAdapter();
        $old_page_content = $db->select()
            ->from('engine4_sesnewsletter_content')
            ->where('`template_id` = ?', $old_template_id)
            ->order(array('type', 'content_id'))
            ->query()
            ->fetchAll();

        $content_count = count($old_page_content);
        for($i = 0; $i < $content_count; $i++){
          $contentRow = $contentTable->createRow();
          $contentRow->template_id = $new_template_id;
          $contentRow->type = $old_page_content[$i]['type'];
          $contentRow->name = $old_page_content[$i]['name'];
          if( $old_page_content[$i]['parent_content_id'] != null ) {
            $contentRow->parent_content_id = $content_id_array[$old_page_content[$i]['parent_content_id']];
          }
          else{
            $contentRow->parent_content_id = $old_page_content[$i]['parent_content_id'];
          }
          $contentRow->order = $old_page_content[$i]['order'];
          $contentRow->params = $old_page_content[$i]['params'];
          $contentRow->attribs = $old_page_content[$i]['attribs'];
          $contentRow->save();
          $content_id_array[$old_page_content[$i]['content_id']] = $contentRow->content_id;
        }
      }
      else{
        // Create Empty Content Rows
        $contentRow = $contentTable->createRow();
        $contentRow->type = 'container';
        $contentRow->name = 'main';
        $contentRow->template_id = $templateObject->template_id;
        // explicitly setting parent_content_id to null to prevent templates
        // to pass validation check after being created
        $contentRow->parent_content_id = NULL;
        $contentRow->save();

        $contentRow2 = $contentTable->createRow();
        $contentRow2->type = 'container';
        $contentRow2->name = 'middle';
        $contentRow2->template_id = $templateObject->template_id;
        $contentRow2->parent_content_id = $contentRow->content_id;
        $contentRow2->save();
      }
    }

    if( $templateObject ) {
      return $this->_redirectCustom($this->view->url(array('action' => 'index')).'?page='.$templateObject->template_id);
    } else {
      return $this->_redirectCustom($this->view->url(array('action' => 'index')));
    }
  }

  public function saveAction()
  {
    $template_id = $this->_getParam('template_id');

    $pageTable = Engine_Api::_()->getDbtable('templates', 'sesnewsletter');
    $templateObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $template_id)->orWhere('template_id = ?', $template_id));

    $form = new Sesnewsletter_Form_Admin_Template_Template();
    if( !$templateObject->custom ) {
      //$form->removeElement('levels');
    }

    $form->populate($templateObject->toArray());
//     $levels = $form->getElement('levels');
//     if( $levels && !empty($templateObject->levels) ) {
//       $levels->setValue(Zend_Json_Decoder::decode($templateObject->levels));
//     } else if( $levels ) {
//       $levels->setValue(array_keys($levels->getMultiOptions()));
//     }

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $values = $form->getValues();
      unset($values['template_id']);

//       if( empty($values['url']) ) {
//         $values['url'] = new Zend_Db_Expr('NULL');
//       }
//       if( !empty($values['levels']) ) {
//         $values['levels'] = Zend_Json_Encoder::encode($values['levels']);
//       }

      // @todo add provides no-viewer or viewer based on whether the public level is selected?

      $templateObject->setFromArray($values);
      $templateObject->save();
      $form->addNotice($this->view->translate('Your changes have been saved.'));
    }

    $this->getResponse()->setBody($form->render($this->view));
    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }

  public function deleteAction()
  {
    $template_id = $this->_getParam('page');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = 'Invalid method';
      return;
    }

    $pageTable = Engine_Api::_()->getDbtable('templates', 'sesnewsletter');
    $page = $pageTable->find($template_id)->current();

    if( null === $page ) {
      $this->view->status = false;
      $this->view->error = 'Page not found';
      return;
    }

    if( !$page->custom ) {
      $this->view->status = false;
      $this->view->error = 'Cannot delete non-custom templates';
      return;
    }

    $pageTable->deletePage($page);

    $this->view->status = true;
  }



  public function widgetAction()
  {
    // Render by widget name
    $mod = $this->_getParam('mod');
    $name = $this->_getParam('name');
    if( null === $name ) {
      throw new Exception('no widget found with name: ' . $name);
    }
    if( null !== $mod ) {
      $name = $mod . '.' . $name;
    }

    $contentInfoRaw = $this->getContentAreas();
    $contentInfo = array();
    foreach( $contentInfoRaw as $info ) {
      $contentInfo[$info['name']] = $info;
    }

    // It has a form specified in content manifest
    if( !empty($contentInfo[$name]['adminForm']) ) {

      if( is_string($contentInfo[$name]['adminForm']) ) { // Core_Form_Admin_Widget_*
        $formClass = $contentInfo[$name]['adminForm'];
        Engine_Loader::loadClass($formClass);
        $this->view->form = $form = new $formClass();
      } else if( is_array($contentInfo[$name]['adminForm']) ) {
        $this->view->form = $form = new Engine_Form($contentInfo[$name]['adminForm']);
      } else {
        throw new Core_Model_Exception('Unable to load admin form class');
      }

      // Try to set title if missing
      if( !$form->getTitle() ) {
        $form->setTitle('Editing: ' . $contentInfo[$name]['title']);
      }

      // Try to set description if missing
      if( !$form->getDescription() ) {
        $form->setDescription('placeholder');
      }

      $form->setAttrib('class', 'global_form_popup ' . $form->getAttrib('class'));

      // Add title element
      if( !$form->getElement('title') ) {
        $form->addElement('Text', 'title', array(
          'label' => 'Title',
          'order' => -100,
        ));
      }

      // Add mobile element?
      if( !$form->getElement('nomobile') ) {
        $form->addElement('Select', 'nomobile', array(
          'label' => 'Hide on mobile site?',
          'order' => 100000 - 5,
          'multiOptions' => array(
            '1' => 'Yes, do not display on mobile site.',
            '0' => 'No, display on mobile site.',
          ),
          'value' => '0',
        ));
      }


      if( !empty($contentInfo[$name]['isPaginated']) && !$form->getElement('itemCountPerPage') ) {
        $form->addElement('Text', 'itemCountPerPage', array(
          'label' => 'Count',
          'description' => '(number of items to show)',
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          ),
          'order' => 1000000 - 1,
        ));
      }

      // Add submit button
      if( !$form->getElement('submit') && !$form->getElement('execute') ) {
        $form->addElement('Button', 'execute', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array(
            'ViewHelper',
          ),
          'order' => 1000000,
        ));
      }

      // Add name
      $form->addElement('Hidden', 'name', array(
        'value' => $name,
        'order' => 1000010,
      ));

      if( !$form->getElement('cancel') ) {
        $form->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'onclick' => 'parent.Smoothbox.close();',
          'ignore' => true,
          'decorators' => array(
            'ViewHelper',
          ),
          'order' => 1000001,
        ));
      }

      if( !$form->getDisplayGroup('buttons') ) {
        $submitName = ( $form->getElement('execute') ? 'execute' : 'submit' );
        $form->addDisplayGroup(array(
          $submitName,
          'cancel',
        ), 'buttons', array(
          'order' => 1000002,
        ));
      }

      // Force method and action
      $form->setMethod('post')
        ->setAction($_SERVER['REQUEST_URI']);

      if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        $this->view->values = $form->getValues();
        $this->view->form = null;
      }

      return;
    }

    // Try to render admin page
    if( !empty($contentInfo[$name]) ) {
      try {
        $structure = array(
          'type' => 'widget',
          'name' => $name,
          'request' => $this->getRequest(),
          'action' => 'admin',
          'throwExceptions' => true,
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
      } catch( Exception $e ) {

      }
    }

    // Just render default editing form
    $this->view->form = $form = new Engine_Form(array(
      'title' => $contentInfo[$name]['title'],
      'description' => 'placeholder',
      'method' => 'post',
      'action' => $_SERVER['REQUEST_URI'],
      'class' => 'global_form_popup',
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Button',
          'submit',
          array(
            'label' => 'Save',
            'type' => 'submit',
            'decorators' => array('ViewHelper'),
            'ignore' => true,
            'order' => 1501,
          )
        ),
        array(
          'Hidden',
          'name',
          array(
            'value' => $name,
          )
        ),
        array(
          'Cancel',
          'cancel',
          array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'order' => 1502,
          )
        )
      ),
      'displaygroups' => array(
        'buttons' => array(
          'name' => 'buttons',
          'elements' => array(
            'submit',
            'cancel',
          ),
          'options' => array(
            'order' => 1500,
          )
        )
      )
    ));

    if( !empty($contentInfo[$name]['isPaginated']) ) {
      $form->addElement('Text', 'itemCountPerPage', array(
        'label' => 'Count',
        'description' => '(number of items to show)',
        'validators' => array(
          array('Int', true),
          array('GreaterThan', true, array(0)),
        )
      ));
    }

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $this->view->values = $form->getValues();
      $this->view->form = null;
    } else {
      $form->populate($this->_getAllParams());
    }
  }

  public function getContentAreas()
  {
    $contentAreas = array();

    $templateModule = array('core', 'sesnewsletter');
    $templateWidgets = array('core.html-block', 'core.rich-text-block', 'sesnewsletter.content', 'sesnewsletter.newsletter', 'sesnewsletter.footer', 'sesnewsletter.header', 'sesnewsletter.highlight');

    // From modules
    $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
    foreach( $modules as $module => $path ) {
      if(in_array($module, $templateModule)) {
        $contentManifestFile = dirname($path) . '/settings/content.php';
        if( !file_exists($contentManifestFile) ) continue;
        $ret = include $contentManifestFile;

        $contentAreas = array_merge($contentAreas, (array) $ret);
        $widgetcontentAreas = array();
        foreach($contentAreas as $key => $contentArea) {
            if(in_array($contentArea['name'], $templateWidgets)) {
                $widgetcontentAreas[] = $contentArea; //array_merge($contentAreas, (array) $ret[$key]['name']);
            }
        }
      }
    }

    return $widgetcontentAreas;
  }

  public function buildCategorizedContentAreas($contentAreas)
  {
    $categorized = array();
    foreach( $contentAreas as $config ) {
      // Check some stuff
      if( !empty($config['requireItemType']) ) {
        if( is_string($config['requireItemType']) && !Engine_Api::_()->hasItemType($config['requireItemType']) ) {
          $config['disabled'] = true;
        } else if( is_array($config['requireItemType']) ) {
          $tmp = array_map(array(Engine_Api::_(), 'hasItemType'), $config['requireItemType']);
          $config['disabled'] = !(array_sum($tmp) == count($config['requireItemType']));
        }
      }

      // Add to category
      $category = ( isset($config['category']) ? $config['category'] : 'Uncategorized' );
      $categorized[$category][] = $config;
    }

    // Sort categories
    uksort($categorized, array($this, '_sortCategories'));

    // Sort items in categories
    foreach( $categorized as $category => &$items ) {
      usort($items, array($this, '_sortCategoryItems'));
    }

    return $categorized;
  }

  public function filterContentAreasByRequirements($contentAreas, $provides)
  {
    // Process provides
    if( is_string($provides) ) {
      $providedFeatures = explode(';', $provides);
      $provides = array();
      foreach( $providedFeatures as $providedFeature ) {
        if( false === strpos($providedFeature, '=') ) {
          $provides[$providedFeature] = true;
        } else {
          list($feature, $value) = explode('=', $providedFeature);
          if( false === strpos($value, ',') ) {
            $provides[$feature] = $value;
          } else {
            $provides[$feature] = explode(',', $value);
          }
        }
      }
    } else if( !is_array($provides) ) {
      $provides = array();
    }

    // Process content areas
    $filteredContentAreas = array();
    foreach( $contentAreas as $category => $categoryWidgets ) {
      foreach( $categoryWidgets as $widget ) {
        $pass = true;
        // Requirements
        if( !empty($widget['requirements']) && is_array($widget['requirements']) ) {
          foreach( $widget['requirements'] as $k => $v ) {
            if( is_numeric($k) ) {
              $req = $v;
              $value = null;
            } else {
              $req = $k;
              $value = $v;
            }
            // Note: will continue if missing any of the requirements
            switch( $req ) {
              case 'viewer':
                if( isset($provides['no-viewer']) ) {
                  $pass = false;
                }
                break;
              case 'no-viewer':
                if( isset($provides['viewer']) ) {
                  $pass = false;
                }
                break;
              case 'subject':
                if( !isset($provides['subject']) /*||
                    isset($provides['no-subject'])*/ ) {
                  $pass = false;
                } else if( is_string($value) ) {
                  if( is_string($provides['subject']) &&
                      $provides['subject'] == $value ) {

                  } else if( is_array($provides['subject']) &&
                      in_array($value, $provides['subject']) ) {

                  } else {
                    $pass = false;
                  }
                } else if( is_array($value) ) {
                  if( count(array_intersect($value, (array) $provides['subject'])) <= 0 ) {
                    $pass = false;
                  }
                }
                break;
              case 'no-subject':
                if( isset($provides['subject']) /*||
                    !isset($provides['no-subject'])*/ ) {
                  $pass = false;
                }
                // @todo subject blacklist?
                break;
              case 'header-footer';
                if( !isset($provides['header-footer']) ) {
                  $pass = false;
                }
                break;
            }
          }
        }
        // Add to areas
        if( $pass ) {
          $filteredContentAreas[$category][] = $widget;
        }
      }
    }

    return $filteredContentAreas;
  }

  protected function _sortCategories($a, $b)
  {
    if( $a == 'Core' ) return -1;
    if( $b == 'Core' ) return 1;
    return strcmp($a, $b);
  }

  protected function _sortCategoryItems($a, $b)
  {
    if( !empty($a['special']) ) return -1;
    if( !empty($b['special']) ) return 1;
    return strcmp($a['title'], $b['title']);
  }

  protected function _reorderContentStructure($a, $b)
  {
    $sample = array('left', 'middle', 'right');
    $av = $a['name'];
    $bv = $b['name'];
    $ai = array_search($av, $sample);
    $bi = array_search($bv, $sample);
    if( $ai === false && $bi === false ) return 0;
    if( $ai === false ) return -1;
    if( $bi === false ) return 1;
    $r = ( $ai == $bi ? 0 : ($ai < $bi ? -1 : 1) );
    return $r;
  }
}
