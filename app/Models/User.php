<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 * @mixin Builder
 */
class User extends Model {

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    public function setPassword($password) {
        $this->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
}