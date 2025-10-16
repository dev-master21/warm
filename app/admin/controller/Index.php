<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\Controller;
use think\facade\Cookie;

class Index
{
    public function index()
    {
        return redirect('/admin2022.php/index/login.html');
    }

    public function login()
    {
        // if(!isset($_COOKIE['from_hash']) || !isset($_COOKIE['from_check'])){
        //     exit('请从入口文件进入');
        // }
        // if(md5('ZENCODING'.md5($_COOKIE['from_hash']).'ZENCODING') != $_COOKIE['from_check']){
        //     exit('请从入口文件进入');
        // }
        if (request()->isPost())
        {
            if(!captcha_check(request()->post('verify'))){
                return error('验证码错误');
            }
            $admin = Db::name('admin')->where('admin_account', input('account'))->where('admin_pwd', hash('sha256', input('password')))->find();
            if($admin){
                if($admin['admin_state'] == 1){
                    Cookie::set('admin_id', $admin['admin_id']);
                    Cookie::set('admin_hash', hash('sha256', $admin['admin_id'].request()->ip()));
                    return success('登录成功');
                } else {
                    return error('当前账号非激活状态，不可登录');
                }
            } else {
                return error('账号或密码错误');
            }
        } else {
            return view('/login');
        }
    }

    public function upload()
    {
        $file = request()->file('file');
        $savename = \think\facade\Filesystem::disk('public')->putFile('', $file, 'data');
        return success('上传成功', ['file'=>str_replace("\\", "/", $savename)]);

        // 图片按比例压缩，注意png透明背景会出现问题
        // if(in_array(strtolower(pathinfo($savename, PATHINFO_EXTENSION)), ['png','jpg','jpeg'])){
        //     $image     = \think\Image::open(getcwd().'/storage/'.$savename);
        //     $newWidth  = env('upload.image_max_width');
        //     $newHeight = $image->height() * $newWidth / $image->width();
        //     $image->thumb($newWidth, $newHeight)->save(getcwd().'/storage/'.$savename);
        // }
        // return success('上传成功', ['file'=>str_replace("\\", "/", $savename), 'name'=>$file->getOriginalName()]);
    }

    // 上传后保持原文件名存储（SEO使用）
    public function upload_original_name()
    {
        $file = request()->file('file');
        if (!preg_match("/^[a-zA-Z0-9_.]+$/", $file->getOriginalName())) {
            return error('上传失败：上传的文件名只能含有大小写字母，数字，下划线和.');
        }
        $savename = \think\facade\Filesystem::disk('public')->putFileAs(date('Ymd'), $file, str_replace(' ', '-', $file->getOriginalName()));
        return success('上传成功', ['file'=>str_replace("\\", "/", $savename)]);
    }

    public function ueditor_upload_image()
    {
        $file = request()->file('upfile');
        $savename = \think\facade\Filesystem::disk('public')->putFile('', $file, 'data');
        // if(in_array(strtolower(pathinfo($savename, PATHINFO_EXTENSION)), ['png','jpg','jpeg'])){
        //     $image     = \think\Image::open(getcwd().'/storage/'.$savename);
        //     $newWidth  = env('upload.image_max_width');
        //     $newHeight = $image->height() * $newWidth / $image->width();
        //     $image->thumb($newWidth, $newHeight)->save(getcwd().'/storage/'.$savename);
        // }
        $resp = [
            "state"    => "SUCCESS",
            "url"      => "http://".$_SERVER['HTTP_HOST'].'/storage/'.str_replace("\\", "/", $savename),
            "title"    => $file->getOriginalName(),
            "original" => $file->getOriginalName(),
            "type"     => '.'.$file->extension(),
            "size"     => $file->getSize()
        ];
        return json($resp);
    }

    public function ueditor_upload_video()
    {
        $file = request()->file('upfile');
        $savename = \think\facade\Filesystem::disk('public')->putFile('', $file, 'data');
        $resp = [
            "state"    => "SUCCESS",
            "url"      => "http://".$_SERVER['HTTP_HOST'].'/storage/'.str_replace("\\", "/", $savename),
            "title"    => $file->getOriginalName(),
            "original" => $file->getOriginalName(),
            "type"     => '.'.$file->extension(),
            "size"     => $file->getSize()
        ];
        return json($resp);
    }

    public function logout()
    {
        Cookie::delete('admin_id');
        Cookie::delete('admin_hash');
        return redirect('/admin2022.php/index/login');
    }

    public function region()
    {
        $region_pid = request()->post('region_pid');
        $region = Db::name('region')->where('region_pid','=',$region_pid)->select();
        return json($region);
    }
}