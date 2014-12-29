<!DOCTYPE html>
<html lang="en">
  <head>
    @include('partials.head')
  </head>

  <body>
    @if(Auth::user())
      @include('partials.header')
    @endif

    <div class="tracker-content">
      @yield('content', 'This is default')
    </div>
    
    @include('partials.footer')
    @include('partials.foot')
    @yield('js')
  </body>
</html>