@extends('admin.template.layout')
@section('content')
<div class="content">
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">总用户量：<span class="user_count"></span></h3>
        </div>
        <div class="panel-body">
           <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th>普通用户数</th>
                        <th>vip用户数</th>
                        <th>手动激活vip用户数</th>
                        <th>含vip用户数</th>
                        <th>含vip金卡用户数</th>
                        <th>含铂金vip用户数</th>
                        <th>vip缴费总量</th>
                        <th>开通缴费总量</th>
                        <th>续费缴费总量</th>
                    </tr>
                </thead>
               <tbody>
                    <tr class="usercount_data">
                      
                    </tr>
               </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">正式网店数量：<span class="store_count"></span></h3>
        </div>
        <div class="panel-body"><table class="table table-condensed table-striped">
            <thead>
                    <tr>
                        <th>手动激活</th>
                        <th>缴费开通网店</th>
                        <th>已启用</th>
                        <th>已禁用</th>
                        <th>运营中</th>
                        <th>临时掌柜总量</th>
                        <th>网店掌柜总量</th>
                        <th>金牌掌柜总量</th>
                        <th>到期待续费总量</th>
                        <th>续费总量</th>
                    </tr>
                </thead>
               <tbody>
                    <tr class="storecount_data">
                       
                    </tr>
               </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">金额统计</h3>
        </div>
        <div class="panel-body"><table class="table table-condensed table-striped">
            <thead>
                    <tr>
                        <th>用户手上剩余总余额</th>
                        <th>用户手上剩余总积分</th>
                        <th>申请提现总金额</th>
                        <th>已完成提现总金额</th>
                        <th>含已提现拨款出去总金额</th>
                        <th>申请提现总笔数</th>
                        <th>活动送出积分共多少</th>
                        <th>用户充值积分共多少</th>
                    </tr>
                </thead>
               <tbody>
                    <tr class="amount_data">
                       
                    </tr>
               </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/admin/scripts/plugin/highcharts.js') }}"></script>
<script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
            var self = this;
            $.get('/admin/statistics/getUserCount', {}, function(rst){
                if(rst.code == '200'){
                    var info = self.trinfo(rst.data.statistics);
                    $(".usercount_data").html(info);
                    $(".user_count").text(rst.data.user_count);
                }
            }, 'json');

            $.get('/admin/statistics/getStoreCount', {}, function(rst){
                if(rst.code == '200'){
                    var info = self.trinfo(rst.data.statistics);
                    $(".storecount_data").html(info);
                    $(".store_count").text(rst.data.store_count);
                }
            }, 'json');

            $.get('/admin/statistics/getAmount', {}, function(rst){
                if(rst.code == '200'){
                    var info = self.trinfo(rst.data.statistics);
                    $(".amount_data").html(info);
                }
            }, 'json');
        };
        app.trinfo = function(data){
            var tr = '';
            for(var i in data){
                tr += '<td class="text-red">' + data[i] +'</td>';
            }
            return tr;
        }
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection