@extends('layout.master')

@section('title')
  <title>New user form</title>
@stop

@section('body')
<body background="img/login_bg.jpg">
<div>

  <div class="login-card">
    <h1>CloudIE</h1><br>
    {!! Form::open(['action' => 'SystemController@createID']) !!}
    {!! Form::text('firstname', null, array('placeholder'=>'Firstname' )); !!}
    {!! Form::text('lastname', null, array('placeholder'=>'Lastname' )); !!}
    {!! Form::text('username', null, array('placeholder'=>'Username (Min:8)' )); !!}
    {!! Form::text('email', null, array('placeholder'=>'Email (name@domain.com)' )); !!}
    {!! Form::password('password', array('placeholder'=>'Password (Min:8)' )); !!}
    {!! Form::password('confirm_password', array('placeholder'=>'Password (Confirm)' )); !!}
    {!! Form::submit('Create', array('class'=>'login login-submit')) !!}
    {!! Form::close() !!}

    <div class="login-help">
      {!! isset($display) ? $display : '' !!}
    </div>
  </div>
</div>

@stop




