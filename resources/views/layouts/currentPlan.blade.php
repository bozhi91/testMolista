<!-- Mostrar el plan actual y un boton para actualizar el plan. -->
<?php $plan= App\Http\Controllers\Account\ReportsController::getPlan();?>
<?php $protocol =isset($_SERVER['HTTPS']) ? 'https://' : 'http://';?>

<?php if(strpos($_SERVER['PHP_SELF'],"account")):;?><!-- If the current page is the admin panel: load the register button-->
<div class="row" id="updatePlan">
    <div class="col-sm-3"></div>
    <div class="col-sm-3"></div>
    <div class="col-sm-3"></div>

    <div class="col-sm-3">
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