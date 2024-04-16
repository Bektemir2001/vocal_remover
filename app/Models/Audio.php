<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    protected $guarded = false;

    const UPLOADED = 0;
    const SEPERATED = 1;
    const FINISHED = 2;

    public static function getStatuses(): array
    {
        return [
            self::UPLOADED => 'UPLOADED',
            self::SEPERATED => 'SEPERATED',
            self::FINISHED => 'FINISHED',
        ];
    }
    public function status(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => self::getStatuses()[$value]
        );
    }

}
