<?php

namespace DailyRecipe\Settings;

use DailyRecipe\Model;

class Setting extends Model
{
    protected $fillable = ['setting_key', 'value'];

    protected $primaryKey = 'setting_key';
}
