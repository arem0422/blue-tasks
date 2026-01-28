<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['titulo', 'prioridad', 'estado', 'project_id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function scopeFilter(Builder $q, array $filters): Builder
    {
        return $q
            ->when($filters['project_id'] ?? null, fn ($qq, $v) => $qq->where('project_id', $v))
            ->when($filters['estado'] ?? null, fn ($qq, $v) => $qq->where('estado', $v))
            ->when($filters['prioridad'] ?? null, fn ($qq, $v) => $qq->where('prioridad', $v));
    }

}
