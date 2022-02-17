<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Type\Integer;
use function Symfony\Component\Translation\t;

class MarcaController extends Controller
{
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);

        if ($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else{
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')){
           $marcaRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')){
            $marcaRepository->selectAtributos($request->atributos);
        }

        return response()->json($marcaRepository->getResultado() , 200);
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
        $request->validate($this->marca->rules(), $this->marca->feedback());
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem -> store('imagens', 'public');

        $marca = new Marca();
        $marca->nome = $request->nome;
        $marca->imagem = $imagem_urn;
        $marca->save();
        return response()->json($marca , 201);

        /*ou pode ser feito pelo método create
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);*/
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $marca = $this->marca->with('modelos')->find($id);
        if ($marca === null){
            return response()->json(['erro'=>'Recurso pesquisado não existe!'] , 404) ;
        }
        return response()->json($marca , 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null){
            return response()->json(['erro'=>'Impossível realizar a atualização o recurso solicitado não existe!'] , 404) ;
        }
            if ($request->method() ==='PATCH'){

                $regrasDinamicas = array();

                foreach ($marca->rules() as $input => $regra){

                    if (array_key_exists($input, $request->all())){
                        $regrasDinamicas[$input] = $regra;
                    }
                }

                $request->validate($regrasDinamicas, $this->marca->feedback());

            } else {

                $request->validate($this->marca->rules(), $this->marca->feedback());
            }
            if ($request->file('imagem')){
                Storage::disk('public')->delete($marca->imagem);
            }
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem -> store('imagens', 'public');

            $marca->fill($request->all());
            $marca ->imagem = $imagem_urn;
            $marca->save();

            return response()->json($marca ,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca =$this->marca->find($id);
        if ($marca === null){
            return response()->json(['erro'=>'Impossível realizar a exclusão o recurso solicitado não existe!'] , 404) ;
        }

        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json( ['msg'=> 'A marca foi removida com sucesso!'] , 200);
    }
}
