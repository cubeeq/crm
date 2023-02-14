<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'user_id',
        'done',
        'share',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
  
    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Shared Tasks relationship
     */
    public function shared()
    {
        return $this->belongsToMany(User::class, 'shared_task_user', 'task_id', 'user_id');
    }
}
