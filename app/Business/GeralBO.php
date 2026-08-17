<?php

namespace App\Business;

use App\Util\Constantes;

abstract class GeralBO
{

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function slug($slug)
    {
        $v = $this->model->whereSlug($slug)->first();
        return $v ?? abort(404);
    }

    public function listaSemData()
    {
        $dados = request()->all();
        unset($dados['page']);
        return $this->model->filtroGeral($dados);
    }

    public function listaSemDataPaginada($with = [],$pag = 20,$order = ['id','asc'])
    {
        return $this->listaSemData()
            ->with($with)
            ->orderBy($order[0],$order[1])
            ->paginate($pag)
            ->appends(request()->except('page'));
    }

    public function saveOrUpdate($request, $id = null){
        $data = $request->all();
        $save = null;
        $this->model->beginTransaction();
        try{
            if($id != null){
                $save = $this->model->findOrFail($id);
                $save->update($data);
            }else{
                //validações aqui
                $save = $this->model->create($data);
            }
            $this->model->commit();
            $this->msg($save,$id);
        }catch(\QueryException $e){
            $this->erroCatch($e);
        }catch(\Exception $e){
            $this->erroCatch($e);
        }

        return $save;
    }

    public function erroCatch($erro)
    {
        $this->model->rollback();
        //usar dd para pegar erro
        dd($erro);
        flash()->error('Ocorreu um erro ao salvar os dados, contate o Administrador do sistema');
        return redirect()->back()->withInput()->send();
    }

    public function msg($save,$id)
    {
        if($save && $id){
            flash()->message(Constantes::REGISTRO_ALTERADO_SUCESSO)->success();
        } elseif($save){
            flash()->message(Constantes::REGISTRO_INCLUIDO_SUCESSO)->success();
        }
    }

    public function delete($id)
    {
        $del = $this->model->findOrFail($id);
        if($del){
            $del = $del->delete();
            flash()->message(Constantes::REGISTRO_EXCLUIDO_SUCESSO)->success();
            return $del;
        }

        return null;
    }

}