@extends('master')

@section('title', 'パスワード変更のご確認')

@section('content')
<br> {{$data['username']}} 様<br>
本メールは、パスワード再発行手続きを希望された方にお送りしています。<br>
パスワードを変更するには下記の確認コードを入力し、パスワードの再設定を行ってください。<br>
──────────────────<br>
確認コード（6桁）：{{$data['token']}}<br>
認証コードを3回以上間違えた場合、コードが無効になります。<br>
──────────────────<br>
※このメールはシステムにより自動送信されています。<br>
このメールに対して返信しないようお願い致します。<br>
@endsection