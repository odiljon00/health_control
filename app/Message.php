<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message',
        'status', 'is_starred', 'admin_note',
        'read_at', 'replied_at', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'is_starred' => 'boolean',
        'read_at'    => 'datetime',
        'replied_at' => 'datetime',
    ];

    public function scopeNew($query) {
        return $query->where('status', 'new');
    }

    public function scopeStarred($query) {
        return $query->where('is_starred', true);
    }

    public function getStatusBadgeAttribute() {
        return [
            'new'      => ['🆕', 'Yangi', 'red'],
            'read'     => ['👁️', "O'qilgan", 'yellow'],
            'replied'  => ['✅', 'Javob berilgan', 'green'],
            'archived' => ['📦', 'Arxivlangan', 'navy'],
        ][$this->status] ?? ['', $this->status, 'navy'];
    }
}