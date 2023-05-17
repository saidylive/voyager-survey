<!-- Adding / Editing -->
@php
    $dataType = \TCG\Voyager\Facades\Voyager::model('DataType')
        ->where('slug', '=', $reference_slug)
        ->first();
    $dataTypeContent = strlen($dataType->model_name) != 0 ? new $dataType->model_name() : false;
    $dataTypeRows = $dataType->{$edit ? 'editRows' : 'addRows'};
    $contoller = new \Saidy\VoyagerSurvey\Http\Controllers\PublicSurveyController();
    $contoller->removeRelationshipField($dataType, 'add');
    foreach ($dataTypeRows as $row) {
        $row->field = "{$reference_field}[{$row->field}]";
        if ($row->type == 'select_dependent_dropdown') {
            $details = $row->details;
            $details->reference_field = $reference_field;
            $row->details = $details;
        }
    }
@endphp

@foreach ($dataTypeRows as $row)
    <!-- GET THE DISPLAY OPTIONS -->
    @php
        // echo '<pre>';
        // print_r($row);
        // echo '</pre>';
        $display_options = $row->details->display ?? null;
        if ($dataTypeContent->{$row->field . '_' . ($edit ? 'edit' : 'add')}) {
            $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field . '_' . ($edit ? 'edit' : 'add')};
        }
    @endphp
    @if (isset($row->details->legend) && isset($row->details->legend->text))
        <legend class="text-{{ $row->details->legend->align ?? 'center' }}"
            style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">
            {{ $row->details->legend->text }}</legend>
    @endif

    <div class="form-group @if ($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ isset($errors) && $errors->has($row->field) ? 'has-error' : '' }}"
        @if (isset($display_options->id)) {{ "id=$display_options->id" }} @endif>
        {{ $row->slugify }}
        <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
        @include('voyager::multilingual.input-hidden-bread-edit-add')
        @if ($add && isset($row->details->view_add))
            {{-- <pre>add && view_add</pre> --}}
            @include($row->details->view_add, [
                'row' => $row,
                'dataType' => $dataType,
                'dataTypeContent' => $dataTypeContent,
                'content' => $dataTypeContent->{$row->field},
                'view' => 'add',
                'options' => $row->details,
            ])
        @elseif ($row->type == 'relationship')
            {{-- <pre>relationship</pre> --}}
            @include('voyager::formfields.relationship', [
                'options' => $row->details,
            ])
        @else
            {{-- <pre>else</pre> --}}
            {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
        @endif

        @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
            {!! $after->handle($row, $dataType, $dataTypeContent) !!}
        @endforeach
        @if (isset($errors) && $errors->has($row->field))
            @foreach ($errors->get($row->field) as $error)
                <span class="help-block">{{ $error }}</span>
            @endforeach
        @endif
    </div>
@endforeach
