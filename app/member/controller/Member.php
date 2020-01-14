<?php

namespace app\member\controller;
use app\common\controller\Init;

class Member extends Init{

    public function _initialize()
    {

        parent::_initialize();
        $this->service = model('member/Member','service');
        $this->group_service = model('member/MemberGroup','service');
    }




    public function index(){

        $sqlmap=[];

        $get=input('get.');

        if($get['keywords']){

            if(is_numeric($get['keywords'])){
                if(is_mobile($get['keywords'])){
                    $sqlmap['username']=$get['keywords'];
                }else{
                    $sqlmap['id']=$get['keywords'];
                }

            }else{
                $sqlmap['realname']=$get['keywords'];
            }
        }


        if($get['is_lock']!=''){
            $sqlmap['is_lock']=$get['islock'];
        }

        if($get['group_id']){
            $sqlmap['group_id']=$get['group_id'];
        }

        $list=$this->service->get_list($sqlmap);

        $member_group=$this->group_service->get_column();
        $member_group[0]='所有等级';
        ksort($member_group);
        $this->assign('list',$list)->assign('member_group',$member_group);
        return $this->fetch();
    }


    public function add(){

        exit('404');
        if(is_post()){

            if(!$this->service->edit(input('post.'))){
                showmessage($this->service->errors);
            }

            showmessage(Lang('_operation_success_'), url('index'),1);
        }else{

            $member_group=$this->group_service->get_column();
            $member_group[0]='未认证';
            $this->assign('member_group',$member_group);
            return $this->fetch('edit');
        }


    }


    public function edit(){

        $info=$this->service->get_find(['id'=>input('id/d')]);

        if(is_post()){

            $validate = new \app\member\validate\Member();

            if(!$validate->scene('edit')->check(input('post.'))) {
                showmessage($validate->getError());
            }


            if(!$this->service->edit(input('post.'),true,false)){
                showmessage($this->service->errors);
            }

            showmessage(Lang('_operation_success_'), url('index'),1);
        }else{

            $member_group=$this->group_service->get_column();





            $this->assign('member_group',$member_group)->assign('info',$info);
            return $this->fetch();
        }

    }

    public function delete()
    {


        showmessage('禁止删除操作');
        $ids = input('param.id/a');

        if(!$this->service->del($ids)) {
            showmessage($this->service->errors);
        }

        showmessage(lang('_operation_success_'), url('index'), 1);
    }



    public function login(){

        $uid=input('id/d');

        if(!db('member')->where(['id'=>$uid])->value('id')){
            showmessage('此账号不存在');
        }

        $rand = random(6);
        $login_key=random(32);
        $admin_login=authcode('gboy_admin','ENCODE');
        $auth = authcode($uid."\t".$rand."\t".$login_key."\t".$admin_login, 'ENCODE');
        session('gboy_member_auth', $auth);
		//session('my_login_time',time());
        showmessage('登录成功',url('member/index/index'),1);
    }


    public function money(){


        $id=input('id/d');

        if(!$info=model('member/Member')->where(['id'=>$id])->find()){
            showmessage('会员不存在');
        }






        if(is_post()){


            $msg=input('msg','');
            $wallets=input('');

            unset($wallets['id'],$wallets['formhash'],$wallets['msg']);

            if(empty(array_filter($wallets))){
                showmessage('请输入金额');
            }



            foreach ($wallets as $k=>$v){
                $v=trim($v);
                if($v>0 || $v<0){
                    $this->service->change_account($id,trim($k),$v,$msg,true);
                }
            }

            showmessage('操作成功','',1);

        }else{

            $this->assign('info',$info);
            return $this->fetch();
        }


    }






    public function tree(){

        if(is_post()){
            $id=input('id/d');


            $list=$this->service->tree($id);

            echo $list;

        }else{

            $arr=['name'=>'团队列表','id'=>0,'pid'=>0,'isParent'=>true];
            $id=input('keywords/d');

            if($id){
                $arr['name']='ID：'.$id.'的团队';
                $arr['id']=$id;
            }

            $this->assign('arr',json_encode($arr));
            return $this->fetch();
        }


    }







	
	
	public function lock(){
		
		
		$id=input('id/d');

        if(!$info=$this->service->where(['id'=>$id])->find()){
            showmessage('会员不存在');
        }


		db('member')->where(['id'=>$info['id']])->data(['is_lock'=>0,'lock_time'=>0])->update();
		

		showmessage('操作成功',url('index'),1);
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}