<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];
    protected $table = 'cart';

    /**
     * Product constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function setFields($data)
    {
        $this->user_id = $data['user_id'];
        $this->quantity = 1;
        $this->product_id = $data['product_id'];
    }


}
