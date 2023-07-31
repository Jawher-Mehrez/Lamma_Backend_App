<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

class RoomPlayer extends Model
{
    use HasFactory;
    protected $fillable = [
        'score',
        'rank',
        'user_id',
        'room_id',
        'kicked',
        'left'
    ];

    protected $with = [
        'player',
    ];

    protected $appends = [
        'play_time_by_minutes',
        'play_time_by_hours',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function getPlayTimeByMinutesAttribute(): String
    {
        return Carbon::parse($this->created_at)->diffInMinutes(Carbon::parse($this->updated_at)) . 'm';
    }

    public function getPlayTimeByHoursAttribute(): Int
    {
        return Carbon::parse($this->created_at)->diffInHours(Carbon::parse($this->updated_at));
    }
}
