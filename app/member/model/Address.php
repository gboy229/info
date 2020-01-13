<?php

namespace app\member\model;
use think\Model;

class Address extends Model{

    protected $table='gboy_member_address';

    protected $append=['complete_address'];


    protected function getCompleteAddressAttr($value,$data){

        return $data['province'].$data['city'].$data['county'].$data['address'];

    }

}