<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

$functions = array(
  
    'cursive_json' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'cursive_json_func',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'generate JSON',
        'type'        => 'write',
        'ajax'        => true
    ),
    'cursive_get_user_list' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'get_user_list',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'get quiz list by course',
        'type'        => 'read',
        'ajax'        => true
    ),
    'cursive_get_module_list' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'get_module_list',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'get quiz list by course',
        'type'        => 'read',
        'ajax'        => true
    ),
    'cursive_reports' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'cursive_reports_func',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'generate Reports for download',
        'type'        => 'write',
        'ajax'        => true
    ),
    'cursive_approve_token' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'cursive_approve_token_func',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'generate Reports for download',
        'type'        => 'write',
        'ajax'        => true
    ),
    
    'cursive_user_comments' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'cursive_user_comments_func',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => 'User Comments',
        'type'        => 'write',
        'ajax'        => true
    )
    ,
    'cursive_get_comment_link' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'get_comment_link',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => ' Comments Links',
        'type'        => 'write',
        'ajax'        => true
    ) ,
    'cursive_get_assign_comment_link' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'get_assign_comment_link',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => ' Comments Links',
        'type'        => 'write',
        'ajax'        => true
    ) ,
    'cursive_user_list_submission_stats' => array(
        'classname'   => 'cursive_json_func_data',
        'methodname'  => 'get_user_list_submission_stats',
        'classpath'   => '/lib/editor/tiny/plugins/cursive/externallib.php',
        'description' => ' Comments Links',
        'type'        => 'write',
        'ajax'        => true
    )

);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.

$services = array(
    'cursive_json_service' => array(
        'functions' => array(           
            'cursive_json',
            'cursive_reports',
            'cursive_get_quizlist',
            'cursive_get_module_list',
            'cursive_user_comments',
            'cursive_get_comment_link',
            'cursive_user_list_submission_stats',
            'cursive_approve_token'
        ),
        'restrictedusers' => 0,
        'enabled'=>1
    )
);



