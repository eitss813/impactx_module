<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
class Yndynamicform_Model_Form extends Core_Model_Item_Abstract
{
    const MAX_SIZE = 512;
    public function getCategory()
    {
        $category = Engine_Api::_()->getItem('yndynamicform_category', $this->category_id);
        if ($category) {
            return $category;
        }
    }

    public function isSubmittable()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity() && $this -> require_login) {
            return false;
        } else {
            return true;
        }
    }

    public function isReachedMaximumFormsByLevel()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $level_id = $viewer->getIdentity() ? $viewer->level_id : 5;
        // Check maximum entries submit by level
        $max_submit_forms = Engine_Api::_()->getDbTable('permissions', 'authorization') -> getAllowed('yndynamicform_form', $level_id, 'max');
        if (!$max_submit_forms) {
            return true;
        } else {
            $submitted_forms = Engine_Api::_() -> getDbTable('entries', 'yndynamicform') -> getSubmittedFormsOfViewer($this->getIdentity());
            if ($submitted_forms >= $max_submit_forms)
                return false;
        }
        return true;
    }

    public function isViewable()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer->isAdmin()) {
            return true;
        }

        // Check permission with this form
        if ((!$viewer -> getIdentity() && $this -> privacy == 2)
            || ($viewer -> getIdentity() && $this -> privacy == 1)
            || (date("Y-m-d 00-00-01", strtotime($this->valid_from_date)) > date("Y-m-d H-i-s") || (!$this->unlimited_time && date("Y-m-d 23-59-59", strtotime($this->valid_to_date)) < date("Y-m-d H-i-s")))) {
            return false;
        }

        return true;
    }

    public function getHref($params = array()) {
        $slug = $this -> getSlug();
        $params = array_merge(array(
            'route' => 'yndynamicform_form_detail',
            'reset' => true,
            'form_id' => $this -> form_id,
            'slug' => $slug
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
    }

    public function getAllModeratorsID()
    {
        $table = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform');
        $tName = $table -> info('name');
        return $table->select()
            ->from($tName, 'moderator_id')
            ->where('form_id = ?', $this -> getIdentity())
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    public function getAllModerators()
    {
        $table = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform');
        $select = $table->select()
            ->where('form_id = ?', $this -> getIdentity());

        $moderators = $table->fetchAll($select);
        return $moderators;
    }

    public function getSuperAdminsID() {
        $table = Engine_Api::_()->getDbtable('users', 'user');
        $tName = $table -> info('name');
        return $table->select()
            ->from($tName, 'user_id')
            ->where('level_id = ?', 1)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    /**
     * check if current user get maximum entries per unit time
     * @return bool
     */
    public function isGetMaximumEntries()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$this -> entries_max) {
            return true;
        }
        $table = Engine_Api::_() -> getDbTable('entries', 'yndynamicform');

        $select = $table->select()
            ->from($table -> info('name'), 'COUNT(entry_id)')
            ->where('form_id = ?', $this->getIdentity());

        if ($viewer -> getIdentity()) {
            $select -> where('owner_id = ?', $viewer -> getIdentity());
        } else {
            $ipObj = new Engine_IP();
            $ip = bin2hex($ipObj->toBinary());
            $select -> where('ip = ?', $ip);
        }

        $submitted_entries = 0;
        if ($this -> entries_max_per == 'total') {
            $submitted_entries = $select -> query()->fetchColumn();
        } else {
            switch ($this -> entries_max_per) {
                case 'day':
                    $from = date("Y-m-d 00-00-01");
                    $to = date("Y-m-d 23-59-59");
                    break;
                case 'week':
                    $from = date( 'Y-m-d 00-00-01', strtotime( 'monday this week' ) );
                    $to = date( 'Y-m-d 23-59-59', strtotime( 'sunday this week' ) );

                    break;
                case 'month':
                    $from = date( 'Y-m-d 00-00-01', strtotime( 'first day of this month' ) );
                    $to = date( 'Y-m-d 23-59-59', strtotime( 'last day of this month' ) );

                    break;
                case 'year':
                    $from = date( 'Y-1-1 00-00-01');
                    $to = date( 'Y-12-31 23-59-59');
            }

            $submitted_entries = $select -> where('creation_date <= ?', $to)
                -> where('creation_date >= ?', $from)
                -> query()->fetchColumn();
        }

        if ($submitted_entries >= $this -> entries_max)
            return false;
        else
            return true;
    }

    public function isModerator(User_Model_User $user)
    {
        $list = $this -> getAllModeratorsID();
        return in_array ($user -> getIdentity(), $list);
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Check if this form has file upload field and it is enabled
     *
     * @return bool
     */
    public function hasFileUpload()
    {
        $hasFileUpload = false;
        $option_id = $this->option_id;
        $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
        foreach ($fieldMaps as $fieldMap) {
            $field = $fieldMap->getChild();
            $config = $field->config;
            if ($field->type == 'file_upload' && ($config['show_registered'] || $config['show_guest']))
            {
                $hasFileUpload = true;
                break;
            }
        }

        return $hasFileUpload;
    }

    /**
     * @param $photo
     * @return $this
     * @throws Engine_Image_Exception
     * @throws User_Model_Exception
     */
    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File)
        {
            $file = $photo -> getFileName();
            $name = basename($file);
        }
        else if( $photo instanceof Storage_Model_File ) {
            $file = $photo->temporary();
            $name = $photo->name;
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'yndynamicform_form',
            'parent_id' => $this -> getIdentity(),
            'user_id' => $viewer -> getIdentity(),
        );

        // Save
        $storage = Engine_Api::_() -> storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image -> open($file);
        $image -> resize(640, 360) -> write($path . '/m_' . $name) -> destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image -> open($file);
        $image-> resize(420, 236) -> write($path . '/p_' . $name) -> destroy();

        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        $iProfile = $storage -> create($path . '/p_' . $name, $params);

        $iMain -> bridge($iProfile, 'thumb.profile');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        //@unlink($file);

        // Update row
        $this -> modified_date = date('Y-m-d H:i:s');
        $this -> photo_id = $iMain -> file_id;
        $this -> save();

        return $this;
    }
}