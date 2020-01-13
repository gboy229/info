<?php

namespace app\admin\controller;
use app\common\controller\Init;
use think\Db;

class Login extends Init{


    private $code_url='RR5FYV^W!3J@uRZL';

    public function _initialize()
    {
        //error_status(404);
        if(request()->action()!='captcha'){

            if(input('go')!==$this->code_url){

                error_status(404);
            }
        }

        define('IN_ADMIN',true);

        $this->service = model('admin/Admin','service');

        $admin=$this->service->init();

        if($admin['id'] && request()->action()!='logout'){

            $this->redirect('admin/index/index');
        }


    }

    public function index(){

        if(IS_POST()){

            $data=input('post.');
            $result = $this->service->login($data['username'], $data['password'],$data['code']);
			
            if($result === false) {
                showmessage($this->service->errors);
            } else {
				
                session('gboy_guuadminislogin',1);
                $admin=$this->service->init();

                $this->redirect('admin/index/index',['formhash'=>$admin['formhash']]);
            }

        }else{


            return $this->fetch();
        }


    }


    public function logout(){



        $this->service->logout();
        session('gboy_guuadminislogin',null);

        header("Cache-control:no-cache,no-store,must-revalidate");
        header("Pragma:no-cache");
        header("Expires:0");
        $this->redirect(url('admin/login/index',['go'=>$this->code_url]));
    }


    public function captcha(){
        ob_clean();
        $captcha = new \think\captcha\Captcha();
        $captcha->imageW=290;
        $captcha->imageH = 60;  //图片高
        $captcha->fontSize =30;  //字体大小
        $captcha->length   = 5;  //字符数
        $captcha->fontttf = '5.ttf';  //字体
        $captcha->expire = 30;  //有效期
        $captcha->useNoise = true;  //不添加杂点
        return $captcha->entry();
    }
}