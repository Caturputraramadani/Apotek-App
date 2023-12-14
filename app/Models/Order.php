<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'medicines' => 'array'
    ];

    public function user()
    {
        // menghubungkan ke primary key nya
        // dalam kurung merupakan nama model tempat penyimpanan dari PK nya si Fk ada di model ini
        return $this->belongsTo(User::class);
    }
}
