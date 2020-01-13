<?php


namespace app\ads\service;
use think\Model;
class Ads extends Model{

    public function initialize()
    {

        $this->model=model('ads/Ads');

    }

    /**
     * @param        $sqlmap 条件
     * @param string $field 字段
     * @return mixed
     */
    public function get_by_id($sqlmap, $field = '')
    {
        $info = $this->model->get($sqlmap)->toArray();

        if($field) {
            return $info[$field];
        }

        return $info;
    }



    public function get_list($sqlmap,$order='sort asc,id desc',$page=[]){



        $map=$this->search_map(input('param.'));

        $sqlmap=array_merge($sqlmap,$map['sqlmap']);

        if($map['order']) $order=$map['order'];

        $lists = $this->model->where($sqlmap)->order($order)->paginate($page);
        return $lists;

    }

    public function edit(array $data, $isupdate = false, $valid = true, $msg = []){


        $result = $this->model->except('id')->validate($valid, $msg)->isupdate($isupdate)->save($data);
        if ($result === false) {
            $this->errors = $this->model->getError();
            return false;
        }

        return true;
    }


    private function search_map($data){

        $map=[];
        $map['sqlmap']=[];
        $map['order']=[];
        if(isset($data['keywords']) && $data['keywords'] ) $map['sqlmap']['title']=['like','%'.$data['keywords'].'%'];
        if(isset($data['start_time']) && $data['start_time']  ) $map['sqlmap']['add_time']=['>=',strtotime($data['start_time'])];
        if(isset($data['end_time']) && $data['end_time']) $map['sqlmap']['add_time']=['<=',strtotime($data['end_time'])];
        if(isset($data['status']) && $data['status']!='') $map['sqlmap']['status']=(int) $data['status'];
        if(isset($data['order']) && $data['order']) $map['order']=$data['order'].' desc';

        return $map;
    }


    /**
     * @param array $ids id主键
     * @return bool
     */
    public function del($ids){


        if(empty($ids)){

            $this->errors = lang('_param_error_');
            return false;
        }

        $_map = [];
        if(is_array($ids)) {
            $_map['id'] = array("IN", $ids);
        } else {
            $_map['id'] = $ids;
        }
        $this->model->destroy($ids);

        return true;
    }


}