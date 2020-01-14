<?php


namespace app\member\controller;
use app\common\controller\Init;

class MemberLog extends Init{

    public function _initialize()
    {

        parent::_initialize();
        $this->service = model('member/MemberLog','service');
    }




    public function index(){



        if(false===$sqlmap = $this->service->build_sqlmap(input('get.'))){
            showmessage($this->service->errors);
        }



        $list=$this->service->get_list($sqlmap);


        $this->assign('list',$list);
        return $this->fetch();
    }



}