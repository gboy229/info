<?php

namespace org;
use OSS\OssClient;
use OSS\Core\OssException;
class Uploader
{

    protected $config = [
        'path' => 'uploads',
        'size' => 2097152,
        'ext' => ['jpg', 'png', 'jpeg'],
        'mid' => 0,
        'dir' => 'abc',

    ];

    public function __construct($config = [])
    {

        $this->config = array_merge($this->config, $config);


    }

    public function upload($filename)
    {

        if (isset($_FILES[$filename])) {

            //$file = request()->file($filename);
            $file = $_FILES[$filename];

            if(!empty($file['error'])){
                $this->errors='上传失败了';
                return false;
            }


            $name=$file['name'];
            $format = strrchr($name, '.');


            $allow_type = ['.jpg', '.jpeg','.png'];
            if (!in_array($format, $allow_type)) {
                $this->errors='格式不正确';
                return false;
            }


            if($file["type"]!="image/png" && $file["type"]!="image/jpeg"){
                $this->errors='格式不正确';
                return false;
            }

            if($file["size"]>2097152){
                $this->errors='图片大小不能超过2M';
                return false;
            }

            if($file["size"]<2048){
                $this->errors='图片大小不正确';
                return false;
            }

            $file = request()->file($filename);
            $info = $file->validate($this->config)->move($this->config['path']);
            if ($info) {
                return $info;
            } else {
                $this->errors = $file->getError();
                return false;
            }
        }
        $this->errors = lang('file_upload_empty');
        return false;
    }

}