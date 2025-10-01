<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Booking extends AdminBase
{
    public function _config(){
        $this->modules    = 'booking';
        $this->pk         = 'booking_id';
        $this->base_where = [];
        $this->order      = 'booking_id DESC';
        $this->top_btn    = "";

        $this->search = [
            "booking_first_name" => 'input|First Name',
            "booking_last_name" => 'input|Last Name',
            "booking_email" => 'input|Email',
            "booking_phone_number" => 'input|Phone',
            "booking_country_of_residence" => 'input|Country',
        ];

        $this->column = [
            ['title'=>'#', 'field'=>'booking_id','width'=>60],
            ['title'=>'First', 'field'=>'booking_first_name'],
            ['title'=>'Last', 'field'=>'booking_last_name'],
            ['title'=>'Email', 'field'=>'booking_email'],
            ['title'=>'Phone', 'field'=>'booking_phone_number'],
            ['title'=>'Arrival', 'field'=>'booking_date_of_arrival'],
            ['title'=>'Departure', 'field'=>'booking_date_of_departure'],
            ['title'=>'Flexibility', 'field'=>'booking_flexibility_of_dates'],
            ['title'=>'Country', 'field'=>'booking_country_of_residence'],
            ['title'=>'Adults', 'field'=>'booking_number_of_adults'],
            ['title'=>'Children', 'field'=>'booking_number_of_children'],
            ['title'=>'Children Age', 'field'=>'booking_age_of_children'],
            ['title'=>'Required Bedrooms', 'field'=>'booking_required_number_of_bedrooms'],
            ['title'=>'Budget', 'field'=>'booking_budget_per_night'],
            ['title'=>'Purpose', 'field'=>'booking_purpose_of_stay'],
            ['title'=>'Form', 'field'=>'booking_form'],
            ['title'=>'Addtime', 'field'=>'booking_addtime','width'=>160],
            ['field'=>'sys_action','align'=>'left','width'=>120,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [
            ['type'=>'text','title'=>'First', 'field'=>'booking_first_name'],
            ['type'=>'text','title'=>'Last', 'field'=>'booking_last_name'],
            ['type'=>'text','title'=>'Email', 'field'=>'booking_email'],
            ['type'=>'text','title'=>'Phone', 'field'=>'booking_phone_number'],
            ['type'=>'text','title'=>'Arrival', 'field'=>'booking_date_of_arrival'],
            ['type'=>'text','title'=>'Departure', 'field'=>'booking_date_of_departure'],
            ['type'=>'text','title'=>'Flexibility', 'field'=>'booking_flexibility_of_dates'],
            ['type'=>'text','title'=>'Country', 'field'=>'booking_country_of_residence'],
            ['type'=>'text','title'=>'Adults', 'field'=>'booking_number_of_adults'],
            ['type'=>'text','title'=>'Children', 'field'=>'booking_number_of_children'],
            ['type'=>'text','title'=>'Children Age', 'field'=>'booking_age_of_children'],
            ['type'=>'text','title'=>'Required Bedrooms', 'field'=>'booking_required_number_of_bedrooms'],
            ['type'=>'text','title'=>'Budget', 'field'=>'booking_budget_per_night'],
            ['type'=>'text','title'=>'Purpose', 'field'=>'booking_purpose_of_stay'],
            ['type'=>'text','title'=>'Form', 'field'=>'booking_form'],
            ['type'=>'textarea','title'=>'Comments', 'field'=>'booking_comments'],
        ];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['booking_addtime'] = date('Y-m-d H:i:s', $v['booking_addtime']);
            $rows[$k]['sys_action'] = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?booking_id={$row['booking_id']}', '查看', ['1000px', '800px'])\">查看</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>