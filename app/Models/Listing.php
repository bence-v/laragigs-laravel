<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    Use HasFactory;
    protected $fillable = [
        'title',
        'company',
        'location',
        'website',
        'email',
        'description',
        'tags',
    ];

    public function scopeFilter($query, array $filters) {
        if($filters['tag'] ?? false) {
            $query->where('tags', 'like', "%{$filters['tag']}%");
        }

        if($filters['search'] ?? false) {
            $query->where('title', 'like', "%{$filters['search']}%")
                ->orWhere('description', 'like', "%{$filters['search']}%");
        }
    }

    public function getUrl() {
        return $this->logo ? asset('storage/'.$this->logo) : asset('images/no-image.png');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
