<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome' , 'imagem'];


    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }

    public function rules()
    {
        return [
            'nome' =>'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem'=>'required|file|mimes:png,jpg,jpeg',
        ];
    }

    public function feedback()
    {
        return [
            'required'=>'O campo :attribute é obrigatório',
            'imagem.mimes'=> 'O arquivo devo ser uma imagem',
            'nome.unique'=>'O nome da marca já existe.',
            'nome.min'=>'O nome precisa ter mais de 3 caracteres'
        ];
    }
}
