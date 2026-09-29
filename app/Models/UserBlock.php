<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserBlock extends Pivot
{
    protected $table = 'user_blocks';
    protected $primaryKey = 'block_id';
    public $incrementing = true;
    public const CREATED_AT = 'block_created_at';
    public const UPDATED_AT = 'block_updated_at';

    public function getCreatedAtColumn()
    {
        return self::CREATED_AT;
    }

    public function getUpdatedAtColumn()
    {
        return self::UPDATED_AT;
    }
}
