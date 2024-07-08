<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'description',
        'time',
        'amount',
        'discount',
        'images',
        'category_id',
        'user_id',
    ];

    protected $casts = [
        'images' => 'json', // Assurez-vous que le champ images est casté en JSON
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
