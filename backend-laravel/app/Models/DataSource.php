<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class DataSource extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_sources';

    protected $fillable = [
        'name',
        'connection_string',
        'type',
        'user_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'connection_string',
    ];

    // Encrypt connection string when storing
    public function setConnectionStringAttribute($value): void
    {
        $this->attributes['connection_string'] = $value ? Crypt::encryptString($value) : null;
    }

    // Decrypt connection string when retrieving
    public function getConnectionStringAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Get the user that owns the data source
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all reports that use this data source
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get all queries that use this data source
     */
    public function queries(): HasMany
    {
        return $this->hasMany(Query::class);
    }

    /**
     * Get all dashboards that use this data source
     */
    public function dashboards(): HasMany
    {
        return $this->hasMany(Dashboard::class);
    }

    /**
     * Scope to filter by data source type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get active data sources (with valid connections)
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('connection_string')
                    ->where('connection_string', '!=', '');
    }

    /**
     * Scope to get database type data sources
     */
    public function scopeDatabase($query)
    {
        return $query->whereIn('type', ['mysql', 'postgresql', 'sqlite', 'sqlserver', 'oracle']);
    }

    /**
     * Scope to get API type data sources
     */
    public function scopeApi($query)
    {
        return $query->whereIn('type', ['rest_api', 'graphql', 'webhook']);
    }

    /**
     * Scope to get file type data sources
     */
    public function scopeFile($query)
    {
        return $query->whereIn('type', ['csv', 'excel', 'json', 'xml']);
    }

    /**
     * Check if data source is database type
     */
    public function isDatabaseType(): bool
    {
        return in_array($this->type, ['mysql', 'postgresql', 'sqlite', 'sqlserver', 'oracle']);
    }

    /**
     * Check if data source is API type
     */
    public function isApiType(): bool
    {
        return in_array($this->type, ['rest_api', 'graphql', 'webhook']);
    }

    /**
     * Check if data source is file type
     */
    public function isFileType(): bool
    {
        return in_array($this->type, ['csv', 'excel', 'json', 'xml']);
    }

    /**
     * Get the display name for the data source type
     */
    public function getTypeDisplayNameAttribute(): string
    {
        $typeNames = [
            'mysql' => 'MySQL',
            'postgresql' => 'PostgreSQL',
            'sqlite' => 'SQLite',
            'sqlserver' => 'SQL Server',
            'oracle' => 'Oracle',
            'rest_api' => 'REST API',
            'graphql' => 'GraphQL',
            'webhook' => 'Webhook',
            'csv' => 'CSV File',
            'excel' => 'Excel File',
            'json' => 'JSON File',
            'xml' => 'XML File',
        ];

        return $typeNames[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-assign user_id when creating
        static::creating(function ($dataSource) {
            if (!$dataSource->user_id && auth()->check()) {
                $dataSource->user_id = auth()->id();
            }
        });
    }
}