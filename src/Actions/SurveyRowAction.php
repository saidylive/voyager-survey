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

class SurveyRowAction extends AbstractAction
{
    /**
     * Optional Disk
     */
    protected $survey = false;


    public function getTitle()
    {
        return 'Survey';
    }

    public function getIcon()
    {
        return 'voyager-megaphone';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right edit',
            'style' => 'margin-right: 5px;',
        ];
    }

    public function getDefaultRoute()
    {
        return route('saidy.voyager.survey.survey_action', [
            "slug" => $this->dataType->slug,
            "id" => $this->data->{$this->data->getKeyName()}
        ]);
    }

    public function shouldActionDisplayOnRow($row)
    {
        return config('voyager.survey.enabled', true) !== false
            && isInPatterns(
                $this->dataType->slug,
                config('voyager.survey.allowed_slugs_row', ['*'])
            )
            && !isInPatterns(
                $this->dataType->slug,
                config('voyager.survey.not_allowed_slugs_row', [])
            );
        // return true;
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
        $survey_key  = $this->getSurveyModelData("survey_key");
        $link  = url("public/survey/{$survey_key}");
        return $link;
    }
}
