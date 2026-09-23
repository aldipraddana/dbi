<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Scopes\OrderByCreationDateDescendingScope;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperUser
 */
#[ScopedBy([OrderByCreationDateDescendingScope::class])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'employee_card_number',
        'resident_card_number',
        'phone_number',
        'user_position_id',
        'username',
        'role',
        'password',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        parent::booted();
        static::creating(function (User $model) {
            $userId = auth()->id();
            $model->created_by = $userId;
            $model->updated_by = $userId;
        });

        static::updating(function (User $model) {
            $userId = auth()->id();
            $model->updated_by = $userId;
        });

        static::deleting(function (User $user) {
            if ($user->isForceDeleting()) {
                return;
            }

            $userId = auth()->id();
            $user->deleted_by = $userId;
            $user->saveQuietly();
        });
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(related: UserMenu::class, table: 'user_has_menus', foreignPivotKey: 'user_id', relatedPivotKey: 'menu_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'created_by', ownerKey: 'id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'updated_by', ownerKey: 'id');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'deleted_by', ownerKey: 'id');
    }

    
}
