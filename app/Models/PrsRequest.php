<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrsRequest extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'user_id',
        'request_id',
        'item_name',
        'description',
        'quantity',
        'status',
        'approved_by_id',
        'approved_at',
        'rejected_by_id',
        'rejected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    public function requestActions(): HasMany
    {
        return $this->hasMany(RequestAction::class, 'request_id');
    }

    /**
     * Generate next request_id: REQ-YYYY-MM-NNNNN
     */
    public static function generateRequestId(): string
    {
        $year = date('Y');
        $month = date('m');
        $key = "{$year}-{$month}";

        $last = static::whereRaw("request_id LIKE ?", ["REQ-{$key}-%"])
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($last && preg_match('/REQ-\d{4}-\d{2}-(\d+)/', $last->request_id, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return sprintf('REQ-%s-%s-%05d', $year, $month, $seq);
    }
}
