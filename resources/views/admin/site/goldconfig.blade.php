@extends('admin.template.layout')
@section('content')
<style type="text/css">
    .thumbnail {
        margin-bottom: 10px;
        line-height: 18px;
        padding: 0px 4px;
    }
    .thumbnail .caption {
        padding: 0px 9px;
    }
</style>
<ul class="breadcrumb">
  	<li><a href="">站点管理</a></li>
    <li class="active">配置</li>
</ul>
<div class="well">
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
              <div class="caption">
                <h4>累计应发总额</h4>
                <p class="text-red" style="font-size: 24px">{{ $goldDaySta['should_issued_amount'] }}</p>
                <p>
                    <span class="text-info">说明：</span>
                    = 礼包总销量*每单400标准（每个礼包的红利金额），今天发放出去的马上结算
                </p>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
              <div class="caption">
                <h4>累计实发总额</h4>
                <p class="text-red" style="font-size: 24px">{{ $goldDaySta['actual_issued_amount'] }}</p>
                <p>
                    <span class="text-info">说明：</span>= 每天发放红利的累计，今天发放出去的马上结算
                </p>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
              <div class="caption">
                <h4>节省库存剩余</h4>
                <p class="text-red" style="font-size: 24px">{{ $goldDaySta['remaining_gold'] }}</p>
                <p>
                    <span class="text-info">说明：</span>
                    = 应发总额-实发总额，今天发放出去的马上结算
                </p>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="thumbnail">
              <div class="caption">
                <h4>昨天礼包销量</h4>
                <p><span class="text-red" style="font-size: 24px">{{ $yesterday_gift }}</span>个</p>
                <p>
                    <span class="text-info">说明：</span>每天易24点为标准，统计昨天的销量
                </p>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="thumbnail">
              <div class="caption">
                <h4>今天红利发放参考值</h4>
                <p><span class="text-red" style="font-size: 24px">{{ $yesterday_gold_amount }}<span>之内</p>
                <p>
                    <span class="text-info">说明：</span>
                    昨天礼包销量*400（每个礼包的红利金额）
                </p>
              </div>
            </div>
        </div>
    </div>
     <div class="row">
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
              <div class="caption">
                <h4>截至昨天24点金麦穗总量</h4>
                <p style="font-size: 24px"><span class="text-red">{{ $user_gold_numbers }}</span>支</p>
                <p>
                    <span class="text-info">说明：</span>
                    = 截至昨天24点所有用户手上的金麦穗总和
                </p>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
              <div class="caption">
                <h4>不可以参与金麦穗总量</h4>
                <p  style="font-size: 24px"><span class="text-red">{{ $user_un_gold_numbers }}</span>支</p>
                <p>
                    <span class="text-info">说明：</span>
                    = 昨天没有分享朋友圈的所有用户手上的金麦穗总和
                </p>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
              <div class="caption">
                <h4>今天可以参与金麦穗总量</h4>
                <p class="text-red" style="font-size: 24px"><span class="text-red">{{ $available_gold_number }}</span>支，（共{{ $available_user_count }}个金麦用户）</p>
                <p>
                    <span class="text-info">说明：</span>
                    = 昨天24点前分享朋友圈的所有用户手上的金麦穗总和
                </p>
              </div>
            </div>
        </div>
    </div>
</div>
<div class="well" style="width: 540px;margin: 20px auto">
    <fieldset>
        <legend>请设置今天红利设置</legend>
        <form class="form-horizontal" action="/admin/site/saveConfig" method="post">
            {!! csrf_field() !!}
            <div class="form-group">
                <label for="store_integral_send_amount" class="col-sm-3 control-label">红利金额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control day_bouns_amount"  value="{{ !empty($GoldDayConfig) ? $GoldDayConfig['bouns_amount'] : '' }}" name="bouns_amount"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">参与金麦穗数量:</label>
                <div class="col-sm-9">
                    <input type="text" disabled="disabled" class="form-control day_bouns_amount"  value="{{ $available_gold_number }}支" name="bouns_amount"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">每支获得：</label>
                <div class="col-sm-9">
                    <input type="text" disabled="disabled" class="form-control unit_gold"  value="" name="bouns_amount"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <div class="text-center">
                  <input type="button" id="day_gold_submit" @if($available_gold_number <=0) disabled="disabled" @endif class="btn btn-primary" value="确定发放" />
                </div>
            </div>
        </form>
    </fieldset>
</div>
@endsection
@section('scripts')
{!! App\Assets\Admin::script('admin/scripts/module/admin.js') !!}
<script type="text/javascript">
    $(function(){
        $("#day_gold_submit").on("click", function(){
            var bouns_amount = $(".day_bouns_amount").val();
            $.showLoad();
            $.post("/admin/site/saveGoldConfig/", {'bouns_amount': bouns_amount}, function(rst){
                if(rst.code == '200'){
                    window.location.reload();
                }
            }, 'json');
        });
        $(".day_bouns_amount").on("keyup change", function(){
            var bouns_amount = $(".day_bouns_amount").val();
            var user_gold_numbers = {{ $available_gold_number }};
            if(user_gold_numbers == 0){
                var unit_gold = 0;
            } else {
                var unit_gold = bouns_amount / user_gold_numbers;
            }
            $(".unit_gold").val(unit_gold);
        })
    })
</script>
@endsection

