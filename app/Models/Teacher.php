<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass(): HasOne
    {
        return $this->hasOne(SchoolClass::class, 'teacher_id');
    }

    public function schedules()
    {
        return $this->hasMany(TeacherSchedule::class, 'teacher_id');
    }
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }
}
