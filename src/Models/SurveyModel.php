<?php

namespace Saidy\VoyagerSurvey\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyModel extends Model
{
    protected $fillable = [
        "slug",
        "survey_key",
        "row_id",
        "method",
        "starts_at",
        "ends_at",
        "options",
    ];
}
