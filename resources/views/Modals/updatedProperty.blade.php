
    <?php
        $sites = DB::select("select * from  marketplaces where free = true");
    ?>

    <div class="modal fade" id="propertyModal" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> ¿Quieres publicar tu propiedad en estos portales gratuitos? </h4>
            </div>
                <div class="modal-body">

                    {!! Form::open(['route' => 'publishProperty']) !!}

                    @foreach($sites as $site)
                        <div style="margin-left:20px;">

                            <input type="checkbox" name="marketplace[]" value=<?php echo $site->id;?> />

                            <?php $icon_url = "http://".$site->code.".molista.com/marketplaces/".$site->logo;?>
                            <img src={{$icon_url}}>

                            {!! Form::label($site->code ,$site->code) !!}
                        </div>

                       <br/>
                    @endforeach

                    {!! Form::submit('Publicar', ['class' => 'btn btn-info']) !!}
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>