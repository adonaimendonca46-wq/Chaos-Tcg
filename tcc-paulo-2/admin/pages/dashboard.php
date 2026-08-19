<?php
/**
 * admin/pages/dashboard.php
 *
 * Painel inicial do admin. Lê os dados direto de data/produtos.json e
 * data/fornecedores.json (mesma pasta "data" da raiz do projeto) e monta
 * cards com números reais — nada aqui é inventado/fixo.
 *
 * Esse arquivo é incluído dentro de admin/index.php, então ele NÃO deve
 * ter <html>, <head> ou <body> — só o conteúdo que entra dentro da <main>.
 */

// __DIR__ aqui é admin/pages, então subimos 2 níveis pra chegar em tcc-paulo/,
// e daí entramos em data/. Isso funciona não importa de onde o PHP é chamado.
$caminho_produtos    = __DIR__ . '/../../data/produtos.json';
$caminho_fornecedores = __DIR__ . '/../../data/fornecedores.json';
$caminho_categorias  = __DIR__ . '/../../data/categorias.json';

function lerJson(string $caminho): array
{
    if (!file_exists($caminho)) {
        return [];
    }
    $conteudo = file_get_contents($caminho);
    $dados = json_decode($conteudo, true);
    return is_array($dados) ? $dados : [];
}

$produtos     = lerJson($caminho_produtos);
$fornecedores = lerJson($caminho_fornecedores);
$categorias   = lerJson($caminho_categorias);

// ------------------------------------------------------------
// Cálculos
// ------------------------------------------------------------
$total_produtos     = count($produtos);
$total_fornecedores = count($fornecedores);

$valor_total_catalogo = 0;
$produtos_por_categoria = []; // ex: ['Boosters Avulsos' => 3, 'Caixas de Boosters' => 3]

$produto_mais_caro = null;
$produto_mais_barato = null;

foreach ($produtos as $p) {
    $preco = (float) ($p['preco'] ?? 0);
    $valor_total_catalogo += $preco;

    $cat = $p['categoria'] ?? 'Sem categoria';
    if (!isset($produtos_por_categoria[$cat])) {
        $produtos_por_categoria[$cat] = 0;
    }
    $produtos_por_categoria[$cat]++;

    if ($produto_mais_caro === null || $preco > $produto_mais_caro['preco']) {
        $produto_mais_caro = $p;
    }
    if ($produto_mais_barato === null || $preco < $produto_mais_barato['preco']) {
        $produto_mais_barato = $p;
    }
}

// Ordena categorias da mais cheia pra menos cheia, pra virar um mini-gráfico de barras
arsort($produtos_por_categoria);
$maior_quantidade_categoria = !empty($produtos_por_categoria) ? max($produtos_por_categoria) : 1;

function formatarReal(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
?>

<div class="mb-10">
    <h1 class="text-3xl font-black tracking-tighter text-white uppercase mb-1">Dashboard</h1>
    <p class="text-zinc-500 text-sm">Visão geral da loja, atualizada em tempo real a partir dos seus dados.</p>
</div>

<!-- Cards de resumo -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">

    <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6 hover:border-red-900/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-[11px] font-bold uppercase tracking-widest text-zinc-500">Produtos</span>
            <i class="ph-fill ph-package text-red-600 text-xl"></i>
        </div>
        <p class="text-3xl font-black text-white"><?php echo $total_produtos; ?></p>
        <p class="text-zinc-600 text-xs mt-1">cadastrados no catálogo</p>
    </div>

    <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6 hover:border-red-900/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-[11px] font-bold uppercase tracking-widest text-zinc-500">Categorias</span>
            <i class="ph-fill ph-tag text-red-600 text-xl"></i>
        </div>
        <p class="text-3xl font-black text-white"><?php echo count($produtos_por_categoria); ?></p>
        <p class="text-zinc-600 text-xs mt-1">categorias em uso</p>
    </div>

    <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6 hover:border-red-900/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-[11px] font-bold uppercase tracking-widest text-zinc-500">Fornecedores</span>
            <i class="ph-fill ph-users-three text-red-600 text-xl"></i>
        </div>
        <p class="text-3xl font-black text-white"><?php echo $total_fornecedores; ?></p>
        <p class="text-zinc-600 text-xs mt-1">cadastrados</p>
    </div>

    <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6 hover:border-red-900/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-[11px] font-bold uppercase tracking-widest text-zinc-500">Valor do Catálogo</span>
            <i class="ph-fill ph-currency-circle-dollar text-red-600 text-xl"></i>
        </div>
        <p class="text-2xl font-black text-white"><?php echo formatarReal($valor_total_catalogo); ?></p>
        <p class="text-zinc-600 text-xs mt-1">soma de todos os preços</p>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Produtos por categoria (mini gráfico de barras) -->
    <div class="lg:col-span-2 bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
            <i class="ph-fill ph-chart-bar text-red-600"></i> Produtos por Categoria
        </h3>

        <?php if (empty($produtos_por_categoria)): ?>
            <p class="text-zinc-600 text-sm">Nenhum produto cadastrado ainda.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($produtos_por_categoria as $categoria => $quantidade): ?>
                    <?php $porcentagem = round(($quantidade / $maior_quantidade_categoria) * 100); ?>
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-zinc-300 font-bold"><?php echo htmlspecialchars($categoria); ?></span>
                            <span class="text-zinc-500"><?php echo $quantidade; ?> produto<?php echo $quantidade > 1 ? 's' : ''; ?></span>
                        </div>
                        <div class="w-full bg-zinc-900 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-red-600 h-2.5 rounded-full transition-all" style="width: <?php echo $porcentagem; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Destaques -->
    <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
            <i class="ph-fill ph-star text-red-600"></i> Destaques
        </h3>

        <?php if ($produto_mais_caro): ?>
            <div class="mb-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-600 mb-1">Produto mais caro</p>
                <p class="text-white text-sm font-bold leading-snug"><?php echo htmlspecialchars($produto_mais_caro['nome']); ?></p>
                <p class="text-red-500 font-black text-sm"><?php echo formatarReal((float) $produto_mais_caro['preco']); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($produto_mais_barato): ?>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-600 mb-1">Produto mais barato</p>
                <p class="text-white text-sm font-bold leading-snug"><?php echo htmlspecialchars($produto_mais_barato['nome']); ?></p>
                <p class="text-red-500 font-black text-sm"><?php echo formatarReal((float) $produto_mais_barato['preco']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!$produto_mais_caro && !$produto_mais_barato): ?>
            <p class="text-zinc-600 text-sm">Cadastre produtos para ver destaques aqui.</p>
        <?php endif; ?>
    </div>

</div>

<!-- Atalhos rápidos -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mt-10">
    <a href="index.php?pg=produtos" class="bg-zinc-950 border border-zinc-800 hover:border-red-600 rounded-2xl p-6 flex items-center gap-4 transition-colors group">
        <i class="ph-fill ph-plus-circle text-2xl text-red-600 group-hover:scale-110 transition-transform"></i>
        <div>
            <p class="text-white font-bold text-sm">Novo Produto</p>
            <p class="text-zinc-600 text-xs">Cadastrar item no catálogo</p>
        </div>
    </a>
    <a href="index.php?pg=fornecedores" class="bg-zinc-950 border border-zinc-800 hover:border-red-600 rounded-2xl p-6 flex items-center gap-4 transition-colors group">
        <i class="ph-fill ph-users-three text-2xl text-red-600 group-hover:scale-110 transition-transform"></i>
        <div>
            <p class="text-white font-bold text-sm">Fornecedores</p>
            <p class="text-zinc-600 text-xs">Ver e gerenciar cadastros</p>
        </div>
    </a>
    <a href="index.php?pg=categorias" class="bg-zinc-950 border border-zinc-800 hover:border-red-600 rounded-2xl p-6 flex items-center gap-4 transition-colors group">
        <i class="ph-fill ph-tag text-2xl text-red-600 group-hover:scale-110 transition-transform"></i>
        <div>
            <p class="text-white font-bold text-sm">Categorias</p>
            <p class="text-zinc-600 text-xs">Organizar o catálogo</p>
        </div>
    </a>
</div>