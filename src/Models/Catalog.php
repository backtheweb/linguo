<?php

namespace Backtheweb\Linguo\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{

    protected $table = 'linguo_catalogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'locale', 'source_format',
    ];

}
