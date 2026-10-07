<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Ogohlantirish</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:Arial,sans-serif;color:#0A2540;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa;padding:30px 15px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 4px 20px rgba(10,37,64,.08);">

                {{-- HEADER --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#0A2540,#1a3a5c);padding:24px 30px;color:#fff;">
                        <table width="100%">
                            <tr>
                                <td>
                                    <div style="font-size:20px;font-weight:800;">
                                        🛡️ SOG'LOM <span style="color:#4ecdc4;">AVLOD</span>
                                    </div>
                                    <div style="font-size:12px;color:rgba(255,255,255,.7);margin-top:4px;">
                                        Yoshlarni himoya qilish platformasi
                                    </div>
                                </td>
                                <td align="right" valign="top">
                                    <div style="background:#E63946;color:#fff;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;">
                                        🚨 OGOHLANTIRISH
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Sarlavha --}}
                <tr>
                    <td style="padding:30px 30px 20px;">
                        <h1 style="margin:0 0 10px;font-size:22px;font-weight:800;color:#0A2540;">
                            Hurmatli {{ $parent->name }},
                        </h1>
                        <p style="margin:0;color:#6c757d;font-size:15px;line-height:1.6;">
                            Sizga <strong>{{ $schoolName }}</strong> dan xabar yuborildi.
                            Farzandingiz <strong style="color:#E63946;">{{ $consecutiveDays }} kun</strong>
                            ketma-ket darsga kelmadi.
                        </p>
                    </td>
                </tr>

                {{-- O'quvchi --}}
                <tr>
                    <td style="padding:0 30px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background:#f5f7fa;border-radius:12px;border-left:4px solid #E63946;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <div style="font-size:18px;font-weight:800;color:#0A2540;margin-bottom:6px;">
                                        {{ $student->full_name }}
                                    </div>
                                    <div style="font-size:13px;color:#6c757d;">
                                        <span style="background:#e6ecf3;color:#0A2540;padding:3px 10px;border-radius:20px;font-weight:700;font-size:12px;">
                                            {{ $student->class_name }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Statistika --}}
                <tr>
                    <td style="padding:0 30px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="48%" align="center" style="padding:18px 8px;background:#fde8ea;border-radius:10px;">
                                    <div style="font-size:32px;font-weight:800;color:#E63946;">
                                        {{ $consecutiveDays }}
                                    </div>
                                    <div style="font-size:12px;color:#6c757d;font-weight:600;margin-top:4px;">
                                        KETMA-KET KUN
                                    </div>
                                </td>
                                <td width="4%"></td>
                                <td width="48%" align="center" style="padding:18px 8px;background:{{ $consecutiveDays >= 5 ? '#fde8ea' : '#fef3e7' }};border-radius:10px;">
                                    <div style="font-size:32px;font-weight:800;color:{{ $consecutiveDays >= 5 ? '#E63946' : '#F4A261' }};">
                                        {{ $consecutiveDays >= 5 ? '🔴' : '🟡' }}
                                    </div>
                                    <div style="font-size:12px;color:#6c757d;font-weight:600;margin-top:4px;">
                                        {{ $consecutiveDays >= 5 ? 'YUQORI XAVF' : "O'RTA XAVF" }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Sabab --}}
                @if($attendance && $attendance->reason)
                <tr>
                    <td style="padding:0 30px 20px;">
                        <div style="background:#f5f7fa;padding:14px 18px;border-radius:10px;border-left:4px solid #F4A261;">
                            <div style="font-size:12px;color:#899bbd;font-weight:700;text-transform:uppercase;margin-bottom:4px;">
                                OXIRGI SABAB
                            </div>
                            <div style="font-size:14px;color:#0A2540;font-weight:600;">
                                {{ $attendance->reason }}
                            </div>
                        </div>
                    </td>
                </tr>
                @endif

                {{-- Tavsiya --}}
                <tr>
                    <td style="padding:0 30px 24px;">
                        <div style="background:#e3f4f1;padding:16px 20px;border-radius:10px;">
                            <div style="font-size:13px;font-weight:800;color:#21867a;margin-bottom:8px;">
                                💡 Nima qilish kerak?
                            </div>
                            <ul style="margin:0;padding-left:20px;color:#495057;font-size:14px;line-height:1.7;">
                                <li>Farzandingiz bilan suhbatlashing</li>
                                <li>Sinf rahbari bilan bog'laning</li>
                                <li>Maktab psixologi bilan uchrashing</li>
                                <li>Sababini maktabga xabar qiling</li>
                            </ul>
                        </div>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#f5f7fa;padding:20px 30px;border-top:1px solid #eef1f5;">
                        <p style="margin:0 0 8px;font-size:13px;color:#6c757d;text-align:center;">
                            Bu xabar avtomatik yuborildi.
                        </p>
                        <p style="margin:0;font-size:12px;color:#899bbd;text-align:center;">
                            © {{ date('Y') }} SOG'LOM AVLOD ·
                            Bugun e'tibor — ertaga sog'lom avlod
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>