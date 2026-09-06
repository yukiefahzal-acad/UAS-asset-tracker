<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'action',
        'actor_name',
        'admin_name',
        'notes',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class)->withTrashed();
    }

    public function getActionDetailsAttribute(): array
    {
        return match ($this->action) {
            'registered' => [
                'label' => 'Registrasi Aset',
                'color' => 'emerald',
                'icon' => 'plus-circle',
            ],
            'updated' => [
                'label' => 'Pembaruan Data Aset',
                'color' => 'blue',
                'icon' => 'edit',
            ],
            'borrowed' => [
                'label' => 'Peminjaman Disetujui',
                'color' => 'blue',
                'icon' => 'arrow-up-right',
            ],
            'returned' => [
                'label' => 'Pengembalian Diterima',
                'color' => 'emerald',
                'icon' => 'arrow-down-left',
            ],
            'marked_broken' => [
                'label' => 'Dilaporkan Rusak',
                'color' => 'rose',
                'icon' => 'alert-octagon',
            ],
            'soft_deleted' => [
                'label' => 'Ditandai Rusak/Dibuang',
                'color' => 'rose',
                'icon' => 'trash-2',
            ],
            'restored' => [
                'label' => 'Dipulihkan',
                'color' => 'indigo',
                'icon' => 'refresh-cw',
            ],
            default => [
                'label' => ucfirst($this->action),
                'color' => 'gray',
                'icon' => 'activity',
            ],
        };
    }
}
