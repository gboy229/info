<?php

namespace app\member\controller;
use app\common\controller\Base;

class Check extends Base{


    public function __construct()
    {

        parent::__construct();


         if($this->member['id'] < 1){

             if(is_ajax()){


                  showmessage(lang('_not_login_'),url('member/login'));
             }else{

                 $url_forward = input('url_forward') ? input('url_forward') : request()->server('REQUEST_URI');
                 $this->redirect('/member/login',['url_forward'=>$url_forward],302);

             }

         }

    }



}