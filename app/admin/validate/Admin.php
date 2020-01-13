<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\validate;
use think\Validate;

class Admin extends Validate{

    protected $rule = [
        ['code'  ,'require|checkCode','{%code_not_exist}|{%code_checked_error}'],
        ['username'  ,'require','{%username_not_exist}'],
        ['password'  ,'require','{%password_not_exist}'],
    ];



    protected function checkCode($value,$rule,$data){

        $captcha = new \think\captcha\Captcha();
        $result=$captcha->check($value);
        return $result;

    }



}
