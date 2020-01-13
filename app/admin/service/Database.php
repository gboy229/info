<?php
/**
 *
 * 本软件不得用于商业用途，仅做学习交流。
 *
 */

namespace app\admin\service;
use think\Model;
use \tp5er\Backup;
class Database extends Model
{

    public $config=[];

    public function initialize()
    {

        $this->config=array(
            'path'     => ROOT_PATH.'data/dbbak/',//数据库备份路径
            'part'     => 20971520,//数据库备份卷大小
            'compress' => 1,//数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 9 //数据库备份文件压缩级别 1普通 4 一般  9最高
        );

        $this->db=new Backup($this->config);

    }


    public function data_list(){

        return $this->db->dataList();

    }


    public function file_ist(){

        return $this->db->fileList();

    }


    public function export($data){


        if(!empty($data['sql_file_name'])){
            $filename= ['name' => $data['sql_file_name'], 'part' => 1];
        }else{
            $filename=null;
        }

        if(!empty($data['vol_size'])){

            $this->config['part']=$data['vol_size']*1024;

        }


        $this->db=new Backup($this->config);


        switch($data['type']){
            case 0:
                $tables= $this->db->dataList();
            break;

            case 1:
                $tables_string='gboy_goods_category,gboy_goods_index,gboy_goods_sku,gboy_goods_spu,gboy_member,gboy_member_deposit,gboy_category,gboy_content';

                $table=explode(',',$tables_string);

                foreach($table as $k=>$v){
                    $tables[$k]=['name'=>$v];
                }



            break;

            case 2:
                $tables_string='gboy_goods_index,gboy_goods_sku,gboy_goods_spu,gboy_member';

                $table=explode(',',$tables_string);

                foreach($table as $k=>$v){
                    $tables[$k]=['name'=>$v];
                }

            break;

            case 3:
                $tables= $data['tables'];

                if(!$tables || !is_array($tables)){

                    $this->errors='请选择要备份的数据表';
                    return false;
                }else{
                    foreach($tables as $k=>$v){
                        $tables[$k]=['name'=>$v];
                    }
                }

                break;
        }


        foreach($tables as $k=>$table){

            if(false===$this->db->setFile($filename)->backup($table['name'], 0)){
               $this->errors=$table['name'].'备份失败';
               return false;
            }
        }

        return true;


    }


    public function file_list(){

        return $this->db->fileList();
    }


    public function download($time){

        return $this->db->downloadFile($time);
    }


    public function delfile($time){

        return $this->db->delFile($time);
    }

    public function optimize($tables){

        return $this->db->optimize($tables);
    }

    public function repair($tables){

        return $this->db->repair($tables);
    }



}