<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\service;
use think\Model;

class District extends Model
{

    public function initialize()
    {

        $this->model = model('admin/District');
    }


    public function get_lists($parent_id = 0,$order = 'sort ASC,id ASC'){
        $lists = $this->model->where(array('parent_id' => $parent_id))->order($order)->select();

        return $lists;
    }

    public function get_find($id) {
        return $this->model->where(['id'=>$id])->find();
    }

    /**
     * 获取指定地区的所有上级地区数组
     * @param int $id 地区主键ID
     * @return array
     */
    public function fetch_parents($id, $isclear = true) {
        static $position;
        if($isclear === true) $position = [];
        $r = $this->get_find($id);
        if($r && $r['parent_id'] > 0) {
            $position[] = $r;
            $this->fetch_parents($r['parent_id'], FALSE);
        }
        if($r['parent_id'] == 0){
            $position[] = $r;
        }
        return $position;
    }

    /**
     * 获取指定地区完整路径
     * @param int $id 地区ID
     * @param string $filed 字段
     * @return array
     */
    public function fetch_position($id, $filed = 'name') {
        $position = $this->fetch_parents($id);
        krsort($position);
        $result = [];
        foreach($position AS $pos) {
            $result[] = $pos[$filed];
        }
        return $result;
    }


    public function edit($data,$isupdate=false){

        if(empty($data)) {
            $this->errors = lang('_param_error_');
            return false;
        }


        $result=$this->model->allowField(true)->isupdate($isupdate)->save($data);

        if($result===false){
            $this->errors=$this->model->getError();
            return false;
        }

        return true;

    }

    /**
     * 获取指定地区所有下级地区
     * @param int $id 地区ID
     */
    public function fetch_all_childrens_by_id($id = 0, $isself = 1) {
        static $ids = [];
        if($isself == 1) {
            $ids[] = $id;
        }
        $rs = $this->model->where(['parent_id' => $id])->column('id');
        if($rs) {
            $ids = array_merge($ids, $rs);
            foreach($rs AS $id) {
                $this->fetch_all_childrens_by_id($id, 0);
            }
        }
        return $ids;
    }

    /**
     * 删除指定地区
     * @param type $ids
     * @return boolean
     */
    public function del($ids = array()) {
        if(empty($ids) || !is_array($ids)){
            $this->errors = lang('_param_error_');
            return false;
        }
        $district_ids = [];
        foreach($ids AS $id) {
            $district_ids = array_merge($district_ids, $this->fetch_all_childrens_by_id($id));
        }

        if($district_ids) {
            $_map = [];
            $_map['id'] =["IN", $district_ids];
            $result = $this->model->where($_map)->delete();

        }
        return true;
    }


}

