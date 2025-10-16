<?php
namespace app\index\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;
use app\BaseController;

class Base extends BaseController
{
    public function __construct()
    {
        // $category = Db::name('category')->order('category_sort DESC')->where('category_is_show', 1)->select()->toArray();
        // $category_tree = list_to_tree($category, 'category_id', 'category_pid', '_child', 0);
        // View::assign('category_tree', $category_tree);

        $villas_city = Db::name('villas')->group('villas_city')->column('villas_city');
        View::assign('villas_city', $villas_city);
        
        $villas_tag = Db::name('villas_tag')->select();
        View::assign('villas_tag', $villas_tag);

        $config = Db::name('config')->column('*', 'config_id');
        View::assign('config', $config);

        $country = ['Afghanistan','Aland','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica','Antigua and Barbuda','Argentina','Armenia','Aruba','Australia','Austria','Azerbaijan','Bahamas, The','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia','Botswana','Bouvet Island','Brazil','British Virgin Islands','Brunei','Bulgaria','Burkina Faso','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Chad','Chile','China','Christmas Island','Cocos (Keeling) Islands','Colombia','Comoros','Congo, Democratic Republic of the (Congo – Kinshasa)','Cook Islands','Costa Rica','Croatia','Cuba','Curacao','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Faroe Islands','Fiji','Finland','France','French Guiana','French Polynesia','Gabon','Gambia, The','Georgia','Germany','Ghana','Gibraltar','Greece','Greenland','Grenada','Guadeloupe','Guam','Guatemala','Guernsey','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hong Kong','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Isle of Man','Israel','Italy','Jamaica','Japan','Jersey','Jordan','Kazakhstan','Kenya','Kiribati','Kosovo','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Macau','Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Martinique','Mauritania','Mauritius','Mayotte','Mexico','Micronesia','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Myanmar (Burma)','Namibia','Nauru','Nepal','Netherlands','Netherlands Antilles','New Caledonia','New Zealand','Nicaragua','Niger','Nigeria','Niue','Norfolk Island','North Korea','Northern Mariana Islands','Norway','Oman','Pakistan','Palau','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Pitcairn Islands','Poland','Portugal','Pridnestrovie (Transnistria)','Puerto Rico','Qatar','Reunion','Romania','Russia','Rwanda','Saint Helena','Saint Kitts and Nevis','Saint Lucia','Saint Pierre and Miquelon','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Korea','Spain','Sri Lanka','Sudan','Suriname','Svalbard','Swaziland','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Togo','Tokelau','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','U.S. Virgin Islands','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Wallis and Futuna','Western Sahara','Yemen','Zambia','Zimbabwe'];
        View::assign('country', $country);

        $other_list = Db::name('villas')->where('villas_status', 1)->orderRaw('RAND()')->limit(8)->select()->toArray();
        foreach ($other_list as $k => $v) {
            $other_list[$k] = Villas::_detail_format($v);
        }
        View::assign('other_list', $other_list);
        
        View::assign('resource_version', '202412151702');
        // View::assign('resource_version', time());
    }

    public function upload()
    {
        $file = request()->file('file');
        $savename = \think\facade\Filesystem::disk('public')->putFile('', $file, 'data');
        return success('上传成功', ['file'=>str_replace("\\", "/", $savename)]);
    }
    
    public function update_villas_price()
    {
        $ymd   = date('Y-m-d');
        $lists = Db::name('villas_price_plan')->where('villas_price_plan_stime', '<=', $ymd)->where('villas_price_plan_etime', '>=', $ymd)->select();
        foreach ($lists as $v) {
            Db::name('villas')->where('villas_id', $v['villas_id'])->update([
                'villas_original_price' => $v['villas_price_plan_original'],
                'villas_price'          => $v['villas_price_plan_now']
            ]);
        }
    }
}