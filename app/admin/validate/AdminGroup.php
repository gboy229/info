<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\validate;
use think\Validate;

class AdminGroup extends Validate
{

    protected $rule = [
        'title' => 'require',
        'description' => 'require',
        'rules' => 'require',

    ];

    protected $message = [
        'title.require' => '{%role_name_require}',
        'description.require' => '{%role_description_require}',
        'rules.require' => '{%rules_admin_group_require}',

    ];

}