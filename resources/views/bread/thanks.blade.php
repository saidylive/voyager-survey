@php
    $survey_title = config('voyager.survey.survey_title', 'Registration');
@endphp

@extends('saidy-voyager-survey::layout.master')

@section('page_title', $survey_title . ': ' . $dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title" style="padding-left: 15px;"></h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="text-align: center;">
                    <h1 class="page-title text-center" style="padding-left: 15px;">
                        Thanks for your response.
                    </h1>
                    <p>For any query use reference number: {{ $id }}.</p>
                    <p>To submit any other response <a href="{{ url("public/survey/$survey_key") }}">Click here</a>.</p>
                    <p>&nbsp;</p>
                </div>
            </div>
        </div>
    </div>
@stop
