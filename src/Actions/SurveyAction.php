<?php

namespace Saidy\VoyagerSurvey\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Saidy\VoyagerSurvey\Models\SurveyModel;
use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Str;

class SurveyAction extends AbstractAction
{
    /**
     * Optional mimes
     */
    protected $mimes;

    /**
     * Optional File Path
     */
    protected $filePath;

    /**
     * Optional Disk
     */
    protected $survey = false;

    /**
     * Optional Reader Type
     */
    protected $readerType;

    public function getFormConfigs()
    {
        $rows = [
            [

            ]
        ];

    }

    public function getTitle()
    {
        return __('saidy-voyager-survey::generic.bulk_import');
    }

    public function getIcon()
    {
        return 'voyager-megaphone';
    }

    public function getPolicy()
    {
        return 'browse';
    }

    public function getAttributes()
    {
        return [
            'id'    => 'bulk_survey_btn',
            'class' => 'btn btn-primary',
        ];
    }

    public function getDefaultRoute()
    {
        // return route('my.route');
    }

    public function shouldActionDisplayOnDataType()
    {
        return config('voyager.survey.enabled', true) !== false
            && isInPatterns(
                $this->dataType->slug,
                config('voyager.survey.allowed_slugs', ['*'])
            )
            && !isInPatterns(
                $this->dataType->slug,
                config('voyager.survey.not_allowed_slugs', [])
            );
    }

    public function massAction($ids, $comingFrom)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug(request());
        $id = request()->input("id");
        $inputs = request()->except(['_token', 'action']);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        // Check permission
        Gate::authorize('browse', app($dataType->model_name));

        $validator = Validator::make(request()->all(), [
            'slug' => 'required',
            // 'starts_at' => 'sometimes|date',
            // 'ends_at' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return redirect($comingFrom)->with([
                'message'    => $validator->errors()->first(),
                'alert-type' => 'error',
            ]);
        }

        $model = null;

        if ($id) {
            $model = SurveyModel::find($id);
            $model->update($inputs);
        }
        if (!$model) {
            $inputs["survey_key"] = Str::random(30);
            $inputs["method"] = "list";
            $model = SurveyModel::create($inputs);

        }
        return redirect($comingFrom)->with([
            'message'    => $id ? "Survey updated successfully" : "Survey Created Successfully",
            'alert-type' => 'success',
        ]);
    }

    public function view()
    {
        $view = 'saidy-voyager-survey::bread.survey';

        if (view()->exists('saidy-voyager-survey::' . $this->dataType->slug . '.survey')) {
            $view = 'saidy-voyager-survey::' . $this->dataType->slug . '.survey';
        }
        return $view;
    }

    protected function getSlug(Request $request)
    {
        if (isset($this->slug)) {
            $slug = $this->slug;
        } else {
            $slug = explode('.', $request->route()->getName())[1];
        }

        return $slug;
    }

    public function getSurveyModel()
    {
        if ($this->survey === false) {
            $this->survey  = SurveyModel::where("slug", $this->dataType->slug)->first();
        }
        return $this->survey;
    }

    public function hasSurveyModel()
    {
        if ($this->survey === false) $this->getSurveyModel();
        return $this->survey ? true : false;
    }

    public function getSurveyModelData($key, $default = null, $date = false)
    {
        if ($this->survey && isset($this->survey->$key)) {
            return $date ? date("Y-m-d", strtotime($this->survey->$key)) : $this->survey->$key;
        }
        return $default;
    }

    public function getSurveyLink()
    {
        return $this->getSurveyModelData("survey_link");
    }
}
