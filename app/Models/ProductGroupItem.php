<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGroupItem extends Model
{
    protected $guarded = [];

    /**
     * Product constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

}
