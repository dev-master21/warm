<?php
namespace app\index\controller;
use think\facade\Db;
use think\facade\View;

class Index extends Base
{
    public function index()
    {
        // $index_info = Db::name('index_info')->order('index_info_sort DESC')->select();
        // View::assign('index_info', $index_info);

        $banner = Db::name('banner')->order('banner_sort DESC')->select();
        View::assign('banner', $banner);

        return view('/index');
    }
    
    public function about()
    {
        $info = Db::name('chip')->where('chip_type', '关于我们')->order('chip_id ASC')->select();
        View::assign('info', $info);
        return view('/about');
    }

    public function contact()
    {
        return view('/contact');
    }

    public function clubs_submit()
    {
        // if (input('post.clubs_first_name')) {
        //     $data['clubs_first_name'] = input('post.clubs_first_name');
        // } else {
        //     return error('Please enter First name');
        // }
        // if (input('post.clubs_last_name')) {
        //     $data['clubs_last_name'] = input('post.clubs_last_name');
        // } else {
        //     return error('Please enter Last name');
        // }
        // if (input('post.clubs_email')) {
        //     $data['clubs_email'] = input('post.clubs_email');
        // } else {
        //     return error('Please enter Email address');
        // }
        // if (input('post.clubs_country_of_residence')) {
        //     $data['clubs_country_of_residence'] = input('post.clubs_country_of_residence');
        // } else {
        //     return error('Please select Country of residence');
        // }
        // if (input('post.clubs_passport_country_of_issue')) {
        //     $data['clubs_passport_country_of_issue'] = input('post.clubs_passport_country_of_issue');
        // } else {
        //     return error('Please select Passport country of issue');
        // }
        
        if (input('post.clubs_email')) {
            $data['clubs_email'] = input('post.clubs_email');
        } else {
            return error('Please enter Email address');
        }

        $data['clubs_addtime'] = time();
        Db::name('clubs')->insert($data);
        return success('Join Clubs Success');
    }
    
    public function contact_submit(){
        $data = input('post.');

        if (empty($data['contact_email'])){
            return error('Please enter Email address');
        }
        if (empty($data['contact_country'])){
            return error('Please enter Country of residence');
        }
        if (empty($data['contact_message'])){
            return error('Please enter Message');
        }
        
        $data['contact_addtime'] = time();
        Db::name('contact')->insert($data);
        return success('Submit Contact Success');
    }
}