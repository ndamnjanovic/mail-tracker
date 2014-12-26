<!DOCTYPE html>
<html lang="en">
  <head>
    @include('partials.head')
  </head>

  <body>
    @include('partials.header')

    <div class="tracker-content">
      @yield('content', 'This is default')
    </div>
    
    @include('partials.footer')
    @include('partials.foot')
    @yield('js')
  </body>
</html>