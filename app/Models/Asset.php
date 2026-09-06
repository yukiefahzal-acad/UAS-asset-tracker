<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'category',
        'location',
        'purchase_price',
        'purchase_date',
        'status',
        'borrowed_by',
        'approved_by',
        'borrowed_at',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchase_date' => 'date',
        'borrowed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (empty($asset->uuid)) {
                $asset->uuid = (string) Str::uuid();
            }
            if (empty($asset->code)) {
                $year = date('Y');
                $count = static::withTrashed()->count() + 1;
                $asset->code = sprintf('AST-%s-%04d', $year, $count);
            }
        });

        static::created(function ($asset) {
            $adminName = Auth::check() ? Auth::user()->name : 'System Admin';
            $asset->logs()->create([
                'action' => 'registered',
                'actor_name' => $adminName,
                'admin_name' => $adminName,
                'notes' => 'Aset didaftarkan ke sistem inventaris.',
            ]);
        });

        static::deleted(function ($asset) {
            $adminName = Auth::check() ? Auth::user()->name : request('admin_name', 'Finance Admin');
            $asset->logs()->create([
                'action' => 'soft_deleted',
                'actor_name' => $adminName,
                'admin_name' => $adminName,
                'notes' => request('reason', 'Aset ditandai rusak / dibuang (Soft Delete).'),
            ]);
        });
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssetLog::class)->orderBy('created_at', 'desc');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && is_null($this->deleted_at);
    }

    public function isBorrowed(): bool
    {
        return $this->status === 'borrowed';
    }

    public function isBroken(): bool
    {
        return $this->status === 'broken';
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->purchase_price, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->trashed()) {
            return [
                'label' => 'Dibuang / Dihapus',
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-700',
                'border' => 'border-gray-300',
                'icon' => 'trash-2',
            ];
        }

        return match ($this->status) {
            'available' => [
                'label' => 'Tersedia',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'icon' => 'check-circle',
            ],
            'borrowed' => [
                'label' => 'Dipinjam',
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-700',
                'border' => 'border-amber-200',
                'icon' => 'clock',
            ],
            'broken' => [
                'label' => 'Rusak',
                'bg' => 'bg-rose-50',
                'text' => 'text-rose-700',
                'border' => 'border-rose-200',
                'icon' => 'alert-triangle',
            ],
            default => [
                'label' => ucfirst($this->status),
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-700',
                'border' => 'border-gray-300',
                'icon' => 'help-circle',
            ],
        };
    }
}
