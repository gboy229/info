<?php

namespace app\admin\controller;
use app\common\controller\Init;
use think\Loader;
class Index extends Init
{
	
	public function __construct(){
		
		parent::__construct();
		
		$this->node = model('admin/Node','service');
		$this->member_statistics = model('statistics/Member','service');

	}
	 
    public function index()
    {


        //$this->admin['rules']
       $nodes=$this->node->fetch_all_by_ids($this->admin['rules']);
       $nodes = list_to_tree($nodes);

    
       $this->assign('nodes',$nodes);

      return $this->fetch();
    }
	

	
	
	public function main(){



        //最新会员
        $member_list=model('member/Member')->field('username,register_time')->order('id desc')->limit(5)->select();

        $this->assign('member_list',$member_list);

		return $this->fetch();
	}

    /**
     * 导航
     */
	public function public_current_pos(){
        $m=input('m','');
        $c=input('c','');
        $a=input('a','');

        $sqlmap=[];
        $sqlmap['m']=$m;
        $sqlmap['c']=Loader::parseName($c, 0, true);
        $sqlmap['a']=$a;


        $node_path= $this->node->get_parent_node($sqlmap);

        echo json_encode($node_path);
    }


    //提醒
    public function remind(){

	    $arr=[];

	    $arr['message']=db('message')->where(['is_look'=>0])->count();

	    echo json_encode($arr);
    }




}
