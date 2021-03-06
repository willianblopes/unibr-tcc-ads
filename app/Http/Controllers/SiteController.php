<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    private $nameFolder = "site";

    /**
     * Mostra a página inicial do site
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $produtos = (array) Produto::all();
        $produtos = array_chunk($produtos, 3);
        #var_dump($produtos[0][0]);
        #exit;
        shuffle($produtos[0][0]);
        return view("{$this->nameFolder}/index", ["listaProdutos"=>$produtos[0][0]]);
    }
    
    /**
     * Mostra a página de contato do site
     * @return \Illuminate\Http\Response
     */
    public function contato(Request $request)
    {
        return view("{$this->nameFolder}/contato");
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function contatoStore(Request $request)
    {
        // Valida
        $this->validate($request, [
            'nm_contato' => 'required',
            'nm_email_contato' => 'required',
            'ds_contato' => 'required',
        ]);
        
        // Adiciona e salva
        $contato = new Contato();
        $contato->nm_contato = $request->nm_contato;
        $contato->nm_email_contato = $request->nm_email_contato;
        $contato->ds_contato = $request->ds_contato;
        $contato->dt_contato = date('Y-m-d H:i:s');
        $contato->save();
        
        // Redireciona
        return redirect('entrar-contato')->with('message', 'Contato salvo com sucesso!');
    }
    
    /**
     * Mostra as páginas de noticias do site
     * @return \Illuminate\Http\Response
     */
    public function noticia1(Request $request)
    {
        return view("{$this->nameFolder}/noticia-1");
    }
    public function noticia2(Request $request)
    {
        return view("{$this->nameFolder}/noticia-2");
    }
    
    
    /**
     * Mostra a página de logon do site
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        return view("{$this->nameFolder}/login");
    }

    
    /**
     * Mostra a página de logon do site
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->session()->forget('client_on');
        $request->session()->forget('client_name');
        $request->session()->forget('client_email');
        return redirect('/')->with('message', 'Usuário deslogado com sucesso!');
    }
    
    /**
     * Valida se um usuário existe no banco de dados
     * e loga ele no sistema
     * @return \Illuminate\Http\Response
     */
    public function autenticarStore(Request $request)
    {
        $pessoa = Pessoa::where('nm_email', $request->email)
                        ->where('nm_senha', $request->senha)
                        ->first();
        
        // Tem um usuário cadastrado
        if ($pessoa) {
            $request->session()->put('client_on', $pessoa->cd_pessoa);
            $request->session()->put('client_name', $pessoa->nm_pessoa);
            $request->session()->put('client_email', $pessoa->nm_email);
            return redirect('/orcamento')->with('message', 'Usuário logado com sucesso!');
        }

        return back()->with('message', 'Usuário ou senha inválidos');
    }
    
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cadastrarStore(Request $request)
    {
        // Valida
        $this->validate($request, [
            'nome' => 'required',
            'email' => 'required',
            'senha' => 'required',
        ]);
        
        // Adiciona e salva
        $pessoa = new Pessoa();
        $pessoa->nm_pessoa = $request->nome;
        $pessoa->nm_razao_social = $request->nome;
        $pessoa->nm_email = $request->email;
        $pessoa->nm_senha = $request->senha;
        $pessoa->nm_situacao_cadastral = 'ativo';
        $pessoa->save();
        
        // Redireciona
        $request->session()->put('client_on', $pessoa->cd_pessoa);
        $request->session()->put('client_name', $pessoa->nm_pessoa);
        $request->session()->put('client_email', $pessoa->nm_email);
        return redirect('/orcamento')->with('message', 'Usuário logado com sucesso!');
    }
    
    /**
     * Mostra a página inicial do site
     * @return \Illuminate\Http\Response
     */
    public function orcamento(Request $request)
    {
        $cliente = $request->session()->get('client_on');
        $contratos = Contrato::where('cd_pessoa', '=', $cliente)->orderBy('cd_contrato','desc')->get();
        return view("{$this->nameFolder}/orcamentoListagem", ["listaContratos"=>$contratos]);
    }
    
    /**
     * Mostra a página inicial do site
     * @return \Illuminate\Http\Response
     */
    public function orcamentoCadastrar(Request $request)
    {
        $produtos = Produto::all();
        return view("{$this->nameFolder}/orcamento", ["listaProdutos"=>$produtos]);
    }
    
    /**
     * Todo: 
     * Tem sessão
     *  Cadastrar a sessão
     * Cadastra o produto
     * @return \Illuminate\Http\Response
     */
    public function cotar(Request $request)
    {
        $orcamento = $request->session()->get('orcamento');
        
        if(!is_array($orcamento)){
            $orcamento = array();
        }
        
        if(!isset($orcamento[$request->produto])){
            $orcamento[$request->produto] = 0;
        }
        
        $orcamento[$request->produto]++;
        
        $request->session()->put('orcamento', $orcamento);
        return redirect('/orcamento/listagem');
    }
    
    /**
     * Todo: 
     * Tem sessão
     *  Cadastrar a sessão
     * Remove o produto
     * @return \Illuminate\Http\Response
     */
    public function remover(Request $request)
    {
        $orcamento = $request->session()->get('orcamento');
        
        if(!is_array($orcamento)){
            $orcamento = array();
        }
        
        if(!isset($orcamento[$request->produto])){
            $orcamento[$request->produto] = 0;
        }
        
        $orcamento[$request->produto]--;
        
        if($orcamento[$request->produto] <= 0){
            unset($orcamento[$request->produto]);
        }
        
        $request->session()->put('orcamento', $orcamento);
        return redirect('/orcamento/listagem');
    }
    
    /**
     * Mostra a página inicial do site
     * @return \Illuminate\Http\Response
     */
    public function orcamentoListagem(Request $request)
    {
        
        $orcamento = $request->session()->get('orcamento');
        $produtos = Produto::find(array_keys($orcamento));
        
        foreach($produtos as &$produto){
            $produto->qt_produto = $orcamento[$produto->cd_produto];
            $produto->vl_total = $orcamento[$produto->cd_produto]*$produto->vl_produto;
        }
        
        return view("{$this->nameFolder}/listagem", ["listaProdutos"=>$produtos]);
    }
    
    /**
     * Mostra a página inicial do site
     * @return \Illuminate\Http\Response
     */
    public function orcamentoSalvar(Request $request)
    {
        
        $orcamento = $request->session()->get('orcamento');
        $produtos = Produto::find(array_keys($orcamento));
        $valorTotal = 0.0;
        
        foreach($produtos as &$produto){
            $produto->qt_produto = $orcamento[$produto->cd_produto];
            $produto->vl_total = $orcamento[$produto->cd_produto]*$produto->vl_produto;
            $valorTotal += $produto->vl_total;
        }
        
        // Adiciona e salva
        $contrato = new Contrato();
        $contrato->vl_contrato = $valorTotal;
        $contrato->vl_desconto = 0;
        $contrato->qt_itens_contrato = $produto->qt_produto;
        $contrato->cd_parcela_atual = 1;
        $contrato->cd_parcela_total = 1;
        $contrato->ic_tipo_compra_venda = 'compra';
        $contrato->ic_situacao_aprovado_reprovado = 'aguardando';
        $contrato->cd_pessoa = $request->session()->get('client_on');
        $contrato->dt_contrato = date('Y-m-d H:i:s');
        $contrato->save();

        foreach($produtos as &$produto){

            $produtoContrato = new ContratoProduto();
            $produtoContrato->cd_contrato = $contrato->cd_contrato;
            $produtoContrato->cd_produto = $produto->cd_produto;
            $produtoContrato->vl_produto = $produto->vl_produto;
            $produtoContrato->qt_produto = $orcamento[$produto->cd_produto];
            $produtoContrato->save();
        }
        
        $request->session()->forget('orcamento');
        
        return redirect('/orcamento')->with('message', 'Em breve entraremos em contato com você para apresentar um orçamento definitivo!');
    }
    
    
    /**
     * Mostra a página inicial do meus chamados
     * @return \Illuminate\Http\Response
     */
    public function ordemservico(Request $request)
    {
        $cliente = $request->session()->get('client_on');
        $contratos = [];
        foreach (Contrato::where('cd_pessoa', '=', $cliente)->get() as $contrato) {
            $contratos[] = (int) $contrato->cd_contrato;
        }
        
        $chamados = Chamado::whereIn('cd_contrato', $contratos)->orderBy('cd_chamado','desc')->get();
        return view("{$this->nameFolder}/ordemservicoListagem", ["listaChamados"=>$chamados]);
    }
    
    
    /**
     * Mostra a página de cadastro de uma ordem de serviço
     * @return \Illuminate\Http\Response
     */
    public function ordemservicoCadastrar(Request $request)
    {
        return view("{$this->nameFolder}/ordemservicoCadastro");
    }

    /**
     * Salva uma nova ordem de serviço
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ordemservicoStore(Request $request)
    {
        // Valida
        $this->validate($request, 
        [
            'descricao' => 'required',
        ]);

        $profissional = Usuario::orderByRaw("RAND()")->first();

        // Adiciona e salva
        $chamado = new Chamado();
        $chamado->ds_chamado = $request->descricao;
        $chamado->nm_localizacao = $request->localizacao;
        $chamado->nm_gps = $request->gps;
        $chamado->dt_abertura_chamado = date('Y-m-d');
        $chamado->dt_fechamento_chamado = date('Y-m-d', strtotime('+1 week'));
        $chamado->cd_contrato = 5;
        $chamado->cd_usuario_autor = $profissional->cd_usuario;
        $chamado->cd_usuario_responsavel = $profissional->cd_usuario;
        $chamado->save();

        return redirect('/ordemservico')->with('message', 'Chamado aberto com sucesso, aguarde contato com nossa equipe de atendimento!');
    }
}
