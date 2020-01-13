<?php


namespace app\attachment\controller;
use app\common\controller\Init;

class Index extends Init{

    public function _initialize()
    {
        parent::_initialize();

        $this->service = model('attachment/Attachment', 'service');

    }


    /* 编辑器图片上传 */
    public function editor() {

        exit('');
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($_SERVER['DOCUMENT_ROOT']."/static/js/editor/php/config.json")), true);
        $action = $_GET['action'];

        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':

                /* 上传文件 */
            case 'uploadfile':

                $url = $this->service->config(['mid' => ADMIN_ID])->upload('upfile');
                $result=json_encode(array(
                    "state" => "SUCCESS",          //上传状态，上传成功时必须返回"SUCCESS"
                    "url" => $url,            //返回的地址
                    "title" => "",          //原始文件名
                    "original" =>"",        //新文件名"original" => ""
                    "type" => "",           //文件类型
                    "size" => "",           //文件大小
                ));


                break;
            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }


        exit( $result);


    }


    public function upload(){

        if (!$picurl = $this->service->config(['mid' => ADMIN_ID,'dir'=>'abc'])->upload('upfile')) {
            showmessage($this->service->errors);
        }

        showmessage(lang('upload_success'), '', 1, $picurl, 'json');
    }


    public function webuploader(){


        return $this->fetch();
    }

    public function ajax_webuploader(){


        $url = $this->service->config(['mid' => ADMIN_ID,'dir'=>'abc'])->upload('file');


       showmessage('','',1,$url,'json');

    }

}