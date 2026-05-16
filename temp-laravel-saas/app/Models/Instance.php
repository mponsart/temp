<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    use HasFactory;

    protected $fillable = [
        'subdomain',
        'email',
        'association_name',
        'stripe_subscription_id',
        'status',
    ];
}
