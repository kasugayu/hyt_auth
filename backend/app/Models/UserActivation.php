<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

define('ACTIVATE_SALT', '[秘密鍵]');

class UserActivation extends Model
{
    use HasFactory;

    const EXPIRE_HOUR = 2;

    protected $fillable = [
        'email',
        'verification_code',
        'expired',
    ];

    //
    public static function validateVerificationCode(string $email, string $verification_code)
    {
        $result = self::query()
            ->where('email', $email)
            ->where('verification_code', $verification_code)
            ->doesntExist();

        if ($result) {
            throw ValidationException::withMessages([
                'verification_code' => ['認証コードが不正です'],
            ]);
        }

        return [];
    }

    public static function GenerateActivationCode()
    {
        return str_pad(random_int(0,9999), 4, 0, STR_PAD_LEFT);
    }
}
