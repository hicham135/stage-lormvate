<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluated_user_id', 'evaluator_id', 'period', 
        'performance_score', 'communication_score', 'teamwork_score',
        'innovation_score', 'comments', 'status'
    ];

    public function evaluatedUser()
    {
        return $this->belongsTo(User::class, 'evaluated_user_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}