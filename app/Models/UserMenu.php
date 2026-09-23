<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperUserMenu
 */
class UserMenu extends Model
{
    public function Users(): BelongsToMany {
        return $this->belongsToMany(related: User::class, table: 'user_has_menus', foreignPivotKey: 'menu_id', relatedPivotKey: 'user_id');
    }
}
