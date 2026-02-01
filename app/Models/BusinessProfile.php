<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'business_address',
        'business_email',
        'business_website',
        'business_phone',
        'logo_path',
        'footer_text',
    ];
}
