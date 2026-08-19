<?php
    require_once "includes/banco_ficticio.php";

    // A sessão já é iniciada no index.php, antes de qualquer HTML ser enviado.
    // Não chamar session_start() aqui: nesse ponto já existe saída de HTML
    // (header, nav, etc.), e isso causaria "headers already sent".

    // Inicializa o carrinho na sessão, se ainda não existir
    if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = []; // formato: [ id_produto => quantidade ]
    }

    $produtos = listarProdutos();

    // Remove do carrinho qualquer item cujo produto não existe mais
    // (ex: foi apagado no painel admin depois de já estar no carrinho de alguém).
    // Sem isso, o ID fica "preso" na sessão e continua contando no total do carrinho.
    $ids_existentes = array_column($produtos, 'id');
    foreach (array_keys($_SESSION['carrinho']) as $id_no_carrinho) {
        if (!in_array((int) $id_no_carrinho, $ids_existentes, true)) {
            unset($_SESSION['carrinho'][$id_no_carrinho]);
        }
    }

    // Captura se o usuário clicou em alguma categoria no menu lateral
    $categoria_selecionada = $_GET['cat'] ?? null;

    // Captura o termo digitado na lupa de pesquisa do header
    $busca = trim($_GET['busca'] ?? '');

    // ---------------------------------------------------------------
    // AÇÃO: Adicionar ao carrinho
    // ---------------------------------------------------------------
    // Chega via GET (?add_cart=ID) vindo do botão "Adicionar ao Carrinho".
    // Depois de processar, redirecionamos (POST/Redirect/GET) para a
    // mesma página sem o parâmetro add_cart, evitando duplicar o item
    // se o usuário atualizar a página (F5).
    if (isset($_GET['add_cart'])) {
        $id_para_adicionar = (int) $_GET['add_cart'];

        // Confirma que o produto realmente existe antes de adicionar
        $produto_valido = null;
        foreach ($produtos as $prod) {
            if ((int) $prod['id'] === $id_para_adicionar) {
                $produto_valido = $prod;
                break;
            }
        }

        if ($produto_valido) {
            $estoque_disponivel = (int) ($produto_valido['estoque'] ?? 0);

            // darBaixaEstoque() só desconta e retorna true se ainda houver unidade
            // disponível; assim evitamos adicionar ao carrinho um item esgotado
            // mesmo que o link tenha sido acessado direto pela URL.
            if ($estoque_disponivel > 0 && darBaixaEstoque($id_para_adicionar)) {
                if (!isset($_SESSION['carrinho'][$id_para_adicionar])) {
                    $_SESSION['carrinho'][$id_para_adicionar] = 0;
                }
                $_SESSION['carrinho'][$id_para_adicionar]++;
                $_SESSION['carrinho_msg'] = 'Adicionado: ' . $produto_valido['nome'];
            } else {
                $_SESSION['carrinho_msg'] = 'Produto sem estoque disponível no momento.';
            }
        } else {
            $_SESSION['carrinho_msg'] = 'Produto não encontrado.';
        }

        // Monta a URL de retorno preservando filtros ativos (categoria/busca)
        $params = [];
        if ($categoria_selecionada) $params['cat'] = $categoria_selecionada;
        if ($busca !== '') $params['busca'] = $busca;
        $query = http_build_query(array_merge(['pg' => 'produtos'], $params));

        header('Location: index.php?' . $query);
        exit;
    }

    // Se houver categoria selecionada, filtramos o array original
    if ($categoria_selecionada) {
        $produtos = array_filter($produtos, function($p) use ($categoria_selecionada) {
            return $p['categoria'] === $categoria_selecionada;
        });
    }

    // Se houver termo de busca, filtramos por nome ou descrição (sem diferenciar maiúsculas/minúsculas)
    if ($busca !== '') {
        $produtos = array_filter($produtos, function($p) use ($busca) {
            return stripos($p['nome'], $busca) !== false
                || stripos($p['descricao'], $busca) !== false;
        });
    }

    // Total de itens no carrinho (soma das quantidades) para exibir no badge
    $total_itens_carrinho = array_sum($_SESSION['carrinho']);
?>

<!-- Aviso de item adicionado ao carrinho -->
<?php if (isset($_SESSION['carrinho_msg'])): ?>
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6 bg-green-900/30 border border-green-700/40 rounded-lg px-5 py-4">
        <p class="text-sm text-green-300 flex items-center gap-2">
            <i class="ph-fill ph-check-circle text-green-500"></i>
            <?php echo htmlspecialchars($_SESSION['carrinho_msg']); ?>
        </p>
        <a href="index.php?pg=carrinho" class="text-xs font-black uppercase tracking-widest text-green-400 hover:text-green-300 flex items-center gap-1">
            Ver Carrinho <i class="ph-bold ph-arrow-right"></i>
        </a>
    </div>
    <?php unset($_SESSION['carrinho_msg']); ?>
<?php endif; ?>

<!-- Banner Principal -->
<div class="relative bg-gradient-to-br from-neutral-900 via-neutral-950 to-black rounded-lg p-8 md:p-12 flex flex-col md:flex-row items-center justify-between mb-12 border border-red-900/50 shadow-[0_0_30px_rgba(220,38,38,0.15)] overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-red-600/10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="max-w-md relative z-10">
        <span class="text-[10px] font-black text-white bg-red-600 uppercase tracking-widest px-3 py-1 rounded border border-red-500 shadow-[0_0_10px_rgba(220,38,38,0.4)]">
            Nova Expansão
        </span>
        <h2 class="text-3xl md:text-5xl font-black tracking-tighter text-white uppercase mt-4 mb-2 drop-shadow-lg">
            Domine o <span class="text-red-600">Metagame</span>
        </h2>
        <p class="text-neutral-400 text-sm md:text-base mb-6 font-medium">
            As cartas mais raras e os acessórios definitivos selecionados para você construir o deck perfeito e obliterar seus oponentes.
        </p>
    </div>
    <div class="hidden md:block w-1/3 relative z-10">
        <div class="relative group">
            <div class="absolute -inset-1 bg-red-600 blur opacity-20 group-hover:opacity-40 transition duration-500 rounded-lg"></div>
            <img src="pages/axorus_bgwp.png" alt="Dragão Chaos TCG" class="relative rounded-lg opacity-80 group-hover:opacity-100 transition-opacity duration-500 object-cover aspect-video border border-red-900/50">
        </div>
    </div>
</div>

<!-- Depoimentos de clientes -->
<?php include __DIR__ . '/depoimentos.php'; ?>

<!-- Aviso de busca ativa -->
<?php if ($busca !== ''): ?>
    <div class="flex items-center justify-between flex-wrap gap-3 mb-8 bg-neutral-900/80 border border-red-900/30 rounded-lg px-5 py-4">
        <p class="text-sm text-neutral-300">
            <i class="ph-fill ph-magnifying-glass text-red-600 mr-1"></i>
            Resultados para <strong class="text-white">"<?php echo htmlspecialchars($busca); ?>"</strong>
            <span class="text-neutral-500">(<?php echo count($produtos); ?> encontrado<?php echo count($produtos) != 1 ? 's' : ''; ?>)</span>
        </p>
        <a href="index.php?pg=produtos<?php echo $categoria_selecionada ? '&cat=' . urlencode($categoria_selecionada) : ''; ?>" class="text-xs font-black uppercase tracking-widest text-red-500 hover:text-red-400 flex items-center gap-1">
            <i class="ph-bold ph-x"></i> Limpar busca
        </a>
    </div>
<?php endif; ?>

<div class="flex flex-col md:flex-row gap-8">

    <!-- Menu Lateral de Categorias (Arsenal) -->
    <aside class="w-full md:w-1/4 shrink-0">
        <div class="bg-neutral-900/80 border border-red-900/30 rounded-lg p-6 sticky top-32 shadow-lg backdrop-blur-sm">
            <h3 class="font-black text-white text-lg uppercase tracking-widest mb-6 flex items-center gap-3 border-b border-red-900/50 pb-3">
                <i class="ph-fill ph-sword text-red-600 text-xl"></i> PRODUTOS
            </h3>

            <ul class="space-y-3 text-sm font-bold uppercase tracking-wider text-neutral-400">
                <li>
                    <a href="index.php?pg=produtos&cat=Cole%C3%A7%C3%A3o%20de%20Treinador" class="flex justify-between items-center py-3 px-4 rounded transition-all duration-300 <?php echo $categoria_selecionada == 'Coleção de Treinador' ? 'bg-red-900/20 text-red-500 border-l-4 border-red-600 shadow-[inset_4px_0_0_0_rgba(220,38,38,1)]' : 'border-l-4 border-transparent hover:border-red-900 hover:bg-neutral-800 hover:text-neutral-200'; ?>">
                        <span>Coleção de Treinador</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?pg=produtos&cat=Boosters%20Avulsos" class="flex justify-between items-center py-3 px-4 rounded transition-all duration-300 <?php echo $categoria_selecionada == 'Boosters Avulsos' ? 'bg-red-900/20 text-red-500 border-l-4 border-red-600 shadow-[inset_4px_0_0_0_rgba(220,38,38,1)]' : 'border-l-4 border-transparent hover:border-red-900 hover:bg-neutral-800 hover:text-neutral-200'; ?>">
                        <span>Boosters Avulsos</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?pg=produtos&cat=Caixas%20de%20Boosters" class="flex justify-between items-center py-3 px-4 rounded transition-all duration-300 <?php echo $categoria_selecionada == 'Caixas de Boosters' ? 'bg-red-900/20 text-red-500 border-l-4 border-red-600 shadow-[inset_4px_0_0_0_rgba(220,38,38,1)]' : 'border-l-4 border-transparent hover:border-red-900 hover:bg-neutral-800 hover:text-neutral-200'; ?>">
                        <span>Caixas de Boosters</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?pg=produtos&cat=Latas" class="flex justify-between items-center py-3 px-4 rounded transition-all duration-300 <?php echo $categoria_selecionada == 'Latas' ? 'bg-red-900/20 text-red-500 border-l-4 border-red-600 shadow-[inset_4px_0_0_0_rgba(220,38,38,1)]' : 'border-l-4 border-transparent hover:border-red-900 hover:bg-neutral-800 hover:text-neutral-200'; ?>">
                        <span>Latas</span>
                    </a>
                </li>
            </ul>

            <!-- Indicador do carrinho: leva para a página do carrinho -->
            <a href="index.php?pg=carrinho" class="mt-6 flex items-center justify-between bg-neutral-800 hover:bg-red-900/30 border border-neutral-700 hover:border-red-600/50 rounded-lg px-4 py-3 transition-all duration-300">
                <span class="flex items-center gap-2 text-white text-xs uppercase tracking-widest font-black">
                    <i class="ph-fill ph-shopping-cart-simple text-red-500"></i> Carrinho
                </span>
                <span class="bg-red-600 text-white text-xs font-black rounded-full w-6 h-6 flex items-center justify-center">
                    <?php echo (int) $total_itens_carrinho; ?>
                </span>
            </a>
        </div>
    </aside>

    <!-- Lista de Produtos -->
    <section class="w-full md:w-3/4">

        <?php if (empty($produtos)): ?>
            <div class="flex flex-col items-center justify-center text-center py-20 bg-neutral-900/50 rounded-lg border border-red-900/20">
                <i class="ph ph-ghost text-6xl text-neutral-700 mb-4"></i>
                <h3 class="text-xl font-black text-white uppercase tracking-widest mb-2">O Vazio Absoluto</h3>
                <p class="text-neutral-500">Nenhuma relíquia ou carta foi encontrada nesta dimensão.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($produtos as $p): ?>
                    <?php $estoque_produto = (int) ($p['estoque'] ?? 0); ?>
                    <!-- Card de Produto -->
                    <div class="bg-neutral-900 border border-neutral-800 rounded-lg overflow-hidden group hover:shadow-[0_0_20px_rgba(220,38,38,0.2)] hover:border-red-600/50 transition-all duration-500 flex flex-col h-full relative">
                        <div class="absolute top-0 left-0 w-full h-1 bg-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left z-10"></div>

                        <div class="relative overflow-hidden bg-black aspect-[4/3]">
                            <img src="<?php echo $p['imagem']; ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>" class="w-full h-full object-cover <?php echo $estoque_produto > 0 ? 'opacity-70 group-hover:opacity-100 group-hover:scale-110' : 'opacity-30 grayscale'; ?> transition duration-700">

                            <span class="absolute top-3 left-3 bg-black/80 backdrop-blur text-red-500 border border-red-900/50 text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded shadow-md">
                                <?php echo htmlspecialchars($p['categoria']); ?>
                            </span>

                            <?php if ($estoque_produto <= 0): ?>
                                <span class="absolute top-3 right-3 bg-neutral-950/90 text-red-500 border border-red-700/50 text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded shadow-md flex items-center gap-1">
                                    <i class="ph-fill ph-x-circle"></i> Esgotado
                                </span>
                            <?php elseif (isset($_SESSION['carrinho'][$p['id']])): ?>
                                <span class="absolute top-3 right-3 bg-green-600/90 text-white text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded shadow-md flex items-center gap-1">
                                    <i class="ph-fill ph-check"></i> <?php echo (int) $_SESSION['carrinho'][$p['id']]; ?> no carrinho
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="p-6 flex flex-col flex-grow relative z-10 bg-gradient-to-t from-neutral-950 to-neutral-900">
                            <h3 class="font-black text-white group-hover:text-red-500 transition-colors text-lg uppercase tracking-wide line-clamp-1">
                                <?php echo htmlspecialchars($p['nome']); ?>
                            </h3>
                            <p class="text-neutral-400 text-sm mt-2 line-clamp-2 leading-relaxed font-medium">
                                <?php echo htmlspecialchars($p['descricao']); ?>
                            </p>

                            <div class="mt-6 pt-5 border-t border-neutral-800 flex items-center justify-between mt-auto">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-neutral-500 font-bold uppercase tracking-widest">Valor</span>
                                    <span class="text-xl font-black text-white group-hover:text-red-500 transition-colors">
                                        R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?>
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="index.php?pg=detalhe&id=<?php echo $p['id']; ?>" title="Ver detalhes" class="bg-neutral-800 hover:bg-neutral-700 border border-neutral-700 text-white text-[11px] uppercase tracking-widest font-black px-3 py-3 rounded transition-all duration-300 shadow-md flex items-center gap-1 group/btn">
                                        <i class="ph-bold ph-caret-right group-hover/btn:translate-x-1 transition-transform"></i>
                                    </a>

                                    <?php if ($estoque_produto > 0): ?>
                                        <a href="index.php?pg=produtos&add_cart=<?php echo $p['id']; ?><?php echo $categoria_selecionada ? '&cat=' . urlencode($categoria_selecionada) : ''; ?><?php echo $busca !== '' ? '&busca=' . urlencode($busca) : ''; ?>"
                                           title="Adicionar ao carrinho"
                                           class="bg-neutral-800 hover:bg-red-900/30 border border-neutral-700 hover:border-red-600/50 text-white text-[11px] uppercase tracking-widest font-black px-3 py-3 rounded transition-all duration-300 shadow-md flex items-center gap-1">
                                            <i class="ph-fill ph-shopping-cart-simple"></i>
                                        </a>

                                        <a href="index.php?pg=finalizar&id=<?php echo $p['id']; ?>" class="bg-red-600 hover:bg-red-500 border border-red-500 text-white text-[11px] uppercase tracking-widest font-black px-4 py-3 rounded transition-all duration-300 shadow-[0_0_10px_rgba(220,38,38,0.3)] flex items-center gap-2">
                                            <i class="ph-fill ph-lightning"></i>
                                            Comprar Agora
                                        </a>
                                    <?php else: ?>
                                        <span title="Produto esgotado" class="bg-neutral-800/50 border border-neutral-800 text-neutral-600 text-[11px] uppercase tracking-widest font-black px-4 py-3 rounded flex items-center gap-2 cursor-not-allowed select-none">
                                            <i class="ph-fill ph-x-circle"></i>
                                            Esgotado
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>
</div>