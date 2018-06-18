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

    $message="";
    if($site_data->sent_emails==1){
        $message = Lang::get('account/site.subscription.toExpire');
    }
    if($site_data->sent_emails==2){
        $message = Lang::get('account/site.subscription.expired');
    }
?>

<div class="row" id="updatePlan">
    <div class="col-lg-3" style="color: red; padding-left: 50px;"><h4>{{$message}}</h4></div>
    <div class="col-lg-3"></div>
    <div class="col-lg-3"></div>
    <div class="col-lg-3">
        <h4 id="planTag" style="margin-bottom:0px; margin-top:0px;">
            <p>Plan actual:<b><?php echo  $plan;?></b>
                <a href="/account/payment/upgrade" target='_blank'>
                    <button type="button" class="btn btn-info">Actualizar</button>
                </a>
            </p>
        </h4>
    </div>
</div>
<?php endif;?>
<!-- Mostrar el plan actual y un boton para actualizar el plan. -->