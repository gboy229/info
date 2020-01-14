<?php

namespace app\member\service;
use think\Model;

class MemberLog extends Model{

    public function initialize()
    {
       $this->model = model('member/MemberLog');
    }


    public function get_list($sqlmap){


        $lists = $this->model->where($sqlmap)->order('id desc')->paginate($page);
        return $lists;

    }

    public function build_sqlmap($params){
        $sqlmap=[];

        if($params['start']) {
            $time[] = ["GT", strtotime($params['start'])];
        }
        if($params['end']) {
            $time[] = ["LT", strtotime($params['end'])];
        }
        if($time){
            $sqlmap['dateline'] = $time;
        }

        if($params['type']){
            $sqlmap['type'] = $params['type'];
        }

        if($params['keywords']){

            if(is_numeric($params['keywords'])){
                if(is_mobile($params['keywords'])){
                    $sqlmap['user']=$params['keywords'];
                }else{
                    $sqlmap['mid']=$params['keywords'];
                }

            }else{
                $sqlmap['realname']=$params['keywords'];
            }
        }




        return $sqlmap;
    }



}