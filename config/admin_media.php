<?php 
return array(
    'default' => array(
        'styles' => array(
            'admin/bootstrap/css/bootstrap.min.css',
            'admin/styles/admin.css'
        ),
        'scripts' => array(
            'admin/scripts/plugin/jquery.min.js',
            'admin/bootstrap/js/bootstrap.min.js',
            'admin/scripts/plugin/jquery.validate.min.js',
            'admin/scripts/module/common.js',
            'admin/laydate/laydate.js'
        ),
    ),
    'Admin\AuthController@index' => array(
        'scripts' => array(
          'admin/scripts/module/login.js'
        )
    )
);