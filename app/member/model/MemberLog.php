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



    protected function getTypeAttr($value){

        $type_arr=['money'=>'余额',
        ];



        $type_arr=$type_arr[$value];


        return $type_arr;

    }


    public function getMoneyDetailAttr($value){

        $arr=json_decode($value,true);

        $text='变动前：'.$arr['old_money'].'，变动后：'.$arr['new_money'];

        return $text;

    }






}