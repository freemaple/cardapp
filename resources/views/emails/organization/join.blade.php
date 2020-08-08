<table width="660" cellspacing="0" cellpadding="0" border="0" align="center" style="color:#666;font:12px Arial, Helvetica, sans-serif;line-height:150%;">
	<tbody>
		<tr>
			<td style="padding:5px;">
				<div style="font-size:14px;color:#000;padding:5px 0;font-weight:bold;">
					尊敬的 {{ $username }},
				</div>
				欢迎您入驻{{ $site_name }},您的机构{{ $organization['name'] }}信息已经提交成功,我们会尽快审核您的资料,审核通过后我们会已邮件的方式通知您。
			</td>
		</tr>
		@include('emails.template.footer')
	</tbody>
</table>