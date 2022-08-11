<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'question',
        'question_type'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function generateLink(Customer $customer)
    {
        return url("/api/surveys/{$this->id}/customers/{$customer->id}/answer");
    }
}
