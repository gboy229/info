<?php

namespace app\member\controller;


class Wallet extends Check {

    public function __construct()
    {
        parent::__construct();

        $this->member_service=model('member/Member','service');
    }



    public function index(){


        $info=db('member_group')->where(['id'=>$this->member['group_id']])->field('day_count,cost,top_cost')->find();

        $this->assign('info',$info);
        return $this->fetch();

    }


    public function details(){



        $list=model('member/MemberLog')->where(['mid'=>$this->member['id']])->field('value,dateline,msg')->order('id desc')->paginate();

        $this->assign('list',$list);
        return $this->fetch();
    }




}