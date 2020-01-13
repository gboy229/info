<?php


namespace app\ads\validate;
use think\Validate;

class AdsPosition extends Validate{

    protected $rule = [
        ['name'  ,'require','{%ads_position_name_require}'],

    ];

}