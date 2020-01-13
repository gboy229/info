<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */
namespace app\admin\model;
use think\Model;

class App extends Model{

    protected $append=['ico','module_name'];



    public function getIcoAttr($value,$data)
    {


        $identifier=explode('.',$data['identifier']);
        $ico=__ROOT__.'static/images/'.$identifier[0].'/'.$identifier[1].'.png';

        return $ico;
    }


    public function getModuleNameAttr($value,$data){

        $identifier=explode('.',$data['identifier']);

        return $identifier[1];


    }


}