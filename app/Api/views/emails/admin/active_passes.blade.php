<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <div>Hello, </div>

    @if($reserve->userPasses->count() == 0 )
        <br>{{ $reserve->ReserveName }} Reserve has no valid passes for today.
    @endif

    @if($reserve->userPasses->count() > 0)
        <br>{{ $reserve->ReserveName }} Reserve has {{ $reserve->userPasses->count() }} valid passes for today. Please find the details below.
        <table style="width:100%; border:1px solid #ccc;">
            <tr style="background-color:#ccc; font-weight: bold; text-align:left;">
                <th style="padding:5px 2px;">Purchased by</th>
                <th>Pass</th>
                <th>Validity</th>
                <th>Owner</th>
                <th>Purchased on</th>
            </tr>
            @foreach($reserve->userPasses as $pass)
            <tr>
                <td style="padding:2px;">{{ $pass->user->full_name }}</td>
                <td>{{ $pass->pass->name }}</td>
                <td>{{ $pass->start_date }}
                    @if($pass->start_date != $pass->end_date)
                        - {{ $pass->end_date }}
                    @endif
                </td>
                @if($pass->OwnPass == 1)
                    <td>Self</td>
                @else
                    <td>Others</td>
                @endif
                <td>{{ $pass->created_at }}</td>
            </tr>
            @endforeach
        </table>
    @endif

    <div>
        <br>If you have any questions or concerns, please email us at <a href="mailto:{{ env('MAIL_FROM_ADDRESS') }}">{{ env('MAIL_FROM_ADDRESS') }}</a>

        <br><br>Thanks,

        <br>Engena Team
    </div>

</body>
</html>
