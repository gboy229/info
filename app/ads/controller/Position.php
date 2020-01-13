<?php


namespace app\ads\controller;
use app\common\controller\Init;

class Position extends Init{

    public function _initialize()
    {
        parent::_initialize();

        $this->service = model('ads/AdsPosition','service');
    }


    public function index(){

        $sqlmap=[];

        $list=$this->service->get_list($sqlmap);

        $this->assign('list',$list);
        return $this->fetch();
    }



    public function add(){


        if(is_post()){

            if (!$this->service->edit(input('post.'))) {
                showmessage($this->service->errors);
            }
            showmessage(Lang('_operation_success_'), url('index'),1);

        }else{

            return $this->fetch('edit');


        }


    }


    public function edit(){

        if(is_post()){
            if (!$this->service->edit(input('post.'),true)) {
                showmessage($this->service->errors);
            }
            showmessage(Lang('_operation_success_'), url('index'),1);

        }else{

            $id=input('id/d');
            $info=$this->service->get_by_id(['id'=>$id]);


            $this->assign('info',$info);
            return $this->fetch();

        }

    }


    public function delete()
    {

        if(!$this->service->del(input('param.id/a'))) {
            showmessage($this->service->errors);
        }

        showmessage(lang('_operation_success_'), url('index'), 1);
    }


}