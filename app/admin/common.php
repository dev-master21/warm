<?php
function region($pid){
    $where = [];
    if($pid !== ''){
        $where['region_pid'] = $pid;
    }
    return think\facade\Db::name('region')->where($where)->select();
}

function region_name($region_id){
    return think\facade\Db::name('region')->where('region_id',$region_id)->value('region_name');
}

function list_img_format($string) {
    if($string){
        if(substr($string, 0, 4) == 'http'){
            return '<img src="'.$string.'" height="28" onclick="preview_img(this)" style="cursor:pointer">';
        } else {
            return '<img src="'.img_thumb($string, 1200).'" height="28" onclick="preview_img(this)" style="cursor:pointer">';
        }
    }
}

function list_img_format_some($string) {
    if($string && is_array($string)){
        $html = '';
        foreach ($string as $k => $v) {
            if(substr($v, 0, 4) == 'http'){
                $html .= '<img src="'.$v.'" height="28" onclick="preview_img(this)" style="cursor:pointer; margin-right:5px;">';
            } else {
                $html .= '<img src="'.img_thumb($v, 1200).'" height="28" onclick="preview_img(this)" style="cursor:pointer; margin-right:5px;">';
            }
        }
        return $html;
    }
    return '';
}