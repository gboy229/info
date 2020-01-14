<?php

namespace app\member\model;
use think\Model;

class MemberLog extends Model{




    protected function getValueAttr($value,$data){



        if($value>0){
            $value='+'.$value;
        }
        return $value;
    }

    protected function getDateLineAttr($value){
        return getdatetime($value);
    }





    public function getMoneyDetailAttr($value){

        $arr=json_decode($value,true);

        $text='变动前：'.$arr['old_money'].'，变动后：'.$arr['new_money'];

        return $text;

    }






}