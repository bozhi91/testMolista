
    <?php
        $sites = DB::select("select * from  marketplaces where free = true");

    function create_table($data) {
        $res = '<table width="300" border="0"  style="margin-left: 50px;">';
        $max_data = sizeof($data);
        $ctr = 1;
        foreach ($data as $db_data) {
            if ($ctr % 2 == 0) $res .= '<td align="left" >'.$db_data['checkbox'].'<img src='.$db_data['icon'].'>'.$db_data['label'].'</td></tr>';
            else {
                if ($ctr < $max_data) $res .= '<tr><td align="left">'.$db_data['checkbox'].'<img src='.$db_data['icon'].'>'.$db_data['label'].'</td>';
                else $res .= '<tr><td align="left">'.$db_data['checkbox'].'<img src='.$db_data['icon'].'>'.$db_data['label'].'</td><td ></td></tr>';
            }
            $ctr++;
        }
        return $res . '</table>';
    }
    ?>

    <div class="modal fade" id="propertyModal" role="dialog">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> {{ Lang::get('account/properties.marketplace.publish') }}</h4>
            </div>

                <br>
                <button style="margin-left: 10px;" class="btn btn-info" id="checkall" onclick="checkall()">+</button>
                {{ Lang::get('account/properties.marketplace.selectAll') }}

                <div class="modal-body">
                    {!! Form::open(['route' => 'publishProperty']) !!}
                    <?php $table = array();
                        foreach($sites as $site){
                            $checkbox = "<input type='checkbox' class='chk_boxes' name='marketplace[]' value=".$site->id." />";
                            $icon_url = "http://".$site->code.".molista.com/marketplaces/".$site->logo;
                            $label    = $site->code;
                            $row = array("checkbox"=>$checkbox,"icon"=>$icon_url,"label"=>$label);
                            array_push($table,$row);
                        }
                    ?>
                    <div style="margin-left:-20px;">
                      <?php echo create_table($table);?>
                    </div><br>
                    {!! Form::submit(Lang::get('account/properties.marketplace.submit'), ['class' => 'btn btn-info']) !!}
                    {!! Form::close() !!}
                    <div class="modal-header"> </div>
                        <h4 class="modal-title">{{ Lang::get('account/properties.marketplace.info') }} </h4>
                </div>
            </div>
        </div>
    </div>

    <script  type="text/javascript">
        function checkall(){
            $('.chk_boxes').click();
        }
    </script>