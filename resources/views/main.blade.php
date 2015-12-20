@extends('layout.menu')

@section('title')
  <title>Main Menu</title>
@stop

@section('body')
  {!! Form::open(array('action'=>'SystemController@mainloop','method'=>'POST', 'files'=>true)) !!}
@stop

@section('header')
   CloudIE 
@stop

@section('navigator')
  <input type="submit" name="deleteFiles" value="Delete Files">
  <input type="submit" name="emailFile" value="Email Files">
  <input type="submit" name="viewLog" value="View Log">
  <input type="submit" name="logout" value="Logout">
  <br><br><br><br>
  <b><u>User:</u></b>
  {!! isset(Auth::user()->username) ? Auth::user()->username : '' !!}
@stop

@section('section')

  {!! isset($display) ? $display : '' !!}
  <br>
  {!! Form::file('uploadfiles[]', array('multiple'=>true)) !!}
  <br>
  {!! Form::submit("Upload") !!}
  {!! Form::close() !!}
@stop

@section('footer')
    Updated Dec 2015. @MMWRITING.COM
@stop



