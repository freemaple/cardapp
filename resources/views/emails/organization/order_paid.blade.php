<table width="660" cellspacing="0" cellpadding="0" border="0" align="center" style="color:#666;font:12px Arial, Helvetica, sans-serif;line-height:150%;">
	<tbody>
		<tr>
			<td style="padding:5px;">
			您的课程：{{ $order_data['course_name'] }} 有新的订单 : <a href="{{ Helper::route('account_organization_course_order_view', $order_data['order']['order_number']) }}">{{ $order_data['order']['order_number'] }}</a> 
			</td>
		</tr>
	</tbody>
</table>