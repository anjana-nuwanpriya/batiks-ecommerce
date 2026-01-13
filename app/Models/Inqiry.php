<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inqiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'is_read',
        'admin_notes'
    ];

    /**
     * Get the products for the inquiry.
     */
    public function products()
    {
        return $this->hasMany(ProductInquiry::class);
    }
}
