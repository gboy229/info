<?php

namespace app\member\hook;
use think\Db;

class Hook
{



    public function registerInit(&$params){

        //注册开关
        $reg_allow=config('cache.reg_allow');

        if(empty($reg_allow)){
            $params['reg_status']=0;
            $params['reg_error']='注册暂未开放';
        }

        //注册上限
        $member_reg_num=config('cache.member_reg_num');

        if($member_reg_num!=''){

            $member_reg_num=(int) $member_reg_num;

            $day_reg_count=db('member')->where(['register_time'=>['between',[strtotime(date('Y-m-d')),time()]]])->count(1);
            if($day_reg_count>=$member_reg_num){

                $params['reg_status']=0;
                $params['reg_error']='今天注册人数已上限了';;
            }

        }




    }


    public function memberInit($member){

        //单点登录
        $member_one_login=config('cache.member_one_login');
        if($member_one_login){

            $authkey = session('gboy_member_auth');
            if ($authkey) {
                list($uid, $rand, $login_key,$admin_logo) = explode("\t", authcode($authkey));


                if(authcode($admin_logo)!=='gboy_admin'){
                    if($member['login_key']!=$login_key){
                        model('member/Member','service')->logout();
                        exit('<script>alert("您的账户在另一地登录，您被迫下线");location.href="'.url('member/publics/login').'";</script>');
                    }
                }


            }
        }
		
		
	    $login_out=config('cache.member_login_out');


        if($login_out){

            $authkey = session('gboy_member_auth');

            if ($authkey) {
                list($uid, $rand, $login_key,$admin_logo) = explode("\t", authcode($authkey));

                if(authcode($admin_logo)!=='gboy_admin') {
                    $end_time = session('my_login_time') + ($login_out*60);


                    if (time() >= $end_time) {
                        model('member/Member', 'service')->logout();
                        exit('<script>alert("登录超时，请重新登录");location.href="' . url('member/publics/login') . '";</script>');
                    } else {
                        session('my_login_time', time());
                    }
                }
            }

        }


    }



    public function afterRegister($reg_data){



    }




    public function afterLogin($member){


    }






}