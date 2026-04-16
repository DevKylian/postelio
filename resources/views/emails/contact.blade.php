<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #F8FAFC;">
        <tr>
            <td align="center" style="padding: 40px 16px;">

                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%; background-color: #FFFFFF; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); padding: 36px 40px 28px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto 16px;">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <img src="{{ asset('images/logo_png.PNG') }}" width="40" height="40" alt="Postelio" style="display: block; border-radius: 100px;" />
                                    </td>
                                    <td style="padding-left: 10px; font-size: 22px; font-weight: 700; color: #FFFFFF; letter-spacing: -0.5px;">
                                        Postelio
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 600; color: #FFFFFF; line-height: 1.3;">
                                Nouveau message de contact
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 36px 40px;">

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #F8FAFC; border-radius: 16px; border: 1px solid #E2E8F0; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding-bottom: 16px; border-bottom: 1px solid #E2E8F0;">
                                                    <div style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">De</div>
                                                    <div style="font-size: 15px; font-weight: 600; color: #1E293B;">{{ $senderName }}</div>
                                                    <div style="font-size: 14px; color: #64748B;">{{ $senderEmail }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 16px; padding-bottom: 16px; border-bottom: 1px solid #E2E8F0;">
                                                    <div style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Sujet</div>
                                                    <div style="font-size: 15px; font-weight: 600; color: #1E293B;">{{ $subject }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 16px;">
                                                    <div style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Message</div>
                                                    <div style="font-size: 15px; color: #475569; line-height: 1.7; white-space: pre-line;">{{ $messageBody }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <a href="mailto:{{ $senderEmail }}"
                                           style="display: inline-block; padding: 14px 36px; background-color: #1E293B; color: #FFFFFF; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 50px;">
                                            Répondre à {{ $senderName }} &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px;">
                            <div style="border-top: 1px solid #F1F5F9;"></div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 40px 28px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #CBD5E1;">
                                Reçu le {{ now()->translatedFormat('d F Y à H:i') }} via le formulaire de contact Postelio.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
