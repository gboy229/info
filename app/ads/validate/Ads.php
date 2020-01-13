<?php


namespace app\ads\validate;
use think\Validate;

class Ads extends Validate{

    protected $rule = [
        ['title'  ,'require','{%ads_name_require}'],
        ['position_id'  ,'require','{%ads_position_require}'],
        ['thumb'  ,'require','{%ads_thumb_require}'],

    ];

}