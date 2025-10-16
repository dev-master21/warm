<?php
// 成功时调用
if(!function_exists('success'))
{
    function success($info, $data = [])
    {
        return json([
            'status' => 1,
            'msg'    => $info,
            'data'   => $data,
        ]);
    }
}
// 失败时调用
if(!function_exists('error'))
{
    function error($info, $data = [])
    {
        return json([
            'status' => 0,
            'msg'    => $info,
            'data'   => $data,
        ]);
    }
}
// 检测是否是手机
function is_mobile() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
    $is_mobile = false;
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }
    return $is_mobile;
}

function text2html($string)
{
    return str_replace("\n", "<br>", $string);
}

// 遍历目录
function traverse_directory($dir) {
    $files = array();
    $handle = opendir($dir);
    while (false !== ($file = readdir($handle))) {
        if ($file !== '.' && $file !== '..') {
            $path = $dir.'/'.$file;
            if (is_dir($path)) {
                $files = array_merge($files, traverse_directory($path));
            } else {
                $files[] = $path;
            }
        }
    }
    closedir($handle);
    return $files;
}
// 校验身份证号码格式是否正确
function check_idcard($idcard){
    $idcard = strtoupper($idcard);
    if (!preg_match('#^\d{17}(\d|X)$#', $idcard)) {
        return false;
    }
    // 判断出生年月日的合法性(解决号码为666666666666666666也能通过校验的问题)
    $birth = substr($idcard, 6, 8);
    if ($birth < "19000101" || $birth > date("Ymd")) {
        return false;
    }
    $year = substr($birth, 0, 4);
    $month = substr($birth, 4, 2);
    $day = substr($birth, 6, 2);
    if (!checkdate($month, $day, $year)) {
        return false;
    }
    // 校验身份证格式(mod11-2)
    $check_sum = 0;
    for ($i = 0; $i < 17; $i++) {
        // $factor = (1 << (17 - $i)) % 11;
        $check_sum += $idcard[$i] * ((1 << (17 - $i)) % 11);
    }
    $check_code = (12 - $check_sum % 11) % 11;
    $check_code = $check_code == 10 ? 'X' : strval($check_code);
    if ($check_code !== substr($idcard, -1)) {
        return false;
    }
    return true;
}

function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    $tree = array();
    if(is_array($list)) {
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

function curl_post($url, $params = array(), $header = array()){
    $ch = curl_init();
    if(is_array($params)){
        $urlparam = http_build_query($params);
    } else if(is_string($params)){
        $urlparam = $params;
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $urlparam);
    if($header){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function img_thumb($url, $width, $height = 'auto')
{
    return '/storage/'.$url;
    
    if (strpos($url, '.jpg') || strpos($url, '.png')) {
        $path     = getcwd().'/storage/'.$url;
        $new_path = str_replace(['.jpg', '.png'], ["-{$width}.jpg", "-{$width}.png"], $path);
        if (!file_exists($new_path)) {
            try {
                $image  = \think\Image::open($path);
                if ($height == 'auto') {
                    $height = $image->height() * $width / $image->width();
                }
                $image->thumb($width, $height, \think\Image::THUMB_CENTER)->save($new_path);
                $base64 = base64_encode(file_get_contents($new_path));
                return 'data:image/jpeg;base64,' . $base64;
            } catch (Exception $e) {
                return '/storage/'.$url;
            }
        }
        $new_path = explode('/storage/', $new_path);
        return '/storage/'.$new_path[1];
    } else {
        return '/storage/'.$url;
    }
}