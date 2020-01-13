<?php


namespace app\member\controller;
use app\common\controller\Init;

class Message extends Init{

    public function _initialize()
    {

        parent::_initialize();
        $this->service = model('member/Message','service');

    }




    public function index(){

        $sqlmap=[];

        $get=input('get.');

        if($get['keywords']){

            if(is_numeric($get['keywords'])){
                if(is_mobile($get['keywords'])){
                    $sqlmap['user']=$get['keywords'];
                }else{
                    $sqlmap['mid']=$get['keywords'];
                }

            }else{
                $sqlmap['content']=$get['keywords'];
            }
        }

        if($get['type']!=''){
            $sqlmap['type']=$get['type'];
        }
        if($get['status']!=''){
            $sqlmap['status']=$get['status'];
        }
        if($get['is_look']!=''){
            $sqlmap['is_look']=$get['is_look'];
        }
		if($get['re_time']!=''){
			if($get['re_time']=='0'){
				$sqlmap['re_time']=0;
			}else{
				$sqlmap['re_time']=['neq',''];
			}
            
        }else{
			$sqlmap['re_time']=0;
		}

        $list=$this->service->get_list($sqlmap);



        $this->assign('list',$list);
        return $this->fetch();
    }




    public function edit(){

        $info=$this->service->get_find(['id'=>input('id/d')]);

        if(!$info){
            showmessage($this->service->errors);
        }

        //$this->service->edit(['id'=>$info['id'],'is_look'=>1],true);
		
		db('message')->where(['id'=>$info['id']])->data(['is_look'=>1])->update();

        if(is_post()){
			
			
            if(!$this->service->edit(input('post.'),true)){
                showmessage($this->service->errors);
            }

            showmessage(Lang('_operation_success_'), url('index'),1);
        }else{

            $this->assign('info',$info);
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