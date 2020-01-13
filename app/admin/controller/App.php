<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\controller;
use app\common\controller\Init;

class App extends Init{

    public function _initialize()
    {
        parent::_initialize();

        $this->service = model('admin/App','service');
    }

    public function index(){


        if(input('status')!=''){
            $status=input('status/d');
        }else{
            $status=1;
        }

        $modules = $this->service->lists($status,'module');
        $this->assign('lists',$modules);
        return $this->fetch();
    }



    public function config(){


        $module=input('module/s');

        if(is_post()){

            $data=[];
            $data['domain']=input('domain');
            $data['id']=input('id/d');
            if(!$this->service->config($data)){
                showmessage($this->service->errors);
            }
            showmessage(lang('_operation_success_'),url('index'),1);

        }else{

            $module=$this->service->get_find($module);
            $this->assign('module',$module);
            return $this->fetch();
        }



    }


    public function update(){

        showmessage(lang('_operation_success_'),url('index'),1);
    }



    public function toggle(){

        $module=input('module/s');

        if(!$this->service->toggle($module)){
            showmessage($this->service->errors);
        }
        showmessage(Lang('_operation_success_'), url('index'),1);

    }



}