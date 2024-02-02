<!DOCTYPE html>
<html>

<head>
    {{-- <title>{{$data['申請フォーム名変更']}}</title> --}}
</head>

<body>
    <table>
        @foreach ($data as $key => $item)
            <tr>
                <td style="background: #deeaf6; width:200px;border:1px solid black; text-align:center;font-weight:700">
                    {{ $key }}</td>
                @if (getType($item) == 'array')
                    <td style="border:1px solid black; text-align:center;font-weight:700">
                        @foreach ($item as $abc)
                        <table>
                            @foreach ($abc as $key => $a)
                                <tr>
                                    <td>{{ $key }} : {{ $a }} </td> 
                                </tr>
                            @endforeach
                        </table>
                            @endforeach
                    </td>
                @else
                    <td style="border:1px solid black; text-align:center;font-weight:700">
                        {{ $item }}
                    </td>
                @endif
            </tr>
        @endforeach
    </table>
</body>

</html>
