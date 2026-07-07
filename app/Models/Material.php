<?php

namespace App\Models;

use App\Services\FileStorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPES = ['pdf', 'ppt', 'video', 'youtube', 'image', 'link'];

    protected $fillable = [
        'class_subject_id',
        'created_by',
        'title',
        'description',
        'type',
        'file_path',
        'url',
        'youtube_id',
        'file_name',
        'file_size',
        'mime_type',
        'order',
        'topic',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id')->latest();
    }

    public function allComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    public function getFileSizeFormattedAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($this->file_size, 0);
        $pow = min(floor(($bytes ? log($bytes) : 0) / log(1024)), count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'pdf' => 'bi-file-earmark-pdf',
            'ppt' => 'bi-file-earmark-ppt',
            'video' => 'bi-camera-video',
            'youtube' => 'bi-youtube',
            'link' => 'bi-link-45deg',
            'image' => 'bi-image',
            default => 'bi-file-earmark',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'pdf' => 'PDF',
            'ppt' => 'PowerPoint',
            'video' => 'Video',
            'youtube' => 'YouTube',
            'link' => 'Tautan',
            'image' => 'Gambar',
            default => ucfirst($this->type),
        };
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id ?: FileStorageService::extractYoutubeId($this->url);

        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }

    public function isDownloadable(): bool
    {
        return in_array($this->type, ['pdf', 'ppt', 'video', 'image'], true) && $this->file_path;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
