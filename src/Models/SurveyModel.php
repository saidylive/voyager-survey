<?php

namespace Saidy\VoyagerSurvey\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyModel extends Model
{
    protected $fillable = [
        "slug",
        "survey_key",
        "row_id",
        "column",
        "target_slug",
        "target_slug_column",
        "method",
        "starts_at",
        "ends_at",
        "options",
    ];

    public function setOptionsAttribute($value)
    {
        $this->attributes['options'] = json_encode($value);
    }

    public function getOptionsAttribute($value)
    {
        return json_decode(!empty($value) ? $value : '{}');
    }

    public function getSurveyLinkAttribute()
    {
        return $this->survey_key ? url("public/survey/{$this->survey_key}") : null;
    }
}
