<?php

namespace Botble\Gallery\Models;

use Botble\Base\Models\BaseModel;

class GalleryTranslation extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'galleries_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'lang_code',
        'galleries_id',
        'name',
        'description',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
