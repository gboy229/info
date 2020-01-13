<?php


namespace app\statistics\controller;
use app\common\controller\Init;

class Member extends Init{

    public function _initialize()
    {

        parent::_initialize();
        $this->service = model('statistics/Member','service');
    }




    public function index(){

        $this->assign('member',$this->service->count());
        return $this->fetch();
    }




    public function ajax_getdata(){
        $row = $this->service->build_data(input('get.'));
        echo json_encode($row);
    }





}