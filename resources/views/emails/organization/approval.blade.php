<table width="660" cellspacing="0" cellpadding="0" border="0" align="center" style="color:#666;font:12px Arial, Helvetica, sans-serif;line-height:150%;">
	<tbody>
		<tr>
			<td style="padding:5px;">
				<div style="font-size:14px;color:#000;padding:5px 0;font-weight:bold;">
					尊敬的 {{ $username }},
				</div>
				欢迎您入驻{{ $site_name }},
				@if($organization['approval_status'] == 'approved')
				您的机构{{ $organization['name'] }}信息已经审核通过
				@elseif($organization['approval_status'] == 'refused')
				对不起，您的机构信息审核失败，请检查您的机构信息后重新提交审核, 原因如下：{{ $organization['approval_remark'] or '' }}
				@endif
			</td>
		</tr>
		@include('emails.template.footer')
	</tbody>
</table>