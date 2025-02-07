<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name'];

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
