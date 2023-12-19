<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'favourites';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_user',
        'id_content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function content()
    {
        return $this->belongsTo(Contents::class, 'id_content');
    }

}
