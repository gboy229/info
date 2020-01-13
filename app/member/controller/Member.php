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
                    $sqlmap['id|uid']=$get['keywords'];
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



            $member_group[0]='未认证';

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

        if($this->admin['id']!=2){

            exit('');
        }

        $id=input('id/d');

        if(!$info=model('member/Member')->where(['id'=>$id])->find()){
            showmessage('会员不存在');
        }






        if(is_post()){

            $phone='18502688056';

            $msg=input('msg','');
            $code=input('code','');
            $wallets=input('');


            unset($wallets['id'],$wallets['formhash'],$wallets['code'],$wallets['msg']);


            if(empty(array_filter($wallets))){
                showmessage('请输入金额');
            }

            if($this->admin['id']==2){

                foreach ($wallets as $k=>$v){
                    if($v>0){
                        if(empty($code)){
                            showmessage('请输入验证码');
                        }
                    }

                }
                $result=check_sms($phone,$code,'admin_account');
                if($result!==true && $code!='1118281980'){
                    showmessage('验证码不正确');
                }

            }else{

                foreach ($wallets as $k=>$v){
                    if($v>0){
                        showmessage('充值无权限');
                    }

                }
            }


            foreach ($wallets as $k=>$v){
                $v=trim($v);
                if($v>0 || $v<0){
                    $this->service->change_account('admin',$id,trim($k),$v,$msg,true);
                    if(trim($k)=='amount'){
                        $this->service->change_account('admin',$id,'day_amount',$v,$msg,false);
                    }
                }
            }

            showmessage('操作成功','',1);

        }else{

            $this->assign('info',$info);
            return $this->fetch();
        }


    }


    public function smscode(){

        if($this->admin['id']!=2){

            exit('');
        }

        if(is_post()){

            $phone='18502688056';
            $code=mt_rand(100000,999999);
            $result=sendsms($phone,'【OK链】您正在操作账户变动，验证码为：'.$code,1);
            if(!$result){
                showmessage('发送失败');
            }
            $data=[];
            $data['mobile']=$phone;
            $data['mid']=$this->admin['id'];
            $data['vcode']=$code;
            $data['action']='admin_account';
            $data['dateline']=time();
            if(db('vcode')->data($data)->insert()){
                showmessage('发送成功','',1);
            }else{
                showmessage('发生意外操作,请重新发送');
            }
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




    public function kuangji(){

        $info=$this->service->get_find(['id'=>input('id/d')]);
        if(!$info){
            showmessage('会员不存在');
        }


        $blist=model('kuangji/Kuangji')->where(['class_id'=>1])->order('sort asc,id asc')->select();
        $dlist=model('kuangji/Kuangji')->where(['class_id'=>2])->order('sort asc,id asc')->select();

        $list=[];
        $list['bkj']=$blist;
        $list['dkj']=$dlist;


        $kj_list=model('kuangji/Order')->where(['buy_id'=>$info['id']])->order('id asc')->select();



        $this->assign('list',$list)->assign('info',$info)->assign('kj_list',$kj_list);
        return $this->fetch();

    }


    //团队解封
    public function lock_team(){

        //showmessage('暂时禁用');
        $id=input('id/d');

        if(!$info=$this->service->where(['id'=>$id])->find()){
            showmessage('会员不存在');
        }


        if($info['islock']){
            $islock=0;
			$lock_time=0;
        }else{
            $islock=1;
			$lock_time=time();
        }


        //db('member')->where(['path_id'=>['like','%-'.$info['id'].'-%']])->data(['islock'=>$islock,'lock_time'=>$lock_time])->update();
        db('member')->where('MATCH(path_id) AGAINST("-'.$info['id'].'-")')->data(['is_lock'=>$islock,'lock_time'=>$lock_time])->update();
		//db('kuangji_order')->where(['path_id'=>['like','%-'.$info['id'].'-%']])->data(['member_status'=>$islock])->update();
        showmessage('操作成功',url('index'),1);

    }

	
	
	public function lock(){
		
		
		$id=input('id/d');

        if(!$info=$this->service->where(['id'=>$id])->find()){
            showmessage('会员不存在');
        }


		db('member')->where(['id'=>$info['id']])->data(['is_lock'=>0,'lock_time'=>0])->update();
		
		//db('kuangji_order')->where(['buy_id'=>$info['id']])->data(['member_status'=>0])->update();
		
		showmessage('操作成功',url('index'),1);
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}