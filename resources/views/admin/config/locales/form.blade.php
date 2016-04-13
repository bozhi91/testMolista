{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'files'=>true, 'id'=>'edit-form' ]) !!}
    {!! Form::hidden('admin', 1) !!}

    <div class="row">
        <div class="col-xs-6 col-sm-3">
            <div class="form-group error-container">
                {!! Form::label('locale', Lang::get('admin/config/locales.locale')) !!}
                @if ( empty($item->locale) )
                    {!! Form::text('locale',null, [ 'class'=>'form-control required', 'minlength'=>'2', 'maxlength'=>'2' ]) !!}
                @else
                    {!! Form::text(null, $item->locale, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
                @endif
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div class="form-group">
                <div class="error-container">
                    @if ( empty($item->flag) )
                        {!! Form::label('flag', Lang::get('admin/config/locales.flag')) !!}
                        {!! Form::file('flag', [ 'class'=>'form-control required', 'accept'=>'image/*' ]) !!}
                    @else
                        <img src="{{ asset("flags/{$item->flag}") }}" class="" />
                        {!! Form::label('flag', Lang::get('admin/config/locales.flag')) !!}
                        {!! Form::file('flag', [ 'class'=>'form-control', 'accept'=>'image/*' ]) !!}
                    @endif
                </div>
                <div class="help-block">{{ Lang::get('admin/config/locales.flag.help') }}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group error-container">
                {!! Form::label('native', Lang::get('admin/config/locales.name')) !!}
                {!! Form::text('native', null, [ 'class'=>'form-control required' ]) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group error-container">
                {!! Form::label('name', Lang::get('admin/config/locales.name').' (english)') !!}
                {!! Form::text('name', null, [ 'class'=>'form-control required' ]) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-3">
            <div class="form-group error-container">
                {!! Form::label('dir', Lang::get('admin/config/locales.dir')) !!}
                {!! Form::select('dir', [ 'ltr'=>Lang::get('admin/config/locales.dir.ltr'), 'rtl'=>Lang::get('admin/config/locales.dir.rtl') ], null, [ 'class'=>'form-control required' ]) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-3">
            <div class="form-group error-container">
                {!! Form::label('regional', Lang::get('admin/config/locales.regional')) !!}
                {!! Form::text('regional', null, [ 'class'=>'form-control required' ]) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-3">
            <div class="form-group error-container">
                {!! Form::label('script', Lang::get('admin/config/locales.script')) !!}
                {!! Form::select('script', [ 'Latn'=>@$scripts['Latn'] ]+$scripts, null, [ 'class'=>'form-control required' ]) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-3">
            <div class="form-group error-container">
                {!! Form::label('web', Lang::get('admin/config/locales.web')) !!}
                {!! Form::select('web', [ 1=>Lang::get('general.yes'), 0=>Lang::get('general.no') ], null, [ 'class'=>'form-control required' ]) !!}
            </div>
        </div>
    </div>

    <div class="text-right">
        {!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
        {!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
    </div>

{!! Form::close() !!}

<script type="text/javascript">
    ready_callbacks.push(function(){
        var form = $('#edit-form');

        form.validate({
            ignore: '',
            errorPlacement: function(error, element) {
                element.closest('.error-container').append(error);
            },
            rules: {
                locale: {
                    remote: {
                        url: '{{ action('Admin\Utils\LocaleController@getCheck', 'locale') }}',
                        type: 'get',
                        data: {
                            exclude: {{ empty($item->id) ? '0' : $item->id }}
                        }
                    }
                },
                flag: {
                    accept: 'image/*'
                }
            },
            messages: {
                locale: {
                    remote: "{{ trim( Lang::get('admin/config/locales.locale.used') ) }}"
                },
                flag: {
                    accept: "{{ trim( Lang::get('admin/config/locales.flag.error') ) }}"
                }
            },
            submitHandler: function(f) {
                LOADING.show();
                f.submit();
            }
        });
    });
</script>