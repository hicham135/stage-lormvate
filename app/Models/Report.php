<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // 'attendance', 'performance', 'tasks', 'general'
        'department_id',
        'period_start',
        'period_end',
        'generated_by',
        'file_path',
        'parameters',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'parameters' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}