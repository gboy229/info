<?php


namespace app\content\validate;
use think\Validate;

class Category extends Validate{

    protected $rule = [
        ['modelid'  ,'require|max:30','请选择模型'],
        ['name'  ,'require','{%class_name_require}'],

    ];


}
