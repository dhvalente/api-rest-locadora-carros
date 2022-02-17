<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['marca_id','nome' , 'imagem', 'numero_portas', 'lugares', 'air_bar', 'abs'];

    public function marca()
    {
        return $this->belongsTo(Marca::class , 'marca_id');
    }

    public function rules()
    {
        return [
            'marca_id'=>'exists:marcas,id',
            'nome' =>'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem'=>'required|file|mimes:png,jpg,jpeg',
            'numero_portas'=>'required|integer|digits_between:1,5',
            'lugares'=>'required|integer|digits_between:1,20',
            'air_bag'=>'required|boolean',
            'abs'=>'required|boolean',
        ];
    }
}
