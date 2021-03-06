<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

class Chamado extends Model
{
    // Nome da tabela
    protected $table = 'chamado';
    
    // Chave primário
    protected $primaryKey = 'cd_chamado';
    
    // Removendo os campos de tempo
    public $timestamps = false;
    
    /**
     * Relacionamento hasOne
     * Contrato
     */
    public function contrato(){
        return $this->belongsTo('App\Http\Controllers\Contrato', 'cd_contrato', 'cd_contrato');
    }

    /**
     * Relacionamento hasOne
     * Autor
     */
    public function autor(){
        return $this->belongsTo('App\Http\Controllers\Usuario', 'cd_usuario_autor', 'cd_usuario');
    }

    /**
     * Relacionamento hasOne
     * Responsavel
     */
    public function responsavel(){
        return $this->belongsTo('App\Http\Controllers\Usuario', 'cd_usuario_responsavel', 'cd_usuario');
    }
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'dt_abertura_chamado',
        'dt_fechamento_chamado',
    ];
}
