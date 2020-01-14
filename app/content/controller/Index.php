<?php

namespace app\content\controller;
use app\common\controller\Base;
use think\Request;

class Index extends Base{

    public function __construct()
    {
        parent::__construct();

        $this->member_service=model('member/Member','service');
    }



    public function index(){


        $category_list=db('category')->where(['id'=>['in',[1,2,3]],'display'=>1])->field('id,name,dir_name')->order('sort desc,id desc')->select();

        foreach ($category_list as $k=>$v){
            $category_list[$k]['list']=model('Content/content')->where(['category_id'=>$v['id'],'display'=>3])->order('id desc')->limit(15)->select();
        }


        $news=model('Content/content')->where(['display'=>1,'path_id'=>['like','%,4,%']])->order('id desc')->limit(15)->select();

        $this->assign('category_list',$category_list)->assign('news',$news);
        return $this->fetch();

    }


    public function category(Request $request){


        $route=$request->route();

        $name=trim($route['name']);

        $cid=(int) input('cid/d');
        $did=(int) input('did/d');

        if($name){
            $category=db('category')->where(['dir_name'=>$name,'display'=>1])->field('id,name,dir_name')->find();
        }


        $category_list=db('category')->where(['id'=>['in',[1,2,3]],'display'=>1])->field('id,name,dir_name')->order('sort desc,id desc')->select();


        $sqlmap=[];
        if($category){
            $sqlmap['category_id']=$category['id'];
        }
        $sqlmap['display']=3;


        if($cid){
            $sqlmap['cid_path']=['like','%,'.$cid.',%'];
            $cid_name=db('district')->where(['id'=>$cid])->value('name');
        }
        if($did){
            $sqlmap['did_path']=['like','%,'.$did.',%'];
            $did_name=db('district')->where(['id'=>$did])->value('name');
        }

        $list=model('Content/content')->where($sqlmap)->order('id desc')->paginate();


        $this->assign('category_list',$category_list)->assign('list',$list)->assign('name',$name)->assign('category',$category);
        $this->assign('cid_name',$cid_name)->assign('did_name',$did_name);
        return $this->fetch();
    }



    public function news(){

        $sqlmap=[];

        $sqlmap['path_id']=['like','%,4,%'];
        $sqlmap['display']=1;
        $list=model('Content/content')->where($sqlmap)->order('id desc')->paginate();

        $this->assign('list',$list);

        return $this->fetch();

    }


    public function search(){


        return $this->fetch();
    }


    public function search_list(){

        $name=trim(input('name/s'));
        $kw=trim(input('kw/s'));

        $category=db('category')->where(['dir_name'=>$name,'display'=>1])->field('id,name,dir_name')->find();

        $list=[];
        if($category && !in_array($kw,['*','%','/','\\'])){

            $sqlmap=[];
            $sqlmap['category_id']=$category['id'];
            $sqlmap['display']=3;
            $sqlmap['title']=['like','%'.$kw.'%'];
            $list=model('Content/content')->where($sqlmap)->order('id desc')->paginate();
        }


        $this->assign('list',$list)->assign('category',$category);
        return $this->fetch();
    }




  

}