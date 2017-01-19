<div class="home-search-area hidden-lg hidden-md">
    <div class="container">
        <div class="form-area closed" style="opacity: 0;">
            @include('web.search.form')
            <a href="#" class="form-area-minimizer text-center"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></a>
        </div>
    </div>
</div>

<script type="text/javascript">
    ready_callbacks.push(function(){
        var cont = $('.home-search-area');

        cont.find('.form-area').addClass('closed').css({ opacity: 1 });

        cont.on('focus', '.first-input-line input', function(e){
            cont.find('.form-area').removeClass('closed').find('select.has-select-2').select2();
        });

        cont.on('click', '.form-area-minimizer', function(e){
            e.preventDefault();
            cont.find('.form-area').addClass('closed');
        });
    });
</script>
