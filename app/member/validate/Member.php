<?php

namespace app\member\validate;
use think\Validate;

class Member extends Validate {

    protected $rule = [

        ['username'  ,'require|length:3,15|unique:member','{%username_length_require}|{%username_length_require}|{%username_exist}'],
        ['password'  ,'require|min:3,16','{%password_length_require}'],
        ['group_id'  ,'require','{%group_id_require}'],

    ];


    protected $scene = [
        'edit' => ['id' => 'require','group_id'=>'require','password' => 'requireWith:password|length:3,16'],
    ];

}