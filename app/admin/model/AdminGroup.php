<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\model;

use think\Model;
class AdminGroup extends Model
{



    public function getRulesAttr($value, $data){


      return  explode(',',$value);

    }

    public function setRulesAttr($value, $data){


        return trim($value,',');



    }

}