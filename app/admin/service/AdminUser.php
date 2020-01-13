<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\service;

use think\Model;

class AdminUser extends Model
{

    public function initialize()
    {


        $this->model = model('admin/AdminUser');
    }


    public function get_lists($sqlmap = [],$order='id asc')
    {


        $list = $this->model->where($sqlmap)->order($order)->select()->toArray();


        return $list;


    }

    /**
     * @param string $id
     * @return bool
     */
    public function fetch_by_id($id)
    {

        $id = (int)$id;

        if (!$this->model->where(['id' => $id])->find()) {
            $this->errors = L('admin_user_not_exist', 'admin');
            return false;
        }

        return $this->model->where(['id' => $id])->find()->toArray();

    }


    /**
     * @param $data 数据
     * @param bool $isupdate 是否更新
     * @param bool $valid 是否验证
     * @param array $msg 验证信息
     * @return bool
     */
    public function edit($data, $isupdate = false, $valid = true, $msg = [])
    {


        $data = array_filter($data);
		
		
        if ($isupdate) {

            unset($data['username']);

            if (!isset($data['id'])) {
                $this->errors = L('_param_error_', 'admin');
                return false;
            }

            if (isset($data['password'])) {
                $data['encrypt'] = random(10);
            }

            if($data['id']==1){
                unset($data['group_id']);
            }


        } else {

            $data['encrypt'] = random(10);
        }
	
	
	
		unset($data['formhash']); 

        $result = $this->model->except('id')->validate($valid, $msg)->isupdate($isupdate)->save($data);

        if ($result === false) {

            $this->errors = $this->model->getError();
            return false;
        }

        return TRUE;
    }


    /**
     * 生成登陆密码
     * @param string $pwd 原始密码
     * @param string $encrypt 混淆字符
     * @return string
     */
    public function create_password($pwd, $encrypt = '')
    {
        if (empty($encrypt)) $encrypt = random(6);
        return md5($pwd . $encrypt);

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

        if(in_array(1, $ids)){
            $this->errors = lang('del_default_admin_user_error');
            return false;
        }

        $_map = [];
        if(is_array($ids)) {
            $_map['id'] = array("IN", $ids);
        } else {
            $_map['id'] = $ids;
        }
        $result = $this->model->destroy($ids);
  


        return true;
    }

}