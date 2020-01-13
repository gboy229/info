<?php

namespace app\content\service;
use think\Model;
class Content extends Model{

    public function initialize()
    {

        $this->model=model('content/Content');

    }


    public function get_list($sqlmap,$order='sort asc,id desc',$page=[]){



        $map=$this->search_map(input('param.'));

        $sqlmap=array_merge($sqlmap,$map['sqlmap']);

        if($map['order']) $order=$map['order'];

        $lists = $this->model->where($sqlmap)->order($order)->paginate();
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
        if(isset($data['status']) && $data['status']!='') $map['sqlmap']['display']=(int) $data['status'];
        if(isset($data['catid']) && $data['catid']) $map['sqlmap']['path_id']=['like','%,'.$data['catid'].',%'];
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



    public function get_lists($params){
        extract($params);
        $sqlmap=$this->search_map($params);

        $map=[];
        $map['display']=1;
	
			
		unset($sqlmap['order']);
			
		 $order=trim( $order);
			
        $sqlmap=array_merge($sqlmap['sqlmap'],$map);
	
		
	
       $result=$this->model->field($field)->where($sqlmap)->order($order)->paginate($page,$simple);
		
	
		
        $results=[];
        $results['list']=$result->toArray();
        $results['page']=$result->render();



       return  $results;

    }

}