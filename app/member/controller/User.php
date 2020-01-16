<?php

namespace app\member\controller;

use app\common\controller\Base;

class User extends Base {

    public function __construct()
    {
        parent::__construct();

        $this->member_service=model('member/Member','service');
    }


    public function login(){


        if(is_ajax()){
            $username=input('username','');
            $password=input('password','');
            if(!$this->member_service->login($username,$password)){
                showmessage($this->member_service->errors);
            }

            showmessage('登录成功',url('/member'),1);
        }else{
            return $this->fetch();
        }

        
    }


    public function register(){

        if(is_ajax()){



            $post=input('post.','');

            $captcha = new \think\captcha\Captcha();
            if(!$captcha->check(trim($post['captcha']),'member')){
               showmessage('验证码不正确');
            }

            $data=[];
            $data['group_id']=(int) $post['regid'];

            $data['mobile']=trim($post['mobile']);
            $data['vcode']=trim($post['code']);
            $data['company']=trim($post['company']);
            $data['password']=$post['password'];
            $data['repassword']=$post['cpassword'];

            if(!$this->member_service->register($data)){
                showmessage($this->member_service->errors);
            }

            showmessage('注册成功',url('/member'),1);
        }
        return $this->fetch();
    }



    public function sms(){

        if(is_post()){

            $phone=trim($_POST['mobile']);
            $action=trim($_POST['action']);

            if(empty($phone)){
                showmessage('请输入手机号码');
            }

            if(!is_mobile($phone)){
                showmessage('手机号码格式不对');
            }

            if($action=='getpwd'){
                if(!db('member')->where(['username'=>$phone])->value('id')){
                    showmessage('手机未注册');
                }

                $action='getpwd';
            }else{
                if(db('member')->where(['username'=>$phone])->value('id')){
                    showmessage('此手机号码已被注册了');
                }
                $action='register';
            }


            $result=to_sendsms($phone);
            if($result['status']!=1){
                apijson($result['message']);
            }
            $data=[];
            $data['mobile']=$phone;
            $data['mid']=0;
            $data['vcode']=$result['result'];
            $data['action']=$action;
            $data['dateline']=time();
            if(db('vcode')->data($data)->insert()){
                showmessage($result['message'],'',1);
            }else{
                showmessage('发生意外操作,请重新发送');
            }
        }

    }

    public function getpwd(){

        if(is_ajax()){
            $post=input('post.','');
            $captcha = new \think\captcha\Captcha();
            if(!$captcha->check(trim($post['captcha']),'member')){
                showmessage('验证码不正确');
            }

            $data=[];

            $data['mobile']=trim($post['mobile']);
            $data['code']=trim($post['code']);
            $data['password']=$post['loginpwd'];
            $data['repassword']=$post['loginpwd2'];

            if(!$this->member_service->get_login_pwd($data)){
                showmessage($this->member_service->errors);
            }

            showmessage('登录密码修改成功，请登录',url('/member/login'),1);
        }else{
            return $this->fetch();
        }
    }


    public function logout(){

        $this->member_service->logout();
        $this->redirect('/member','',302);

    }

    public function code(){

        ob_clean();
        $captcha = new \think\captcha\Captcha();
        $captcha->codeSet='0123456789';
        $captcha->imageW=155;
        $captcha->imageH = 40;  //图片高
        $captcha->fontSize =20;  //字体大小
        $captcha->length   = 4;  //字符数
        $captcha->fontttf = '5.ttf';  //字体
        $captcha->expire = 30;  //有效期
        $captcha->useNoise = false;  //不添加杂点
        return $captcha->entry('member');

    }


  

}