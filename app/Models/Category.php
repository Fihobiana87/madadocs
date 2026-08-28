<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function findBySlug(string $slug): ?array
    {
        return self::first('slug', $slug);
    }

    public static function allOrdered(): array
    {
        return self::all('position ASC, name ASC');
    }
}
