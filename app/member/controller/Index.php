<?php

namespace app\member\controller;

use app\common\controller\Base;

class Index extends Base {

    public function __construct()
    {
        parent::__construct();

        $this->member_service=model('member/Member','service');
    }



    public function index(){



        if($this->member['id'] < 1){
            $tpl='user/index';
        }else{
            $tpl='index/index';
        }


        return $this->fetch($tpl);

    }



  

}