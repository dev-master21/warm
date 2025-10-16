<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;

class AdminQuick extends AdminBase
{
    public static function select_options($table, $where, $value_field, $name_field, $order = '')
    {
        $options = [];
        $rows = Db::name($table)->where($where)->order($order)->select()->toArray();
        if ($rows) {
            foreach($rows as $v){
                $options[] = $v[$value_field].':'.$v[$name_field];
            }
        } else {
            $options[] = '0:未设置';
        }
        return implode(';', $options);
    }

    public static function select_options_fid($table, $where, $value_field, $name_field, $fid, $order = '')
    {
        $options = [];
        $rows = Db::name($table)->where($where)->order($order)->select()->toArray();
        foreach ($rows as $k => $v) {
            $rows[$k]['children'] = Db::name($table)->where($fid,$v[$value_field])->column($name_field,$value_field);
            foreach ($rows[$k]['children'] as $key => $value) {
                $rows[$k]['children'][$key] = $key.'='.$value;
            }
        }
        foreach($rows as $v){
            $value = implode(',',$v['children']);
            $options[] = $v['industry_id'].':'.$v['industry_name'].':'.$value;
        }
        return implode(';', $options);
    }
}