<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use Cachable;

    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    protected $casts = [
        'value' => 'json',
    ];
}
