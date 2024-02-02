<!DOCTYPE html>
<html>

<head>
    <title>自分自身が作成した申請フォーム一覧</title>
</head>

<body>
    <table>
        <tr>
            <td style="border:1px solid black; width:200px;text-align:center;font-weight:700">
                ステータス</td>
            <td style="border:1px solid black; width:200px;text-align:center;font-weight:700">
                申請日</td>

        </tr>
        @foreach ($data[0] as $key =>$item)
            <tr>
                    <td style="border:1px solid black; width:300px;text-align:center;font-weight:700">
                        {{$item}}</td>
                        
            </tr>
        @endforeach
        @foreach ($data[1] as $key =>$item)
        <tr>
                <td style="border:1px solid black; width:300px;text-align:center;font-weight:700">
                    {{$item}}</td>
                    
        </tr>
    @endforeach
    </table>
</body>

</html>
