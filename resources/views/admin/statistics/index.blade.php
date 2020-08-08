@extends('admin.template.layout')
@section('content')
<div class="content">
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">用户统计</h3>
        </div>
        <div class="panel-body">
           <div id="user_container" style="min-width:300px;height:300px"></div>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">充值量</h3>
        </div>
        <div class="panel-body">
           <div id="recharge_container" style="min-width:300px;height:300px"></div>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">订单金额</h3>
        </div>
        <div class="panel-body">
           <div id="order_amount_container" style="min-width:300px;height:300px"></div>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">订单统计</h3>
        </div>
        <div class="panel-body">
           <div id="order_count_container" style="min-width:300px;height:300px"></div>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">金额统计</h3>
        </div>
        <div class="panel-body">
           <div id="amount_container" style="min-width:300px;height:300px"></div>
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
                    self.usercharts(rst.data);
                }
            }, 'json');

            $.get('/admin/statistics/getRechargeCount', {}, function(rst){
                if(rst.code == '200'){
                    self.rechargecharts(rst.data);
                }
            }, 'json');

            $.get('/admin/statistics/getOrderAmount', {}, function(rst){
                if(rst.code == '200'){
                    self.orderAmountCharts(rst.data);
                }
            }, 'json');

            $.get('/admin/statistics/getOrderCount', {}, function(rst){
                if(rst.code == '200'){
                    self.orderCountCharts(rst.data);
                }
            }, 'json');

            $.get('/admin/statistics/getAmount', {}, function(rst){
                if(rst.code == '200'){
                    self.amountCharts(rst.data);
                }
            }, 'json');
        };
        app.usercharts = function(data){
            var series_data = [data['user_count'], data['vip_count'], data['vip_pay_count'], data['vip_2_count'], data['vip_3_count'],  data['store_count'], data['store_pay_count'], data['store_valid_count'], data['store_0_count'], data['store_1_count'], data['store_2_count']];
            var chart = Highcharts.chart('user_container',{
                chart: {
                    type: 'column'
                },
                title: {
                    text: '人数统计'
                },
                xAxis: {
                    categories: [
                        '总用户数','vip用户数', 'vip缴费量', 'vip金卡', '铂金vip', '总网店数', '缴费网店数', '正在运行店铺数',  '临时掌柜', '网店掌柜', '金牌掌柜'
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '数量'
                    }
                },
                tooltip: {
                    // head + 每个 point + footer 拼接成完整的 table
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y} 人</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        borderWidth: 0
                    }
                },
                series: [{
                    name: '人数',
                    color: '#15f403',
                    data: series_data
                }]
            });
        }
        app.rechargecharts = function(data){
            var series_data = [data['vip_pay_count'], data['vip_renewal_count'], data['store_pay_count'], data['card_renewal_count'], data['integral_count']];
            var chart = Highcharts.chart('recharge_container',{
                chart: {
                    type: 'column'
                },
                title: {
                    text: '充值量统计'
                },
                xAxis: {
                    categories: [
                        'vip开通量','vip缴费量', '店铺缴费量', '名片缴费量', '积分缴费量'
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '数量'
                    }
                },
                tooltip: {
                    // head + 每个 point + footer 拼接成完整的 table
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y} 次</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        borderWidth: 0
                    }
                },
                series: [{
                    name: '充值量',
                    color: '#3f51b5',
                    data: series_data
                }]
            });
        };
        app.orderAmountCharts = function(data){
            var series_data = [parseFloat(data['order_total']), parseFloat(data['self_order_total']), parseFloat(data['store_order_total']), parseFloat(data['order_actual_total'])];
            var chart = Highcharts.chart('order_amount_container',{
                chart: {
                    type: 'column'
                },
                title: {
                    text: '订单销售统计'
                },
                xAxis: {
                    categories: [
                        '订单销售总额', '自营订单销售总额','个人网店订单销售总额', '营业额扣点数总额'
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '数量'
                    }
                },
                tooltip: {
                    // head + 每个 point + footer 拼接成完整的 table
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>￥{point.y:.2f} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        borderWidth: 0
                    }
                },
                series: [{
                    name: '订单销售额',
                    color: '#ffc107',
                    data: series_data
                }]
            });
        }
        app.orderCountCharts = function(data){
            var series_data = [parseFloat(data['order_pay_count']), parseFloat(data['self_order_count']), parseFloat(data['shipping_order_count']), parseFloat(data['store_order_count']), parseFloat(data['store_shipping_order_count'])];
            var chart = Highcharts.chart('order_count_container',{
                chart: {
                    type: 'column'
                },
                title: {
                    text: '订单销售统计'
                },
                xAxis: {
                    categories: [
                        '总订单数', '自营订单数','自营待发货订单数', '个人网店订单数', '个人网店待发货订单数'
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '数量'
                    }
                },
                tooltip: {
                    // head + 每个 point + footer 拼接成完整的 table
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        borderWidth: 0
                    }
                },
                series: [{
                    name: '订单销售数',
                    data: series_data
                }]
            });
        }
        app.amountCharts = function(data){
            var series_data = [parseFloat(data['integral_amount']), parseFloat(data['reward_amount']), parseFloat(data['freeze_amount'])];
            var chart = Highcharts.chart('amount_container',{
                chart: {
                    type: 'column'
                },
                title: {
                    text: '金额统计'
                },
                xAxis: {
                    categories: [
                        '总剩余积分', '总剩余赏金','总交易中赏金'
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '数量'
                    }
                },
                tooltip: {
                    // head + 每个 point + footer 拼接成完整的 table
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>￥{point.y} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        borderWidth: 0
                    }
                },
                series: [{
                    name: '金额统计',
                    data: series_data
                }]
            });
        }
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection