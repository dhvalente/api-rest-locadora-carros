<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = ['id' , 'nome'];

    public function rules()
    {
        return [
            'nome' =>'required',
        ];
    }


}
