<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ListingSave extends Pivot
{
    protected $table = 'listing_saves';
    protected $primaryKey = 'save_id';
    public $incrementing = true;
    public const CREATED_AT = 'save_created_at';
    public const UPDATED_AT = 'save_updated_at';

    public function getCreatedAtColumn()
    {
        return self::CREATED_AT;
    }

    public function getUpdatedAtColumn()
    {
        return self::UPDATED_AT;
    }
}
