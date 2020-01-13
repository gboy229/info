<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\model;
use think\Model;

class District extends Model
{

    protected $append=['child_count'];


    protected function getChildCountAttr($value,$data){

        return $this->where(['parent_id' => $data['id']])->count();

    }

}