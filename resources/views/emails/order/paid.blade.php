<table width="660" cellspacing="0" cellpadding="0" border="0" align="center" style="color:#666;font:12px Arial, Helvetica, sans-serif;line-height:150%;">
	<tbody>
		<tr>
			<td style="padding:5px;">
				@if($order['is_self'] == '1')
					有自营新订单：{{ $order['order_no'] }}了，请及时发货！
				@else
					有个体网店 @if(!empty($order['order_store'] && isset($order['order_store']['name'])))<span style="color: #f00">{{ $order['order_store']['name'] }}</span>@endif 新订单：{{ $order['order_no'] }}了，请及时跟踪卖家发货！
				@endif
				<br>
				<div style="padding: 20px 0px">
					<div style="padding-bottom: 10px;font-weight: bold;">收获地址</div>
					<div style="line-height: 20px;width: 100%;border: 1px solid #eee;padding: 10px 20px;font-size: 13px;">
						 <p class="weight">
	                		<span>{{ $order['user_info']['fullname'] }}</span>
	                	</p>
		                <p>
		                	<span>{{ $order['user_info']['province'] }} </span>
		                	<span>,{{ $order['user_info']['city'] }} </span>
		                	<span>,{{ $order['user_info']['district'] }}</span>
		                	@if($order['user_info']['town'] != '')
				                <span>,{{ $order['user_info']['town'] }}</span>
				            @endif
			                @if($order['user_info']['village'] != '')
				                <span>,{{ $order['user_info']['village'] }}</span>
				            @endif
		                </p>
		                <p>{{ $order['user_info']['address'] }}</p>
		                <p><span>联系电话：{{ $order['user_info']['phone'] }}</span></p>
		                <p>邮编：{{ $order['user_info']['zip'] }}</p>
					</div>
				</div>
				<div style="padding-bottom: 10px;font-weight: bold;">产品</div>
				@if(!empty($order['order_products']))
				<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="margin:20px 0px;border: 1px solid #eee;color: #333; font: 13px Helvetica, Arial, sans-serif;">
					<tbody>
						@foreach($order['order_products'] as $opkey => $product)
						<tr style="border-bottom: 1px solid #ccc">
							<td style="vertical-align: middle; padding: 20px;">
								<div style="width: 80px;float: left;margin-right: 30px">
									<img style="width: 80px" src="{{ $product['image'] or '' }}">
								</div>
								<div style="float: left;width: 400px">
									<p>
										<span>{{ $product['product_name'] }}</span>
									</p>
									<p>
										@if(!empty($product['spec']))
			                            <div class="spec">
			                               {{ $product['spec'] }}
			                            </div>
			                            @endif
		               				</p>
									<div class="price-info">
										<span class="price-text"> ￥{{ $product['price'] }}</span> × {{ $product['quantity'] }}
		                        	</div>
								</div>
								<div style="clear: both">
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@endif
				<div class="order-view-panel">
					<div class="order-panel-header clearfix">
						订单金额
					</div>
					<div class="order-panel-content">
						<div class="order-product-list">
							<p>总金额: <span class="text-red">￥{{ $order['order_total'] }}</span></p>
							<p>产品金额: <span class="text-red">￥{{ $order['order_subtotal'] }}</span></p>
							<p>
								运费: 
								@if($order['order_shipping'] == 0)
								<span class="text-red">卖家包邮</span>
								@else
								<span class="text-red">￥{{ $order['order_shipping'] }}</span>
								@endif
							</p>
							@if($order['payment_amount'] > 0)
							<p>现金支付: <span class="text-red">￥{{ $order['payment_amount'] }}</span></p>
							@endif
							@if($order['order_integral'] > 0)
							<p>积分支付: <span class="text-red">￥{{ $order['order_integral'] }}</span></p>
							@endif
						</div>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

