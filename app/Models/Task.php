<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'assigned_by',
        'assigned_to',
        'department_id',
        'due_date',
        'priority', // 'low', 'medium', 'high', 'urgent'
        'status', // 'pending', 'in_progress', 'completed', 'delayed'
        'progress',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'progress' => 'integer',
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
