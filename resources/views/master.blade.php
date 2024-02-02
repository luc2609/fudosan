<!DOCTYPE html>
<html lang="en">

<header>
    <title>Master Blade- @yield('title')</title>
    @yield('css')
</header>

<body>
    @include('shared.header')

    @yield('content')

    @include('shared.footer')

    @yield('script')

</body>

</html>