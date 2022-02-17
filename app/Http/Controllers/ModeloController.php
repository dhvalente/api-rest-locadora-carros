<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $modeloRepository = new ModeloRepository($this->modelo);

        if ($request->has('atributos_marca')){
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        } else{
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if ($request->has('filtro')){
            $modeloRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')){
            $modeloRepository->selectAtributos($request->atributos);
        }

        return response()->json($modeloRepository->getResultado() , 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate($this->modelo->rules());
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem -> store('imagens/modelos', 'public');

        $modelo = new Modelo();
        $modelo->marca_id = $request->marca_id;
        $modelo->nome = $request->nome;
        $modelo->imagem = $imagem_urn;
        $modelo->numero_portas = $request->numero_portas;
        $modelo->lugares = $request->lugares;
        $modelo->air_bag = $request->air_bag;
        $modelo->abs = $request->abs;
        $modelo->save();
        return response()->json($modelo , 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $modelo = $this->modelo->with('marca')->find($id);
        if ($modelo === null){
            return response()->json(['erro'=>'Recurso pesquisado não existe!'] , 404) ;
        }
        return response()->json($modelo , 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null){
            return response()->json(['erro'=>'Impossível realizar a atualização o recurso solicitado não existe!'] , 404) ;
        }
            if ($request->method() ==='PATCH')
            {

                $regrasDinamicas = array();
                foreach ($modelo->rules() as $input => $regra)
                {
                    if (array_key_exists($input, $request->all()))
                    {
                        $regrasDinamicas[$input] = $regra;
                    }
                }
                $request->validate($regrasDinamicas);

            } else
                {
                    $request->validate($this->modelo->rules());
                }
                if ($request->file('imagem'))
                {
                    Storage::disk('public')->delete($modelo->imagem);
                }
                $imagem = $request->file('imagem');
                $imagem_urn = $imagem -> store('imagens/modelos', 'public');

                $modelo->fill($request->all());
                $modelo ->imagem = $imagem_urn;
                $modelo->save();

                return response()->json($modelo ,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo =$this->modelo->find($id);
        if ($modelo === null){
            return response()->json(['erro'=>'Impossível realizar a exclusão o recurso solicitado não existe!'] , 404) ;
        }

        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json( ['msg'=> 'O modelo foi removida com sucesso!'] , 200);
    }
}
