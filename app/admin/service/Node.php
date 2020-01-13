<?php 
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */
 
namespace app\admin\service;
use think\Model;

class Node extends Model
{
	public function __construct(){
	    
	    
	    $this->model = model('admin/Node');
	    
	}
	
	
	public function fetch_all_by_ids($ids='',$status='1'){
	    
	    $_map = [];
	    if($ids) {
	        $_map['id'] = array("IN", explode(",", $ids));
	    }

	    $_map['status'] = $status;
	    $_map['nav_show'] = 1;

	    $result = $this->model->where($_map)->order('sort ASC,id ASC')->select()->toarray();
	    return $this->_format($result); 
	    
	}
	
	private function _format($data) {
	    if(empty($data)) return false;
	    $result = [];
	    foreach($data as $k => $v) {
	        $v['url'] = $v['url'] ? $v['url'] : url($v['m'].'/'.$v['c'].'/'.$v['a'], $v['param']);
	        $result[$k] = $v;
	    }
	    return $result;
	}


	public function get_parent_node($sqlmap){

        static $info;

        $node= $this->model->where($sqlmap)->find();

	    if($node){

	        $info[$node['id']]=$node;

	        $this->get_parent_node(['id'=>$node['parent_id']]);

            $info=array_reverse($info);

            sort($info);
        }

        return $info;
    }



    public function ztree(){

        $result = $this->model->where(['status'=>1])->order('sort ASC,id ASC')->field('id,name,parent_id')->select()->toArray();

        return $result;

    }

}

?>