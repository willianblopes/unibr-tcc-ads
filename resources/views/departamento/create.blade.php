@extends('layout/public')
@section('content')
    <div class="component-title" data-intro='Nessa tela você irá cadastrar um novo departamento no sistema.'>
        <h1>Cadastro de departamento</h1>
    </div>

    <div class="component-barra-menu">
        <div class="btn-group pull-right" role="group">
            <a href="#/departamento/help" class="btn btn-default btn-help" data-intro='Clique aqui para ter uma ajuda igual a essa na página.'>Ajuda</a>
            <a href="/departamento/" class="btn btn-default" data-intro='Clique aqui para voltar na listagem de departamentos.'>Listagem</a>
        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row">
        <div class="col-md-12">
        <form class="form-horizontal" action="/departamento" method="POST">
             <div class="form-group">
                <label for="nm_departamento" class="col-md-4 control-label">Nome :</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="nm_departamento" name="nm_departamento" placeholder="Nome da empresa" required/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-offset-4 col-md-6">
                    <button type="submit" class="btn btn-default">Cadastrar</button>
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                </div>

            </div>
        </form>
        </div>
        </div>
    </div>
@stop