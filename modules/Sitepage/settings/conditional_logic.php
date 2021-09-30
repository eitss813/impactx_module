<?php


// group conditional logic by type
$conditional_logic = array(
    'text' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'textarea' => array(
        'conditional_compare' => 'contains', 'starts', 'ends_with',
    ),
    'select' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'radio' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'checkbox' => array(
        'conditional_compare' => 'is',
    ),
    'multiselect' => array(
        'conditional_compare' => 'contains', 'does_not_contain',
    ),
    'multi_checkbox' => array(
        'conditional_compare' => 'contains', 'does_not_contain',
    ),
    'integer' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'float' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'metrics' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'date' => array(
        'conditional_compare' => 'is', 'is_not', 'greater_than', 'less_than',
    ),
    'heading' => array(
    ),
    // Specific
    'profile_type' => array(
    ),
    'first_name' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'last_name' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'gender' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'birthdate' => array(
        'conditional_compare' => 'is', 'is_not', 'greater_than', 'less_than',
    ),
    'about_me' => array(
        'conditional_compare' => 'contains', 'starts', 'ends_with',
    ),

    // Communications
    'website' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'twitter' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'facebook' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'aim' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),

    // Location
    'city' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'country' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'zip_code' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),
    'location' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    ),

    // Dating
    'relationship_status' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'looking_for' => array(
        'conditional_compare' => 'contains', 'does_not_contain',
    ),
    'partner_gender' => array(
        'conditional_compare' => 'contains', 'does_not_contain',
    ),
    'education_level' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'ethnicity' => array(
        'conditional_compare' => 'contains', 'does_not_contain',
    ),
    'income' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'occupation' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'political_views' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'religion' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'weight' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'zodiac' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'eye_color' => array(
        'conditional_compare' => 'is', 'is_not',
    ),
    'interests' => array(
        'conditional_compare' => 'contains', 'starts', 'ends_with',
    ),
    'currency' => array(
        'conditional_compare' => 'is', 'is_not', 'contains', 'starts', 'ends_with',
    )
);

return $conditional_logic;