<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductGroup extends Model
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

    public function productGroupItems()
    {
        return $this->hasMany('App\Models\ProductGroupItem','group_id','id');
    }

}
