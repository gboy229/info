<?php

namespace app\member\controller;

use app\common\controller\Init;

class MemberGroup extends Init
{

    public function _initialize()
    {
        parent::_initialize();

        $this->service = model('member/MemberGroup', 'service');

    }


    public function index()
    {

        $sqlmap = [];
        $list = $this->service->get_list($sqlmap);
        $this->assign('list', $list);
        return $this->fetch();
    }


    public function add()
    {


        if(is_post()) {

            if(!$this->service->edit(input('post.'))) {
                showmessage($this->service->errors);
            }
            showmessage(Lang('_operation_success_'), url('index'), 1);

        } else {

            return $this->fetch('edit');
        }

    }


    public function edit()
    {

        $sqlmap = [];
        $sqlmap['id'] = input('id/d');
        if(!$info = $this->service->get_find($sqlmap)) {
            showmessage(lang('_param_error_'));
        }

        if(is_post()) {

            if(!$this->service->edit(input('post.'), true)) {
                showmessage($this->service->errors);
            }
            showmessage(Lang('_operation_success_'), url('index'), 1);


        } else {


            $this->assign('info', $info);
            return $this->fetch();
        }


    }


    public function delete()
    {

        $ids = input('param.id/a');

        if(!$this->service->del($ids)) {
            showmessage($this->service->errors);
        }

        showmessage(lang('_operation_success_'), url('index'), 1);
    }


}