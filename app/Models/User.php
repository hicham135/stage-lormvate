<<<<<<< HEAD
<?php 

=======
<?php
>>>>>>> c20c1856788050a6e6e89bca26b992efb1776b00
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
<<<<<<< HEAD
        'name',
        'email',
        'password',
        'role',
        'department_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
=======
        'name', 'email', 'role', 'department_id'
>>>>>>> c20c1856788050a6e6e89bca26b992efb1776b00
    ];

    /**
     * Get the department that the user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

<<<<<<< HEAD
    /**
     * Get the tasks assigned to the user.
     */
=======
>>>>>>> c20c1856788050a6e6e89bca26b992efb1776b00
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

<<<<<<< HEAD
    /**
     * Get the attendances for the user.
     */
=======
>>>>>>> c20c1856788050a6e6e89bca26b992efb1776b00
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the evaluations for the user.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluated_user_id');
    }
}