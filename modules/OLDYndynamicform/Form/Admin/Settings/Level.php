<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 5:19 PM
 */
class Yndynamicform_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
    public function init()
    {
        parent::init();
        // My stuff
        $this
            ->setTitle('Member Level Settings')
            ->setDescription("There settings are applied on a per member level basis. Start by selecting a member level you want to modify, then adjust the settings for that level below.");

        // Element: view
        $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing of Forms?',
            'description' => 'Do you want to let members view forms? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                1 => 'Yes, allow viewing of forms.',
                0 => 'No, do not allow forms to be viewed.',
            ),
        ));

        // Element: max
        $this->addElement('Text', 'max', array(
            'label' => 'Maximum Allowed Form Submissions?',
            'description' => 'Enter the total number of forms that members are allowed to submit per day. The field must contain an integer, use zero \'0\' for unlimited.',
            'value' => 0,
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(0),
            ),
        ));

        if( !$this->isPublic() ) {

            // Element: submission
            $this->addElement('Radio', 'submission', array(
                'label' => 'Allow Forms Submission?',
                'description' => 'Do you want to let members submit forms? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    1 => 'Yes, allow submitting forms.',
                    0 => 'No, do not allow form to be submitted.',
                ),
            ));

            // Element: comment
            $this->addElement('Radio', 'comment', array(
                'label' => 'Allow Commenting on Forms?',
                'description' => 'Do you want to let members of this level comment on forms?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to comment on forms.',
                    0 => 'No, do not allow members to comment on forms.',
                ),
            ));

            if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {

                $this->addElement('Integer', 'first_amount', array(
                    'label' => 'Credit for successfully submitting a form',
                    'description' => 'No of First Actions',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));

                $this->addElement('Integer', 'first_credit', array(
                    'description' => 'Credit/Action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));

                $this->addElement('Integer', 'credit', array(
                    'description' => 'Credit for next action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'period', array(
                    'description' => 'Period (days)',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
            }
        }
    }
}