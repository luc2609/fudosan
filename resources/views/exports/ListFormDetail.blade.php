<!DOCTYPE html>
<html>

<head>
    {{-- <title>{{$data['申請フォーム名変更']}}</title> --}}
</head>

<body>
    <table>
        <tr>
            @foreach ($formFields as $key => $item)
                <td style="background: #deeaf6; width:200px;border:1px solid black; text-align:center;font-weight:700">
                    {{ $item }}</td>
            @endforeach
        </tr>
        @foreach ($jsonDatas as $item)
        <tr>
            @foreach($item as $key)
            <td style="background: #deeaf6; width:200px;border:1px solid black; text-align:center;font-weight:700">
            {{ $key }}</td>
            @endforeach
        </tr>
            @endforeach
    </table>
</body>

</html>
