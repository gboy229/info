<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\model;


use think\Model;

class AdminUser extends Model
{


    protected $append = ['group_name'];


    protected static function init()
    {

        self::beforeInsert(function ($data) {

            $service = new  \app\admin\service\AdminUser;

            return $data['password'] = $service->create_password($data->password, $data['encrypt']);


        });

        self::beforeUpdate(function ($data) {

            $service = new  \app\admin\service\AdminUser;

            if (isset($data->password)) {
                return $data['password'] = $service->create_password($data->password, $data['encrypt']);
            }


        });



    }


    public function getLastLoginTimeAttr($value, $data)
    {
        if (empty($value)) {

            $time = '-';
        } else {
            $time = getdatetime($value);
        }

        return $time;
    }

    public function getGroupNameAttr($value, $data)
    {


        return $data['group_id'] == 1 ? '超级管理员' : model('admin/AdminGroup')->where('id',$data['group_id'])->value('title');
    }


}