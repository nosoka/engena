<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Engena - Password Reset Request</title>
</head>
<body>

    <p>Hello {{ $user->full_name }} , </p>

    <p>
        A password reset request was received for your account. Please ignore it if you did not initiate this request.

        <br><br>Please open the following URL to begin the password reset process:
        <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>

        <br><br>If you have any questions or concerns, please email us at <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}">{{ env("MAIL_FROM_ADDRESS") }}</a>

        <br><br>Thanks,

        <br>Engena Team
    </p>
</body>
</html>
