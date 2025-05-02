<?php

namespace app\app\controller;

use think\facade\Request;
use think\facade\Filesystem;

class Upload extends Base
{
    /**
     * 上传图片
     * @param file image 图片
     * @return array|false
    */
    public function uploadImage()
    {
        // 获取上传的文件
        $file = Request::file('file');
        
        if (!$file) {
            // 文件不存在，返回错误信息
            return json(['code' => 500, 'msg' => '未上传文件','data' => '']);
        }

        // 验证文件并移动到指定目录
        // validate(['image' => 'filesize:2048000|fileExt:jpg,png,gif'])->check(['image' => $file]);

        // 使用文件系统保存文件，并返回文件路径
        // $saveName = Filesystem::disk('local')->putFile('image',$file);
        $saveName = Filesystem::disk('public')->putFile('image',$file);
        
        if ($saveName) {
            // 文件保存成功，返回文件路径
            $filePath =  '/'.$saveName;  // https://hrbjltx.com/   http://47.120.28.114/
            $this->success('ok', ['url' => $filePath]);
            // return json(['code' => 200, 'msg' => '上传成功', 'data' => $filePath]);
        } else {
            // 文件保存失败，返回错误信息
            $this->error('上传失败');
            // return json(['code' => 0, 'msg' => '上传失败','data' => '']);
        }
    }
}
