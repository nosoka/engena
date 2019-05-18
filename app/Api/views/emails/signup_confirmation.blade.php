<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Engena - Sign Up Confirmation</title>
</head>
<body>
    <p>Hello {{ $user->full_name }} , </p>

    <p>Thank you for signing up! We need you to <a href='{{ env('ENGENA_APP_BASEURL') }}confirm_activation?code={{ $user->confirmation_code }}'>confirm your email address by clicking this link</a> real quick!

    <br><br>If that doesnt work, you can manually enter this address in your browser - {{ env('ENGENA_APP_BASEURL') }}confirm_activation?code={{ $user->confirmation_code }}

    <br><br>If you have any questions or concerns, please email us at <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}">{{ env("MAIL_FROM_ADDRESS") }}</a>

    <br><br>Thanks,

    <br>Engena Team

    </p>

</body>
</html>
