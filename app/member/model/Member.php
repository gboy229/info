<?php

namespace app\member\model;
use think\Model;

class Member extends Model{

    protected $insert=['register_time','register_ip'];
    protected $append=['group_name','sex_text'];







    protected function setRegisterTimeAttr(){
        return time();
    }

    protected function setRegisterIpAttr(){

        return getip();
    }

    protected function getGroupNameAttr($value,$data){


        $group_name=db('member_group')->where(['id'=>$data['group_id']])->value('name');



        return $group_name;
    }



    protected function getRegisterTimeAttr($value){

        if($value)  return getdatetime($value);

    }

    protected function getLoginTimeAttr($value){

        if($value){
            return getdatetime($value);
        }else{
            return '';
        }

    }


    public function count($sqlmap=''){

        return  $this->where($sqlmap)->order('id desc')->count(1);

    }







    protected function setLockTimeAttr($value,$data){

        if($value){
            return strtotime($value);
        }

    }

    protected function getLockTimeAttr($value,$data){

        if($value){
            return getdatetime($value,'Y-m-d');
        }

    }


	 protected function getNicknameAttr($value,$data){

		if($value){
			return htmlentities(remove_xss($value));
		}else{
			return '';
		}

    }
    protected function getCompanyAttr($value,$data){

        if($value){
            return htmlentities(remove_xss($value));
        }else{
            return '';
        }

    }

	 protected function getRealnameAttr($value,$data){

		if($value){
			return htmlentities(remove_xss($value));
		}else{
			return '';
		}

    }


    protected function getSexTextAttr($value,$data){

        $arr=['保密','先生','女士'];
        $text=$arr[$data['sex']];
        return $text;

    }




}