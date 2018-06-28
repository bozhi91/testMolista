<!-- Mostrar el plan actual y un boton para actualizar el plan. -->
<?php $plan= App\Http\Controllers\Account\ReportsController::getPlan();?>
<?php $protocol =isset($_SERVER['HTTPS']) ? 'https://' : 'http://';?>
<?php if(strpos($_SERVER['REQUEST_URI'],"account")):;?><!-- If the current page is the admin panel: load the register button-->

<?php

    $site_id   = session("SiteSetup")['site_id'];
    $site_data = DB::table('sites')
        ->select('*')
        ->where('id',$site_id)
        ->first();


    $message = null;
    if($site_data->sent_emails!=0){
        if($site_data->sent_emails==1){
            $message = Lang::get('account/site.subscription.expired_1');
        }
        else if($site_data->sent_emails==2 && $site_data->block_date==null){
            $message = Lang::get('account/site.subscription.expired_2');
        }
        else if(strtotime( date("Y-m-d H:i:s"))-strtotime($site_data->block_date)>=0){
            $message = Lang::get('account/site.subscription.expired_3');
        }
    }
?>

<div class="row" id="updatePlan">
    <div class="col-lg-3" style="color: red; padding-left: 50px;">

        @if($message!=null)
            <h4 style="background: red;color: white;padding: 10px;width: 100%;">
                {{$message}}
                <a href="/account/payment/upgrade" target='_blank' style="color:white;">
                   <b> {{ Lang::get('account/site.Update')}}</b>
                </a>
            </h4>
        @endif

    </div>
    <div class="col-lg-3"></div>
    <div class="col-lg-3"></div>
    <div class="col-lg-3">
        <h4 id="planTag" style="margin-bottom:0px; margin-top:0px;">
            <p>{{ Lang::get('account/site.planactual')}}<b><?php echo  $plan;?></b>
                <a href="/account/payment/upgrade" target='_blank'>
                    <button type="button" class="btn btn-info">{{ Lang::get('account/site.Update')}}</button>
                </a>
            </p>
        </h4>
    </div>
</div>
<?php endif;?>
<!-- Mostrar el plan actual y un boton para actualizar el plan. -->