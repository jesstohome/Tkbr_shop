<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0;padding:0;background-color:#f5f7fa;">
@php
    $logo = get_setting('header_logo');
@endphp
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f5f7fa">
    <tr>
        <td align="center" style="padding:40px 12px;">
            <table width="600" border="0" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:8px;">
                @if(!empty($logo))
                <tr>
                    <td style="padding:36px 40px 8px;text-align:center;">
                        <img src="{{ uploaded_asset($logo) }}" alt="" height="32" style="height:32px;border:0;">
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding:24px 40px 0;text-align:center;font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:bold;color:#111827;">{{ $array['subject'] }}</td>
                </tr>
                <tr>
                    <td style="padding:16px 40px 0;text-align:center;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7;color:#4b5563;">{!! nl2br(e($array['content'])) !!}</td>
                </tr>
                <tr>
                    <td style="padding:28px 40px 36px;text-align:center;">
                        <div style="display:inline-block;background:#f0f2f5;border-radius:8px;padding:18px 40px;font-family:Arial,Helvetica,sans-serif;font-size:34px;font-weight:bold;letter-spacing:8px;color:#111827;">{{ $array['code'] }}</div>
                    </td>
                </tr>
                <tr>
                    <td style="background:#0d2b62;border-radius:0 0 8px 8px;padding:28px 40px 32px;text-align:center;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.7;color:#cbd5e1;">
                        @if(!empty($array['footer']))
                        {!! nl2br(e($array['footer'])) !!}
                        <br><br>
                        @endif
                        &copy;{{ date('Y') }} {{ env('APP_NAME') }}. {{ translate('All rights reserved') }}.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
