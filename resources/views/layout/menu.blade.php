<!doctype html>
<html lang="en">
  <head>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @section('title')
    @show
  {!! Html::style('css/styles.css'); !!}
  </head>
  <body>
      @section('body')
      @show
      <div id="header">
          @yield('header')
      </div>
      <div id="nav">
	  @yield('navigator')
      </div>
      <div id="section">
          @yield('section')
      </div>
      <div id="footer">
          @yield('footer')
      </div>
  </body>
  {!! Html::script('js/script.js'); !!}
</html>

