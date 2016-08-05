<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[professional_enabled]', Lang::get('account/properties.professional_enabled')) !!}
            {!! Form::select('details[professional_enabled]', [ '' => '','0'=>Lang::get('general.no'), '1'=>Lang::get('general.yes') ], null, [ 'class'=>'form-control' ]) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[commercial_enabled]', Lang::get('account/properties.commercial_enabled')) !!}
            {!! Form::select('details[commercial_enabled]', [ '' => '','0'=>Lang::get('general.no'), '1'=>Lang::get('general.yes') ], null, [ 'class'=>'form-control' ]) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[property_disposal]', Lang::get('account/properties.property_disposal')) !!}
            {!! Form::select('details[property_disposal]', [ '' => '', 'front'=>Lang::get('account/properties.property_disposal.front'), 'back'=>Lang::get('account/properties.property_disposal.back'), 'internal' => Lang::get('account/properties.property_disposal.internal') ], null, [ 'class'=>'form-control' ]) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[expenses]', Lang::get('account/properties.expenses')) !!}
            <div class="input-group">
                @if ( $infocurrency->position == 'before' )
                    <div class="input-group-addon">{{ $infocurrency->symbol }}</div>
                @endif
                {!! Form::text('details[expenses]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                @if ( $infocurrency->position == 'after' )
                    <div class="input-group-addon">{{ $infocurrency->symbol }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[property_condition]', Lang::get('account/properties.property_condition')) !!}
            {!! Form::select('details[property_condition]', [ '' => '',
                'excelent'=>Lang::get('account/properties.condition.excelent'),
                'very_good'=>Lang::get('account/properties.condition.very_good'),
                'good'=>Lang::get('account/properties.condition.good'),
                'modderate'=>Lang::get('account/properties.condition.modderate'),
                'poor'=>Lang::get('account/properties.condition.poor'),
            ], null, [ 'class'=>'form-control' ]) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[building_condition]', Lang::get('account/properties.building_condition')) !!}
            {!! Form::select('details[building_condition]', [ '' => '',
                'excelent'=>Lang::get('account/properties.condition.excelent'),
                'very_good'=>Lang::get('account/properties.condition.very_good'),
                'good'=>Lang::get('account/properties.condition.good'),
                'modderate'=>Lang::get('account/properties.condition.modderate'),
                'poor'=>Lang::get('account/properties.condition.poor'),
            ], null, [ 'class'=>'form-control' ]) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[bedrooms]', Lang::get('account/properties.bedrooms')) !!}
            {!! Form::text('details[bedrooms]', null, [ 'class'=>'form-control digits', 'min'=>'0' ]) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[toilettes]', Lang::get('account/properties.toilettes')) !!}
            {!! Form::text('details[toilettes]', null, [ 'class'=>'form-control digits', 'min'=>'0' ]) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[covered_area]', Lang::get('account/properties.covered_area')) !!}
            <div class="input-group">
                {!! Form::text('details[covered_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                <div class="input-group-addon">m²</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[semi_covered_area]', Lang::get('account/properties.semi_covered_area')) !!}
            <div class="input-group">
                {!! Form::text('details[semi_covered_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                <div class="input-group-addon">m²</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[uncovered_area]', Lang::get('account/properties.uncovered_area')) !!}
            <div class="input-group">
                {!! Form::text('details[uncovered_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                <div class="input-group-addon">m²</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[lot_area]', Lang::get('account/properties.lot_area')) !!}
            <div class="input-group">
                {!! Form::text('details[lot_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                <div class="input-group-addon">m²</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[buildable_area]', Lang::get('account/properties.buildable_area')) !!}
            <div class="input-group">
                {!! Form::text('details[buildable_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                <div class="input-group-addon">m²</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[basement_area]', Lang::get('account/properties.basement_area')) !!}
            <div class="input-group">
                {!! Form::text('details[basement_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                <div class="input-group-addon">m²</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[mezzanine_area]', Lang::get('account/properties.mezzanine_area')) !!}
            <div class="input-group">
                {!! Form::text('details[mezzanine_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                <div class="input-group-addon">m²</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group error-container">
            {!! Form::label('details[basement_area]', Lang::get('account/properties.basement_area')) !!}
            <div class="input-group">
                {!! Form::text('details[basement_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
                <div class="input-group-addon">m²</div>
            </div>
        </div>
    </div>
</div>
