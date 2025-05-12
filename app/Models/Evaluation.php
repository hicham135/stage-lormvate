<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'period',
        'performance_score',
        'punctuality_score',
        'teamwork_score',
        'initiative_score',
        'comments',
        'goals',
        'status', // 'draft', 'submitted', 'acknowledged'
    ];

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluatee()
    {
        return $this->belongsTo(User::class, 'evaluatee_id');
    }
}