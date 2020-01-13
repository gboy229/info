<?php

namespace app\content\validate;
use think\Validate;

class Content extends Validate{

    protected $rule = [
        ['menuid'  ,'require','{%category_not_exist}'],
        ['title'  ,'require','{%title_require}'],
    ];
}