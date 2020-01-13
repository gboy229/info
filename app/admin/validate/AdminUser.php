<?php

/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\validate;

use think\Validate;

class AdminUser extends Validate
{


    protected $rule = [
        'username' => 'require|length:6,16|unique:admin_user',
        'password' => 'require|length:6,16',

    ];


    protected $message = [
        'username.require' => '{%username_not_exist}',
        'username.length' => '{%username_error_length}',
        'username.unique' => '{%user_name_exist}',
        'password.require' => '{%password_not_exist}',
        'password.length' => '{%password_error_length}',
    ];

    protected $scene = [
        'edit' => ['id' => 'require','password' => 'requireWith:password|length:6,16'],
    ];


}