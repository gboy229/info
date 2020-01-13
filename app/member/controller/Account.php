<?php

namespace app\member\controller;


class Account extends Check {

    public function __construct()
    {
        parent::__construct();

        $this->member_service=model('member/Member','service');
    }



    public function details(){

        return $this->fetch();

    }


    public function edit(){

        if(is_ajax()){

            $data=[];
            $data['realname']=trim(input('realname'));
            $data['sex']=(int) (input('sex'));
            $data['email']=trim(input('email'));

            if(empty($data['realname'])){
                showmessage('请输入姓名');
            }

            if(!in_array($data['sex'],[0,1,2])){
                showmessage('性别选择错误');
            }

            if(empty($data['email'])){
                showmessage('请输入邮箱');
            }

            if(!is_email($data['email'])){
                showmessage('邮箱格式不正确');
            }

            db('member')->where(['id'=>$this->member['id']])->data($data)->update();
            showmessage('修改成功',url('/member'),1);

        }else{
            return $this->fetch();
        }


    }


}