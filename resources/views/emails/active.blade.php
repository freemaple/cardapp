<table width="660" cellspacing="0" cellpadding="0" border="0" align="center" style="color:#666;font:12px Arial, Helvetica, sans-serif;line-height:150%;">
 <tbody>
  <tr>
   <td style="padding:5px;">
		<div style="font-size:14px;color:#000;padding:5px 0;font-weight:bold;">
			尊敬的 {{ $username }},
		</div>
		欢迎您使用{{ $site_name }}, 请您<a href="{{ $active_link }}">点击此处 </a> 激活您的账户。如果您无法点击此链接,请手动将以下链接复制到您的浏览器。
		<a href="{{ $active_link }}">{{ $active_link }}</a>
   </td>
  </tr>
  @include('emails.template.footer')
 </tbody>
</table>
