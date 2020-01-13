<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\service;
use think\Model;

class App extends Model{

    public function initialize()
    {

        $this->model = model('admin/App');

    }


    public function lists($status = 1 , $type='modeule'){

        //$app_path = $type == 'plugin' ? PLUGIN_PATH : MODULES_PATH;
        $lists = $this->model->where(['identifier' => ['like',$type.'.%'],'available'=>$status])->select()->toArray();
        return $lists;

    }

    public function get_find($module_name) {

        $module_name='module.'.$module_name;

        return $this->model->where(['identifier'=>$module_name])->find();
    }



    public function config($data){

        $result = $this->model->validate(true)->isupdate(true)->save($data);

        if ($result === false) {
            $this->errors = $this->model->getError();
            return false;
        }

        return true;
    }






    public function toggle($module_name){


        $module = $this->get_find($module_name);

        $status=(int)!$module['available'];

        $this->model->where(['identifier'=>$module['identifier']])->data(['available'=>$status])->update();

        return true;

    }



}