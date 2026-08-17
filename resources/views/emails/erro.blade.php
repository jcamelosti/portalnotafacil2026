<h2>Erro no sistema</h2>

<p><strong>Mensagem:</strong> {{ $dados['mensagem'] }}</p>
<p><strong>Arquivo:</strong> {{ $dados['arquivo'] }}</p>
<p><strong>Linha:</strong> {{ $dados['linha'] }}</p>
<p><strong>URL:</strong> {{ $dados['url'] }}</p>
<p><strong>Método:</strong> {{ $dados['metodo'] }}</p>
<p><strong>IP:</strong> {{ $dados['ip'] }}</p>

@if($dados['user'])
    <h3>Usuário</h3>
    <p>ID: {{ $dados['user']['id'] }}</p>
    <p>Nome: {{ $dados['user']['nome'] }}</p>
    <p>Email: {{ $dados['user']['email'] }}</p>
@endif

<pre>{{ $dados['trace'] }}</pre>