<?php

namespace Saidy\VoyagerSurvey\Http\Controllers;

use Illuminate\Http\Request;
use Saidy\VoyagerSurvey\Models\SurveyModel;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;

class PublicSurveyController extends VoyagerBaseController
{
    use BreadRelationshipParser {
        removeRelationshipField as public;
    }

    public function index($survey_key)
    {

        $model = SurveyModel::where("survey_key", $survey_key)->first();
        if (!$model) abort(404);
        $is_single = $model->method == "row";
        $slug = $is_single ? $model->target_slug : $model->slug;

        $reference_datatype = null;
        $target_slug_column = null;
        $reference_model = null;
        $reference_id = null;

        if ($is_single) {
            $reference_datatype = Voyager::model('DataType')->where('slug', '=', $model->slug)->first();
            if ($reference_datatype && strlen($reference_datatype->model_name) != 0) {
                $reference_model = call_user_func(array($reference_datatype->model_name, 'find'), $model->row_id);
                if ($reference_model) {
                    $reference_id = $reference_model->{$model->column};
                    $target_slug_column = $model->target_slug_column;
                }
            }
        }

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;


        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }
        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $errors = [];
        $view = 'saidy-voyager-survey::bread.edit-add';

        return Voyager::view($view, compact(
            'survey_key',
            'model',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'reference_datatype',
            'target_slug_column',
            'reference_model',
            'reference_id',
            'is_single',
        ));
    }

    private function duplicate_request(Request $req, $data)
    {
        $request = $req->duplicate();
        $req_keys = array_keys($req->all());
        array_map(function ($key) use ($request) {
            $request->request->remove($key);
        }, $req_keys);
        $request->request->add($data);
        return $request;
    }

    private function store_reference_data(Request $req, $slug, $inputs)
    {
        $model = null;
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        if ($dataType) {
            $request = $this->duplicate_request($req, $inputs);
            $val = $this->validateBread($inputs, $dataType->addRows)->validate();
            $model = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
        }
        return $model;
    }

    public function store_survey($survey_key, Request $request)
    {
        $model = SurveyModel::where("survey_key", $survey_key)->first();
        if (!$model) abort(404);

        $is_single = $model->method == "row";
        $slug = $is_single ? $model->target_slug : $model->slug;

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $reference_datatype = null;
        $target_slug_column = null;
        $reference_model = null;
        $reference_id = null;

        if ($is_single) {
            $reference_datatype = Voyager::model('DataType')->where('slug', '=', $model->slug)->first();
            if ($reference_datatype && strlen($reference_datatype->model_name) != 0) {
                $reference_model = call_user_func(array($reference_datatype->model_name, 'find'), $model->row_id);
                if ($reference_model) {
                    $reference_id = $reference_model->{$model->column};
                    $target_slug_column = $model->target_slug_column;
                }
            }
        }



        $input_all = $request->all();
        $inputs = $request->all();
        if ($target_slug_column && $reference_id) {
            $inputs[$target_slug_column] = $reference_id;
        }

        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $row = $dataType->addRows()->where("field", $key)->first();
                if ($row) {
                    $survey = $row->details->survey ?? null;
                    if ($survey) {
                        $reference_slug = $survey->reference_slug ?? null;
                        $reference_column = $survey->reference_column ?? null;
                        $data = $this->store_reference_data($request, $reference_slug, $value);
                        if ($data) {
                            $replace_value = $data->$reference_column;
                            $inputs[$key] = $replace_value;
                        }
                    }
                }
            }
        }

        $request = $this->duplicate_request($request, $inputs);


        // Validate fields with ajax
        $val = $this->validateBread($inputs, $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
        $primary_key = $data->getKeyName();

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            $redirect = redirect()->route("saidy.voyager.survey.public_view_thanks", ['survey_key' => $survey_key, "id" => $data->$primary_key]);

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

    public function thanks($survey_key, $id, Request $request)
    {
        $model = SurveyModel::where("survey_key", $survey_key)->first();
        if (!$model) abort(404);

        $slug = $model->slug;

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $view = 'saidy-voyager-survey::bread.thanks';
        return Voyager::view($view, compact('survey_key', 'id', 'dataType'));
    }
}
