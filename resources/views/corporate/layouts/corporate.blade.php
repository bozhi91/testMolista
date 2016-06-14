<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if ( !empty($seo_title) )
        <title>{{ $seo_title }}</title>
    @else
        <title>Molista</title>
    @endif

    @if ( !empty($seo_description) )
        <meta name="description" content="{{ $seo_description }}" />
    @endif

    <link href="https://fonts.googleapis.com/css?family=Lato:400,300,700,900,300italic,400italic,700italic|Dosis:400,700,600,500" rel="stylesheet" type="text/css" />
    <link href="{{ Theme::url('/compiled/css/corporate.css') }}" rel="stylesheet" type='text/css' />

    <link id="page_favicon" href="{{ asset('favicon.ico') }}" rel="icon" type="image/x-icon" />

    <script type="text/javascript">
        var ready_callbacks = [];
    </script>

</head>

<body>

    <header id="header">
        <nav class="navbar navbar-default">
          <div class="container">
         
            <div class="navbar-header">

              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>

              <a class="navbar-brand" href="/">
                <img alt="Logo" src="{{ Theme::url('/images/corporate/logo.png') }}">
              </a>

            </div>

            <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">

              <ul class="nav navbar-nav">
                <li><a href="" class="btn btnBdrYlw text-uppercase">VER DEMO</a></li>
                <li><button class="btn btnBdrYlw text-uppercase" data-toggle="modal" data-target="#contact-modal">más información</button></li> 
                <!--<li><a class="navbar-link" href="">Planes y precios</a></li>
                <li><a href="navbar-link">Soporte</a></li>  -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Languages <span class="caret"></span></a>
                    <ul class="language_bar_chooser dropdown-menu">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <li>
                                <a rel="alternate" hreflang="{{$localeCode}}" href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">
                                    {{{ $properties['native'] }}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
              </ul>

            </div>
          </div>
        </nav>
    </header>

    @yield('content')

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <ul class="footer-menu">
                        <li><a href="">Soporte</a></li>
                        <li><a href="">Contactar</a></li>
                        <li><a href="{{ action('AdminController@index') }}">{{ Lang::get('corporate/home.footer.admin.access') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <!-- / FOOTER -->
    <!-- contact modal-->
    <div id="contact-modal" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Modal title</h4>
          </div>
          <div class="modal-body">
            <p>One fine body&hellip;</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- /contact modal -->
    <script src="{{ Theme::url('/compiled/js/corporate.js') }}"></script>
    <script src="{{ Theme::url('/js/jquery.validate/messages_' . LaravelLocalization::getCurrentLocale() . '.min.js') }}"></script>
    <script src="{{ Theme::url('/js/alertify/messages_' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>

</body>
</html>
