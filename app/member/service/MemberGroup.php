<?php


namespace app\member\service;
use think\Model;

class MemberGroup extends Model{

    public function initialize()
    {
       $this->model = model('member/MemberGroup');
    }


    public function get_list($sqlmap,$order='sort asc,id asc',$page=[]){

        $lists = $this->model->where($sqlmap)->order($order)->paginate($page);
        return $lists;

    }

    public function get_column($sqlmap=[],$field='id,name'){

       return  $this->model->where($sqlmap)->column($field);
    }




    public function get_find($sqlmap=[]){

        if(!$result=$this->model->where($sqlmap)->find()){
            $this->errors=lang('_param_error_');
            return false;
        }

        return $result;

    }


    public function edit($data, $isupdate = false, $valid = true, $msg = []){

        $result = $this->model->except('id')->validate($valid, $msg)->isupdate($isupdate)->save($data);


        if ($result === false) {

            $this->errors = $this->model->getError();
            return false;
        }

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