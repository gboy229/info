<?php

namespace app\content\controller;
use app\common\controller\Base;
use think\Request;

class Info extends Base{

    public function __construct()
    {
        parent::__construct();

        $this->member_service=model('member/Member','service');
    }


    public function details(Request $request){


        $route=$request->route();
        $itemid=(int)  trim($route['itemid']);


        if(!$itemid || (!$info=model('content/Content')->where(['id'=>$itemid])->find())){
            $this->redirect('/','',302);
        }

        if($this->member['id']!=$info['uid'] && $info['display']!=3){

            $this->redirect('/','',302);

        }



       db('content')->where(['id'=>$info['id']])->setInc('hits',1);

        $user_info=model('member/Member')->where(['id'=>$info['uid']])->find();
        $this->assign('info',$info)->assign('user_info',$user_info);
        return $this->fetch();

    }


    public function news_details(Request $request){

        $route=$request->route();
        $itemid=(int)  trim($route['id']);

        if(!$itemid || (!$info=model('content/Content')->where(['id'=>$itemid,'display'=>3,'path_id'=>['like','%,4,%']])->find())){
            $this->redirect('/','',302);
        }

        db('content')->where(['id'=>$info['id']])->setInc('hits',1);

        $this->assign('info',$info);
        return $this->fetch();
    }




  

}