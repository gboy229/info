<?php


namespace app\content\model;
use think\Model ;

class Content extends Model{

    protected $insert=['add_time'];
    protected $update=['update_time'];
    protected $append=['category_name','user_type','username'];


    protected static function init()
    {

        self::beforeInsert(function ($data) {
            return $data['category_id']=$data['menuid'];
        });

        self::beforeInsert(function ($data) {
            $category=model('content/Category')->field('id,path_id')->getById($data['menuid']);
            return $data['path_id']=$category['path_id'];
        });




    }

    protected function setAddTimeAttr(){
        return time();
    }

    protected function setUpdateTimeAttr(){
        return time();
    }


    protected function getCategoryNameAttr($value,$data){
       return  model('content/Category')->where(['id'=>$data['category_id']])->value('name');
    }

    protected function getAddTimeAttr($value,$data){

         return getdatetime($value,'Y-m-d');

    }


    protected function getUserTypeAttr($value,$data){

        $text='';
        if($data['uid']){
            if($data['uid']==1){
                $text='个人';
            }else{
                $text=db('member')->where(['id'=>$data['uid']])->value('company');
            }
        }

        return $text;

    }

    protected function getUsernameAttr($value,$data){

        $text='';
        if($data['uid']){
            $text=db('member')->where(['id'=>$data['uid']])->value('username');
        }

        return $text;

    }


    protected function getContentAttr($value,$data){

        return nl2br($value);
    }


}
