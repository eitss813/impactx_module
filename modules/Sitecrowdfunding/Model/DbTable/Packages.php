<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Packages.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Packages extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Package';

    public function getEnabledPackage() {

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ($viewer_id == 0) {
            $user_level = 0;
        } else {
            $user = Engine_Api::_()->user()->getViewer();
            $user_level = $user->level_id;
        }

        $start_one = "'" . $user_level . "'";
        $start = "'" . $user_level . ",%'";
        $middile = "'%," . $user_level . ",%'";
        $end = "'%," . $user_level . "'";

        $select = $this->select()
                ->from($this, array('price', 'enabled', 'package_id'))
                ->where("level_id = 0 or level_id LIKE $start_one or level_id LIKE $start or level_id LIKE $middile or level_id LIKE $end ")
                ->where('enabled = 1');
        return $this->fetchRow($select);
    }

    public function getDisabledPackage() {
        return $this->select()
                        ->from($this, new Zend_Db_Expr('COUNT(*)'))
                        ->where('enabled = 0')
                        ->query()
                        ->fetchColumn();
    }

    public function getTotalPackage() {
        return $this->select()
                        ->from($this, new Zend_Db_Expr('COUNT(*)'))
                        ->query()
                        ->fetchColumn();
    }

    public function getEnabledNonFreePackageCount() {
        return $this->select()
                        ->from($this, new Zend_Db_Expr('COUNT(*)'))
                        ->where('enabled = ?', 1)
                        ->where('price > ?', 0)
                        ->query()
                        ->fetchColumn();
    }

    public function getPackagesSql($user = 0, $paginator = 0) {

        if (empty($user)) {
            $user_level = 0;
        } else {
            $user = Engine_Api::_()->user()->getViewer();
            $user_level = $user->level_id;
        }

        $start_one = "'" . $user_level . "'";
        $start = "'" . $user_level . ",%'";
        $middile = "'%," . $user_level . ",%'";
        $end = "'%," . $user_level . "'";

        $select = $this->select()
                ->where("level_id = 0 or level_id LIKE $start_one or level_id LIKE $start or level_id LIKE $middile or level_id LIKE $end ")
                ->order('order')
                ->order('package_id DESC')
                ->where('enabled=1');

        if (!empty($paginator))
            return $select;
        else
            return Zend_Paginator::factory($select);
    }

    public function getPackageResult($project) {

        $packages_select = $this->getPackagesSql($project->getOwner(), '1')
                ->where("update_list = ?", 1)
                ->where("enabled = ?", 1)
                ->where("package_id <> ?", $project->package_id);
        return Zend_Paginator::factory($packages_select);
    }

    /**
     * Get Package Count
     *
     * @return $countPackage
     */
    public function getPackageCount($allpackage = 0) {

        $user = Engine_Api::_()->user()->getViewer();
        if (!isset($user->level_id))
            return 0;

        $start_one = "'" . $user->level_id . "'";
        $start = "'" . $user->level_id . ",%'";
        $middile = "'%," . $user->level_id . ",%'";
        $end = "'%," . $user->level_id . "'";
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(' . $this->info('name') . '.package_id)'));
        if (empty($allpackage))
            $select->where('enabled = ?', 1);

        return $countPackage = $select->where("level_id = 0 or level_id LIKE $start_one or level_id LIKE $start or level_id LIKE $middile or level_id LIKE $end ")
                ->query()
                ->fetchColumn();
    }

    public function getPackageOption($package_id = null, $option = null) {

        return $this->select()
                        ->from($this->info('name'), array($option))
                        ->where('package_id = ?', $package_id)
                        ->query()
                        ->fetchColumn();
    }

}
