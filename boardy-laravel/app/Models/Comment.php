<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $connection = 'mysql_api';

    protected $fillable = [
        'body',
        'post_id',
        'author_id',
	'author_name',
    ];

    // Связь с постом
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    // Связь с автором (User)
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
