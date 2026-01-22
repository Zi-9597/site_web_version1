<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-- Import Google Fonts (Nunito + Open Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

</head>

<body style="
    margin:0;
    padding:0;
    background-color:#f4f6f8;
    font-family:'Nunito','Open Sans',Arial,sans-serif;
">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" style="
                background-color:#ffffff;
                border-radius:8px;
                padding:30px;
            ">

                <!-- Titre -->
                <tr>
                    <td style="
                        font-size:22px;
                        font-weight:700;
                        color:#1f2933;
                        padding-bottom:15px;
                        font-family:'Nunito','Open Sans',Arial,sans-serif;
                    ">
                        Votre inscription est validée
                    </td>
                </tr>

                <!-- Message -->
                <tr>
                    <td style="
                        font-size:15px;
                        color:#374151;
                        line-height:1.6;
                        padding-bottom:15px;
                        font-family:'Nunito','Open Sans',Arial,sans-serif;
                    ">
                        Bonjour <b>{{NAME}} {{LAST_NAME}}</b>,<br><br>

                        Merci d’avoir rejoint l’
                        <b>Association des Étudiants & Anciens (EEA) Lille</b>.
                        Nous sommes ravis de vous compter parmi nous.
                    </td>
                </tr>

                <!-- Lien de confirmation -->
                <tr>
                    <td style="
                        font-size:15px;
                        color:#374151;
                        line-height:1.6;
                        padding-bottom:15px;
                        font-family:'Nunito','Open Sans',Arial,sans-serif;
                    ">
                        Afin de finaliser votre inscription, veuillez confirmer votre adresse e-mail
                        en cliquant sur le lien ci-dessous :
                        <br><br>

                        <a href="https://association-eea.univ-lille.fr/?dest=confirmation_inscription&token={{TOKEN}}"
                           style="
                               display:inline-block;
                               padding:10px 16px;
                               background-color:#2563eb;
                               color:#ffffff;
                               text-decoration:none;
                               border-radius:5px;
                               font-weight:600;
                               text-align : center;
                               font-family:'Nunito','Open Sans',Arial,sans-serif;
                           ">
                            Confirmer mon inscription
                        </a>

                        <br><br>
                        <span style="font-size:13px;color:#6b7280;">
                            (Lien de test – environnement local)
                        </span>
                    </td>
                </tr>

                <!-- Avantages -->
                <tr>
                    <td style="
                        padding:10px 0;
                        font-family:'Nunito','Open Sans',Arial,sans-serif;
                    ">
                        <p style="margin:0 0 10px 0;">
                            En tant que membre, vous bénéficiez notamment de :
                        </p>

                        <ul style="padding-left:20px; margin:0; line-height:1.6;">
                            <li><b>Un réseau actif</b> d’étudiants et diplômés</li>
                            <li><b>Des événements exclusifs</b> : interventions d’entreprises, parrainages, visites d’entreprises</li>
                            <li><b>Un accompagnement carrière</b> : stages, alternances, premier emploi</li>
                            <li><b>Des séances de révisions collectives</b> pour préparer vos examens</li>
                            <li><b>Des activités associatives</b> : sorties cinéma, football</li>
                        </ul>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="
                        font-size:14px;
                        color:#4b5563;
                        padding-top:20px;
                        border-top:1px solid #e5e7eb;
                        font-family:'Nunito','Open Sans',Arial,sans-serif;
                    ">
                        À très bientôt,<br>
                        <b>Bureau de l’Association des Étudiants & Anciens EEA</b>
                        <br><br>
                        <a href="https://association-eea.univ-lille.fr"
                           style="color:#2563eb;text-decoration:none;">
                            Accéder au site de l’association
                        </a>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
