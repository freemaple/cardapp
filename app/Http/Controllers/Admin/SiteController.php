<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Notice\Notice as NoticeModel;
use App\Cache\Notice as NoticeCache;
use App\Models\User\PhoneCode;
use App\Models\Site\Config as SiteConfig;
use App\Models\User\GoldDaySta;
use App\Models\Order\Recharge as OrdereRechargeModel;
use App\Models\Gold\GoldDayConfig;
use App\Models\User\Gold as UserGoldModel;
use App\Models\Gold\GoldDaySta as GoldDayStaModel;
use App\Models\Gold\GoldDayUser as GoldDayUserModel;

class SiteController extends BaseController
{

     /**
     * 后台系统首页
     *
     * @return void
     */
    public function notice(Request $request)
    {
        $NoticeModel = new NoticeModel();

        $pageSize = 20;

        $form = $request->all();

        $location = trim($request->location);

        if($location != null){
            $NoticeModel = $NoticeModel->where('location', '=', $location);
        }

        $enabled = $request->enabled;

        if(isset($request->enabled) && $enabled !==''){
            $NoticeModel = $NoticeModel->where('enabled', '=', $enabled);
        }

        $notices = $NoticeModel->orderBy('created_at', 'desc')->paginate($pageSize);

        $notices->appends($request->all());

        $pager = $notices->links();

        $locations = config('notice.location');

        $view = View('admin.site.notice');

        $view->with("notices", $notices);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("locations", $locations);

        $view->with("title", "通知公告");

        return $view;
    }

      /**
     * 后台系统首页
     *
     * @return void
     */
    public function loadNotice(Request $request)
    {
       

        $result = ['code' => '2x1'];

        $id = $request->id;

        $NoticeModel = NoticeModel::where('id', $id)->first();
        if($NoticeModel == null){
            $result['message'] = '通知不存在！';
            return json_encode($result);
        }

        $result['code'] = '200';

        $result['data'] = $NoticeModel;

        $result['message'] = '获取成功';

        return json_encode($result);

    }

     /**
     * 后台系统首页
     *
     * @return void
     */
    public function saveNotice(Request $request)
    {
       

        $result = ['code' => '2x1'];

        $id = $request->id;

        $location = trim($request->location);

        $save_type = $request->save_type;

        if($save_type == '0'){
            $NoticeM = NoticeModel::where('location', $location)->first();
            if($NoticeM != null){
                $result['message'] = '此位置通知已存在！';
                return json_encode($result);
            }
            $NoticeModel = new NoticeModel();
            $NoticeModel->location = $location;
        } else{
            $NoticeModel = NoticeModel::where('id', $id)->first();
            if($NoticeModel == null){
                $result['message'] = '通知不存在！';
                return json_encode($result);
            }
        }

       
        $content = trim($request->content);

        $enabled = $request->enabled;

        $NoticeModel->content = $content;

        $NoticeModel->enabled = $enabled == '1' ? '1' : '0';

        $NoticeModel->save();

        NoticeCache::clearNoticeCache($NoticeModel->location);

        $result['code'] = '200';

        $result['message'] = '保存成功';

        return json_encode($result);

    }

    /**
     * 后台系统首页
     *
     * @return void
     */
    public function phonecode(Request $request)
    {
        $PhoneCodeModel = new PhoneCode();

        $pageSize = 20;

        $form = $request->all();

        $phone = trim($request->phone);

        if($phone != null){
            $PhoneCodeModel = $PhoneCodeModel->where('phone', '=', $phone);
        }

        $type = trim($request->type);

        if($type != null){
            $PhoneCodeModel = $PhoneCodeModel->where('type', '=', $type);
        }

        $phonecodes = $PhoneCodeModel->orderBy('created_at', 'desc')->paginate($pageSize);

        $phonecode_type = config('user.phonecode_type');

        $phonecodes->appends($request->all());

        $pager = $phonecodes->links();

        $view = View('admin.site.phonecode');

        $view->with("phonecodes", $phonecodes);

        $view->with("phonecode_type", $phonecode_type);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "验证码");

        return $view;
    }


     /**
     * 后台系统首页
     *
     * @return void
     */
    public function siteConfig(Request $request)
    {
        $config = SiteConfig::first();

        $view = View('admin.site.config');

        $view->with("config", $config);

        $view->with("title", "站点配置");

        return $view;
    }

     /**
     * 后台系统首页
     *
     * @return void
     */
    public function saveConfig(Request $request)
    {
        $config = SiteConfig::first();

        if($config == null){
            $config = new SiteConfig();
        }

        $config->store_integral_send_open = $request->store_integral_send_open;

        $config->store_integral_send_amount = $request->store_integral_send_amount;

        $config->vip_integral_send_open = $request->vip_integral_send_open;

        $config->vip_integral_send_amount = $request->vip_integral_send_amount;

        $config->save();

        return redirect(route("admin_siteconfig"));
    }


     /**
     * 后台系统首页
     *
     * @return void
     */
    public function goldConfig(Request $request)
    {
        $date = date("Y-m-d");

        $GoldDayConfig = GoldDayConfig::where('date', '=', $date)->first();

        $goldDaySta = GoldDaySta::first();

        $goldDaySta['remaining_gold'] = $goldDaySta['should_issued_amount'] - $goldDaySta['actual_issued_amount'];

        if($goldDaySta['remaining_gold'] <0){
            $goldDaySta['remaining_gold'] = 0;
        }

        $yesterday_date = date("Y-m-d",strtotime("-1 day"));

        $yesterday_date_start = $yesterday_date . ' 00:00:00';
        $yesterday_date_end = date("Y-m-d") . ' 00:00:00';

        $yesterday_gift = OrdereRechargeModel::where('gift_id', '>', 0)
        ->where('order_type', 'vip')
        ->where('status', '=', '2')
        ->where('paid_at', '>=', $yesterday_date_start)
        ->where('paid_at', '<', $yesterday_date_end)
        ->count();

        $yesterday_gold_amount = OrdereRechargeModel::where('gift_id', '>', 0)
        ->where('order_type', 'vip')
        ->where('status', '=', '2')
        ->where('paid_at', '>=', $yesterday_date_start)
        ->where('paid_at', '<', $yesterday_date_end)
        ->sum('gold_amount');

        $GoldDayStaModel = GoldDayStaModel::where('date', $yesterday_date)->first();

        $user_gold_numbers = 0;

        $available_gold_number = 0;

        $user_un_gold_numbers = 0;

        if(!empty($GoldDayStaModel)){
            $user_gold_numbers = $GoldDayStaModel->gold_number;
            $available_gold_number = $GoldDayStaModel->available_gold_number;
            $user_un_gold_numbers = $user_gold_numbers - $available_gold_number;
        }

        $available_user_count = GoldDayUserModel::where('date', $yesterday_date)->count('user_id');

        $view = View('admin.site.goldconfig', [
            'GoldDayConfig' => $GoldDayConfig,
            'goldDaySta' => $goldDaySta,
            'yesterday_gift' => $yesterday_gift,
            'yesterday_gold_amount' => $yesterday_gold_amount,
            'user_gold_numbers' => $user_gold_numbers,
            'available_gold_number' => $available_gold_number,
            'user_un_gold_numbers' => $user_un_gold_numbers,
            'available_user_count' => $available_user_count,
            'title' => "站点配置"
        ]);

        return $view;
    }

    /**
     * 后台系统首页
     *
     * @return void
     */
    public function saveGoldConfig(Request $request)
    {
        $date = date("Y-m-d");

        $GoldDayConfig = GoldDayConfig::where('date', '=', $date)->first();

        if($GoldDayConfig == null){
            $GoldDayConfig = new GoldDayConfig();
            $GoldDayConfig->date = $date;
        }

        $GoldDayConfig->bouns_amount = $request->bouns_amount;

        $GoldDayConfig->save();

        $result = ['code' => '200'];

        return response()->json($result);
    }


}