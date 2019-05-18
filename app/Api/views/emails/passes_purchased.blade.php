<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <div>Hello {{ $user->full_name }}, </div>

    <div>
        <br>Thank you for your purchase. Please find the details for the same below.<br>
        <table style="width:90%;border:1px solid #ccc;">
            <tr style="background-color:#ccc; font-weight: bold; text-align:left;">
                <th style="padding:5px;">Reserve</th>
                <th>Pass</th>
                <th>Validity</th>
                @if($passes->count() > 1)
                    <th>Per pass</th>
                    <th>No. of passes</th>
                    <th>Total Cost</th>
                @else
                    <th>Cost</th>
                @endif
            </tr>

            <tr>
                <td style="padding:5px;">{{ $pass->pass->reserve->ReserveName }}</td>
                <td>{{ $pass->pass->name }}</td>
                <td>{{ $pass->start_date }}
                    @if($pass->start_date != $pass->end_date)
                        - {{ $pass->end_date }}
                    @endif
                </td>
                <td>{{ $pass->pass_amount }} {{ env('PEACH_CURRENCY') }}</td>
                @if($passes->count() > 1)
                    <td>{{ $passes->count() }}</td>
                    <td>{{ $passes->sum('pass_amount') }} {{ env('PEACH_CURRENCY') }}</td>
                @endif
            </tr>
        </table>
        <br>If you have any questions or concerns, please email us at <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}">{{ env('MAIL_FROM_ADDRESS') }}</a>

        <br><br>Thanks,

        <br>Engena Team
    </div>
</body>
</html>
