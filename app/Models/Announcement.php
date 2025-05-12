<?php
// app/Models/Announcement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'department_id',
        'is_global',
        'expires_at',
        'is_pinned',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_global' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}