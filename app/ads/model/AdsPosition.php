<?php

namespace app\ads\model;
use think\Model;

class AdsPosition extends Model{


    protected $append='count_num';


    protected function getCountNumAttr($value,$data){

       return  model('ads/Ads')->where(['position_id'=>$data['id']])->count();

    }

}