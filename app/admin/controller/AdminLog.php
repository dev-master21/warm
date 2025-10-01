<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;

class AdminLog extends AdminBase
{
    public function _config(){
        $this->modules = 'admin_log';
        $this->pk = 'admin_log_id';
    }
}