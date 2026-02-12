<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestAction extends Model
{
    protected $table = 'request_actions';

    protected $fillable = [
        'request_id',
        'description',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(PrsRequest::class);
    }
}
