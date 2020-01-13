<?php

namespace app\member\model;
use think\Model;

class Message extends Model{



    protected function getTypeAttr($value){

        $arr=['账单问题','账号问题','发展建议','中奖领取'];
        return $arr[$value];
    }


    protected function getAddTimeAttr($value){

        return getdatetime($value);

    }

    protected function getReTimeAttr($value){

        if($value){
            return getdatetime($value);
        }


    }

  protected function getContentAttr($value){

        if($value){
            return htmlentities(remove_xss($value));
        }


    }



}