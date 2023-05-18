<?php

namespace Saidy\VoyagerSurvey\Http\Controllers;

use Illuminate\Http\Request;
use Saidy\VoyagerSurvey\Models\SurveyModel;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;

class SurveyController extends VoyagerBaseController
{
    private function getTableColumnsBySlug($slug)
    {

        $dataType = Voyager::model('DataType')->where('slug', $slug)->first();
        $items = $dataType ? SchemaManager::describeTable($dataType->name)->pluck("name", "name") : [];
        return $items;
    }

    private function getDataTypeList()
    {

        $items = Voyager::model('DataType')->get()->pluck("display_name_singular", "slug");
        return $items;
    }

    private function getFieldsRows($slug, $id)
    {
        $rows = [];
        $items = [
            [
                "data_type_id" => null,
                "field" => "survey_key",
                "type" => "hidden",
                "display_name" => "survey_key",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 0,
                "add" => 1,
                "delete" => 1,
                "details" => null,
                "order" => 1,
            ],
            [
                "data_type_id" => null,
                "field" => "slug",
                "type" => "hidden",
                "display_name" => "Slug",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => ["default" => $slug],
                "order" => 1,
            ],
            [
                "data_type_id" => null,
                "field" => "method",
                "type" => "hidden",
                "display_name" => "Method",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => ["default" => "row"],
                "order" => 1,
            ],
            [
                "data_type_id" => null,
                "field" => "row_id",
                "type" => "hidden",
                "display_name" => "Row ID",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => ["default" => $id],
                "order" => 1,
            ],
            [
                "data_type_id" => null,
                "field" => "column",
                "type" => "select_dropdown",
                "display_name" => "Column",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => [
                    "options" => $this->getTableColumnsBySlug($slug),
                ],
                "order" => 1,
            ],
            [
                "data_type_id" => null,
                "field" => "target_slug",
                "type" => "select_dropdown",
                "display_name" => "Target Table",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => [
                    "options" => $this->getDataTypeList(),
                ],
                "order" => 1,
            ],
            [
                "data_type_id" => null,
                "field" => "target_slug_column",
                "type" => "select_dropdown",
                "display_name" => "Target Table Column",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => [
                    "options" => [],
                ],
                "order" => 1,
            ],
            [
                "data_type_id" => null,
                "field" => "starts_at",
                "type" => "date",
                "display_name" => "Start Date",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => null,
                "order" => 1,
            ],
            [
                "data_type_id" => null,
                "field" => "ends_at",
                "type" => "date",
                "display_name" => "End Date",
                "required" => 1,
                "browse" => 1,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => null,
                "order" => 2,
            ],
            [
                "data_type_id" => null,
                "field" => "options",
                "type" => "text_area",
                "display_name" => "Optional Details",
                "required" => 0,
                "browse" => 0,
                "read" => 1,
                "edit" => 1,
                "add" => 1,
                "delete" => 1,
                "details" => [],
                "order" => 3,
            ],
        ];
        foreach ($items as $item) {
            $row = new DataRow($item);
            $row->details = json_decode(json_encode($item["details"]));
            $rows[] = new DataRow($item);
        }
        return collect($rows);
    }

    public function surveyAction($slug, $id)
    {
        $targetDataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($targetDataType->model_name));

        $view = 'saidy-voyager-survey::survey.create-action';
        $model = SurveyModel::where(["slug" => $slug, "row_id" => $id])->first();
        $reference_id = $id;
        $dataTypeContent = $model ?? new SurveyModel();
        if (!$model) $dataTypeContent->options = "";
        $dataTypeRows = $this->getFieldsRows($slug, $id);
        $dataType = new DataType;

        return Voyager::view($view, compact('slug', 'reference_id', 'model', 'dataType', 'dataTypeContent', 'dataTypeRows'));
    }

    public function StoreSurveyAction($slug, $id, Request $request)
    {
        $model = SurveyModel::where(["slug" => $slug, "row_id" => $id])->first();
        $survey_key = $model ? $model->survey_key : Str::random(30);
        $request->merge(["survey_key" => $survey_key]);

        $inputs = $request->all();

        $dataTypeRows = $this->getFieldsRows($slug, $id);
        $dataType = new DataType;
        $dataTypeModel = $model ?? new SurveyModel();

        // Validate fields with ajax
        $val = $this->validateBread($inputs, $dataTypeRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataTypeRows, $dataTypeModel);
        $primary_key = $data->getKeyName();

        event(new BreadDataAdded($dataType, $data));

        return back()->with([
            'message'    => $model ? 'Survey Updated Successfully.' : 'Survey Created Successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function get_table_columns_by_slug($slug){
        return $this->getTableColumnsBySlug($slug);
    }

}
