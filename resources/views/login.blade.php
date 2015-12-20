@extends('layout.master')

@section('title')
  <title>CloudIE Login</title>
@stop

@section('body')
<body background="img/login_bg.jpg">
<div>

  <div class="login-card">
    <h1>CloudIE</h1><br>

    {!! isset($display) ? $display : '' !!}
    <br>
    {!! Form::open(['action' => 'SystemController@login']) !!}
    {!! Form::text('username', null, array('placeholder'=>'Username' )); !!}
    {!! Form::password('password', array('placeholder'=>'Password' )); !!}
    {!! Form::submit('Login', array('class'=>'login login-submit')) !!}
    {!! Form::close() !!}

    <div class="login-help">
      <a href="http://p0.mmwriting.com/newuser">Register</a>
    </div>
  </div>
</div>
@stop




