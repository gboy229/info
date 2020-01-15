<?php

namespace app\member\controller;

use app\common\controller\Base;
use think\Request;

class Info extends Base {

    public function __construct()
    {
        parent::__construct();

        $this->member_service=model('member/Member','service');



        $status=(int) config('cache.switch_tourists');

        if(!$status){
            if($this->member['id'] < 1){
                $url_forward = input('url_forward') ? input('url_forward') : request()->server('REQUEST_URI');
                $this->redirect('/member/login',['url_forward'=>$url_forward],302);
            }
        }


    }



    public function category(){

        $category=db('category')->where(['id'=>['in',[1,2,3]],'display'=>1])->field('id,name,dir_name')->order('sort desc,id desc')->select();

        $this->assign('category',$category);
        return $this->fetch();

    }

    public function lists(Request $request){



        $this->check_login();



        $route=$request->route();

        $name=trim($route['name']);

        $status=(int) input('status/d');
        $kw=trim(input('kw/s'));

        if($this->member['id'] < 1){
            $this->redirect('/member/info/add',['name'=>$name],302);
        }

        $category=db('category')->where(['dir_name'=>$name,'display'=>1])->field('id,name,dir_name')->find();
        if(!$name || !$category){
            $this->redirect('/member/info/category','',302);
        }


        $status_arr=[1=>'审核中',2=>'未通过',3=>'已发布',4=>'过期'];

        $statistics=[];
        foreach ($status_arr as $k=>$v){
            $statistics[$k]=model('content/Content')->where(['uid'=>$this->member['id'],'category_id'=>$category['id'],'display'=>$k])->count(1);
        }


        $sqlmap=$list=[];


        $sqlmap['uid']=$this->member['id'];
        $sqlmap['category_id']=$category['id'];
        if($status && in_array($status,[1,2,3,4])){
            $sqlmap['display']=$status;
        }
        if($kw && !in_array($kw,['*','/','\\'])){
            $sqlmap['title']=['like','%'.$kw.'%'];
        }
        $list=model('content/Content')->where($sqlmap)->field('id,thumb,title,add_time')->order('id desc')->paginate();


        $this->assign('category',$category)->assign('list',$list)->assign('status',$status)->assign('statistics',$statistics);
        return $this->fetch();

    }


    public function add(){

        $name=input('name/s');

        $category=db('category')->where(['dir_name'=>$name,'display'=>1])->field('id,name,dir_name,path_id')->find();
        if(is_ajax()){


            $check_moone=false;
            if($this->member['id']){
                $group=db('member_group')->where(['id'=>$this->member['group_id']])->find();
                $sqlmap = [];
                $sqlmap['uid'] = $this->member['id'];
                $sqlmap['add_time'] = ['between', [strtotime(date('Y-m-d')), strtotime(date('Y-m-d 23:59:59'))]];
                $day_add_count = db('content')->where($sqlmap)->count(1);
                if ($day_add_count >= $group['day_count']) {
                    if($group['cost']>0){
                        if( $this->member['money']<$group['cost']){
                            showmessage('余额不足，请充值');
                        }
                        $check_moone=true;
                    }
                }
            }



            if(!$category){
                showmessage('发布类型不正确');
            }

            $cid_path=$did_path='';
            $cid=(int) input('cid/d');
            $did=(int) input('did/d');
            if($cid){
                if(!db('district')->where(['id'=>$cid])->value('id')){
                    showmessage('分类不存在');
                }
                $cid_path=sprintf('%s'.implode(',',$this->get_path($cid)).'%s',',',',');
            }
            if($did){
                if(!db('district')->where(['id'=>$did])->value('id')){
                    showmessage('分类不存在');
                }
                $did_path=sprintf('%s'.implode(',',$this->get_path($did)).'%s',',',',');
            }

            $data=[];
            $data['title']=htmlentities(remove_xss(trim(input('title/s'))));
            $data['category_id']=$category['id'];
            $data['path_id']=$category['path_id'];
            $data['cid']=$cid;
            $data['cid_path']=$cid_path;
            $data['did']=$did;
            $data['did_path']=$did_path;
            $data['content']=htmlentities(remove_xss(trim(input('content/s'))));
            $data['add_time']=time();
            $data['update_time']=time();
            $data['display']=1;
            $data['uid']=$this->member['id'];


            if(mb_strlen($data['title'])<5) showmessage('标题太少了');
            if(mb_strlen($data['title'])>30) showmessage('标题太长了');
            if(empty($data['content'])) showmessage('请输入内容');

            db('content')->startTrans();
            $this->member_service->startTrans();
            $result=db('content')->insertGetId($data);
            if( $check_moone){
                $money=$this->member_service->change_account($this->member['id'],'money','-'.$group['cost'],'发布信息');
            }else{
                $money=true;
            }
            if($result && $money){
                db('content')->commit();
                $this->member_service->commit();
                showmessage('发布成功，请等待审核',url('/member/info/lists/'.$category['dir_name']),1);
            }else{
                db('content')->rollback();
                $this->member_service->rollback();
                showmessage('发布失败，请重新操作');
            }

        }else{

            if(!$name || !$category){
                $this->redirect('/member/info/category','',302);
            }

            $this->assign('category',$category);

            return $this->fetch();
        }





    }



    public function edit(){





        $this->check_login();

        $name=input('name/s');
        $itemid=(int) input('itemid/d');



        $category=db('category')->where(['dir_name'=>$name,'display'=>1])->field('id,name,dir_name')->find();

        $info=db('content')->where(['id'=>$itemid,'uid'=>$this->member['id']])->find();

        if(is_ajax()){
            if(!$category){
                showmessage('发布类型不正确');
            }
            if(!$info){
                showmessage('数据不存在');
            }

            $cid_path=$did_path='';
            $cid=(int) input('cid/d');
            $did=(int) input('did/d');
            if($cid){
                if(!db('district')->where(['id'=>$cid])->value('id')){
                    showmessage('分类不存在');
                }
                $cid_path=sprintf('%s'.implode(',',$this->get_path($cid)).'%s',',',',');
            }
            if($did){
                if(!db('district')->where(['id'=>$did])->value('id')){
                    showmessage('分类不存在');
                }
                $did_path=sprintf('%s'.implode(',',$this->get_path($did)).'%s',',',',');
            }


            $data=[];
            $data['title']=htmlentities(remove_xss(trim(input('title/s'))));
            $data['content']=htmlentities(remove_xss(trim(input('content/s'))));
            $data['cid']=$cid;
            $data['cid_path']=$cid_path;
            $data['did']=$did;
            $data['did_path']=$did_path;
            $data['display']=1;
            if(mb_strlen($data['title'])<5) showmessage('标题太少了');
            if(mb_strlen($data['title'])>30) showmessage('标题太长了');
            if(empty($data['content'])) showmessage('请输入内容');
            if(db('content')->where(['id'=>$info['id']])->data($data)->update()===false){
                showmessage('编辑失败，请重新操作');
            }else{
                showmessage('编辑成功，请等待审核',url('/member/info/lists/'.$category['dir_name']),1);
            }

        }else{
            if(!$name || !$category || !$info){
                $this->redirect('/member/info/category','',302);
            }

            $cid_path_arr=array_filter(explode(',',$info['cid_path']));
            sort($cid_path_arr);
            unset($cid_path_arr[0]);
            $info['cid_path']=implode(',',$cid_path_arr);

            $did_path_arr=array_filter(explode(',',$info['did_path']));
            sort($did_path_arr);
            unset($did_path_arr[0]);
            $info['did_path']=implode(',',$did_path_arr);


            $this->assign('category',$category)->assign('info',$info);

            return $this->fetch();
        }





    }


    public function copy(){


        $this->check_login();

        $itemid=(int) input('itemid');
        $info=db('content')->where(['id'=>$itemid,'uid'=>$this->member['id']])->find();
        $info['add_time']=time();
        unset($info['id'],$info['hits']);
        db('content')->insertGetId($info);
        showmessage('','',1);
    }


    public function top(){


        $this->check_login();



        $itemid=input('itemid/a');

        if( !$itemid ||  !is_array($itemid)){
            showmessage('请选择');
        }


        $group=db('member_group')->where(['id'=>$this->member['group_id']])->find();
        $check_money=false;
        if($group['top_cost']>0){
            $top_num=db('content')->where(['id'=>['in',$itemid],'uid'=>$this->member['id']])->count(1);
            $top_money=$top_num*$group['top_cost'];
            if( $this->member['money']<($top_money)){
                showmessage('余额不足，请充值');
            }
            $check_money=true;
        }

        $top_time=config('cache.top_time')*3600;


        db('content')->startTrans();


        $this->member_service->startTrans();
        $result=db('content')->where(['id'=>['in',$itemid],'uid'=>$this->member['id']])->setInc('update_time',$top_time);
        if( $check_money){
            $money=$this->member_service->change_account($this->member['id'],'money','-'.$top_money,'置顶信息');
        }else{
            $money=true;
        }

        if(!$result || !$money){
            db('content')->rollback();
            $this->member_service->rollback();
            showmessage('置顶失败');
        }else{
            db('content')->commit();
            $this->member_service->commit();
            showmessage('置顶成功','',1);
        }

    }


    public function delete(){

        $this->check_login();


        $itemid=input('itemid/a');

        if(is_array($itemid) && $this->member['id']){

            $sqlmap=[];
            $sqlmap['id']=['in',$itemid];
            $sqlmap['uid']=$this->member['id'];
            db('content')->where($sqlmap)->delete();
            showmessage('','',1);
        }
    }


    public function ajax(){


        $catid=(int) input('catid/d');

        $category=db('district')->where(['parent_id'=>$catid])->order('id asc')->field('id,name')->select();
        echo json_encode($category);

    }


    private function get_path($pid,&$path=[]){




        $path[]=$pid;
        $pid=db('district')->where(['id'=>$pid])->value('parent_id');


        if($pid){
            $this->get_path($pid,$path);
        }
        return $path;



    }

    private function check_login(){
        if($this->member['id'] < 1){
            $this->redirect('/member/login','',302);
        }
    }



  

}