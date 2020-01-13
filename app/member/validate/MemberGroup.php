<?php

namespace app\member\validate;
use think\Validate;
class  MemberGroup extends Validate
{


    protected $rule = [
        ['name'  ,'require','{%member_group_name_require}'],

    ];

}