
<!-- This is a common modal. We can use it to display a popup messages. -->
    <!--
params:
            - $header -> The title of the modal.
- $message-> The message of the modal.
-->
    <div class="modal fade" id="commonModal" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">  {{$header}}</h4>
            </div>
                 <div class="modal-body">
                    {!! $message !!}
                </div>
            </div>
        </div>
    </div>