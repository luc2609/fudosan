@extends('master')

@section('title', 'Email Notifications')

@section('content')
<br /> Dear {{$data['name']}},
<br /> Thank you for register with us. To start using service, you will need to enter the following verification code to complete the registration process.
<br /> Verification code: {{$data['code']}}</br>
<br /> For security reasons, please do not disclose this code to others</br>
<br /> Your information detail is as below
<br /> Name: {{$data['name']}}
<br /> Address: {{$data['address']}}
<br /> Phone: {{$data['phone']}}
@endsection