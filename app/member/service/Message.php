<?php


namespace app\member\service;
use think\Model;

class Message extends Model{

    public function initialize()
    {
       $this->model = model('member/Message');
    }


    public function get_list($sqlmap,$order='id desc'){

        $lists = $this->model->where($sqlmap)->order($order)->paginate();
        return $lists;

    }


    public function get_find($sqlmap=[]){

        if(!$result=$this->model->where($sqlmap)->find()){
            $this->errors=lang('_param_error_');
            return false;
        }

        return $result;

    }


    public function edit($data, $isupdate = false){

        if($data['re_content']){
            $data['re_time']=time();
        }else{
            $data['re_time']='';
        }
        $result = $this->model->except('id')->isupdate($isupdate)->save($data);

        return true;
    }



    /**
     * @param array $ids id主键
     * @return bool
     */
    public function del($sqlmap){


        if(empty($sqlmap)){

            $this->errors = lang('_param_error_');
            return false;
        }

        $this->model->destroy($sqlmap);

        return true;
    }


}