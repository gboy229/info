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
            $tpl='User/index';
        }else{
            $tpl='Index/index';
        }


        return $this->fetch($tpl);

    }



  

}