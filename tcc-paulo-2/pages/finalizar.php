<?php
    require_once "includes/banco_ficticio.php";

    // Garante que a sessão está ativa (o carrinho vive na sessão)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    $produtos = listarProdutos();

    // Captura o id do produto vindo da URL (ex: index.php?pg=finalizar&id=3)
    // Se NÃO vier id, entendemos que o usuário clicou no ícone do carrinho
    // e quer finalizar TODOS os itens que estão no carrinho da sessão.
    $id_produto = $_GET['id'] ?? null;
    $modo_carrinho = ($id_produto === null);

    // ---------------------------------------------------------------
    // MODO PRODUTO ÚNICO ("Comprar Agora")
    // ---------------------------------------------------------------
    $produto = null;
    if (!$modo_carrinho) {
        foreach ($produtos as $p) {
            if ($p['id'] == $id_produto) {
                $produto = $p;
                break;
            }
        }

        // Se o produto não existir, mostramos aviso e paramos por aqui
        if (!$produto):
    ?>
        <div class="flex flex-col items-center justify-center text-center py-20 bg-neutral-900/50 rounded-lg border border-red-900/20">
            <i class="ph ph-ghost text-6xl text-neutral-700 mb-4"></i>
            <h3 class="text-xl font-black text-white uppercase tracking-widest mb-2">Produto Não Encontrado</h3>
            <p class="text-neutral-500 mb-6">Esse item pode ter sido removido ou o link está incorreto.</p>
            <a href="index.php?pg=produtos" class="bg-red-600 text-white px-8 py-3 rounded uppercase font-bold tracking-widest hover:bg-red-700 transition-colors shadow-[0_0_15px_rgba(220,38,38,0.3)]">
                Voltar aos Produtos
            </a>
        </div>
    <?php
        return;
        endif;
    }

    // ---------------------------------------------------------------
    // MODO CARRINHO (clique no ícone do carrinho)
    // ---------------------------------------------------------------
    // Permite remover um item do carrinho antes de finalizar (?remove_cart=ID)
    if ($modo_carrinho && isset($_GET['remove_cart'])) {
        $id_remover = (int) $_GET['remove_cart'];
        unset($_SESSION['carrinho'][$id_remover]);
        header('Location: index.php?pg=finalizar');
        exit;
    }

    $itens_carrinho = [];
    $total_carrinho = 0;

    if ($modo_carrinho) {
        foreach ($_SESSION['carrinho'] as $id_item => $quantidade) {
            foreach ($produtos as $p) {
                if ((int) $p['id'] === (int) $id_item) {
                    $subtotal = $p['preco'] * $quantidade;
                    $itens_carrinho[] = [
                        'produto'   => $p,
                        'quantidade'=> $quantidade,
                        'subtotal'  => $subtotal,
                    ];
                    $total_carrinho += $subtotal;
                    break;
                }
            }
        }

        // Carrinho vazio: nada para finalizar
        if (empty($itens_carrinho)):
    ?>
        <div class="flex flex-col items-center justify-center text-center py-20 bg-neutral-900/50 rounded-lg border border-red-900/20">
            <i class="ph ph-shopping-cart-simple text-6xl text-neutral-700 mb-4"></i>
            <h3 class="text-xl font-black text-white uppercase tracking-widest mb-2">Seu Carrinho Está Vazio</h3>
            <p class="text-neutral-500 mb-6">Adicione produtos ao carrinho antes de finalizar a compra.</p>
            <a href="index.php?pg=produtos" class="bg-red-600 text-white px-8 py-3 rounded uppercase font-bold tracking-widest hover:bg-red-700 transition-colors shadow-[0_0_15px_rgba(220,38,38,0.3)]">
                Ver Produtos
            </a>
        </div>
    <?php
        return;
        endif;
    }

    // Valor total a cobrar, seja produto único ou carrinho inteiro
    $valor_total = $modo_carrinho ? $total_carrinho : $produto['preco'];

    // URL de ação do formulário, preservando o modo atual
    $form_action = $modo_carrinho
        ? 'index.php?pg=finalizar'
        : 'index.php?pg=finalizar&id=' . $produto['id'];

    // Processamento do pedido quando o form é enviado (POST) — é aqui,
    // ao clicar em "Confirmar Pedido", que o estoque é efetivamente baixado.
    $pedido_enviado = false;
    $erro_estoque = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Aqui, futuramente, entraria a lógica real de salvar o pedido no banco.
        // Por enquanto é apenas uma simulação para fins de TCC.
        $nome_cliente = htmlspecialchars($_POST['nome'] ?? '');

        if ($modo_carrinho) {
            // 1º passo: confere se AINDA há estoque suficiente de cada item
            // (pode ter mudado entre o cliente montar o carrinho e confirmar).
            $estoque_ok = true;
            foreach ($itens_carrinho as $item) {
                if (!verificarEstoqueDisponivel($item['produto']['id'], $item['quantidade'])) {
                    $estoque_ok = false;
                    $erro_estoque = 'Estoque insuficiente para "' . $item['produto']['nome'] . '". Remova ou ajuste a quantidade e tente novamente.';
                    break;
                }
            }

            // 2º passo: só dá baixa se TODOS os itens tiverem estoque (evita
            // baixar parte do pedido e falhar no meio do caminho)
            if ($estoque_ok) {
                foreach ($itens_carrinho as $item) {
                    darBaixaEstoque($item['produto']['id'], $item['quantidade']);
                }
                $pedido_enviado = true;
                $_SESSION['carrinho'] = []; // pedido fechado: esvazia o carrinho
            }
        } else {
            // Modo "Comprar Agora": sempre 1 unidade do produto
            if (verificarEstoqueDisponivel($produto['id'], 1)) {
                darBaixaEstoque($produto['id'], 1);
                $pedido_enviado = true;
            } else {
                $erro_estoque = 'Esse produto acabou de ficar sem estoque disponível.';
            }
        }
    }
?>

<div class="mb-8">
    <a href="index.php?pg=produtos" class="text-xs font-black uppercase tracking-widest text-neutral-500 hover:text-red-500 flex items-center gap-2 transition-colors">
        <i class="ph-bold ph-arrow-left"></i> Voltar aos Produtos
    </a>
</div>

<h1 class="text-3xl md:text-4xl font-black tracking-tighter text-white uppercase mb-10 flex items-center gap-3">
    <i class="ph-fill ph-shopping-cart-simple text-red-600"></i> Finalizar Compra
</h1>

<?php if ($pedido_enviado): ?>

    <!-- Confirmação do Pedido -->
    <div class="max-w-xl mx-auto bg-neutral-900/80 border border-red-900/30 rounded-lg p-10 text-center shadow-lg">
        <i class="ph-fill ph-check-circle text-6xl text-red-600 mb-4"></i>
        <h2 class="text-2xl font-black text-white uppercase tracking-wide mb-3">Pedido Confirmado!</h2>
        <p class="text-neutral-400 mb-2">
            Obrigado, <strong class="text-white"><?php echo $nome_cliente; ?></strong>!
        </p>
        <?php if ($modo_carrinho): ?>
            <p class="text-neutral-500 text-sm mb-8">
                Sua compra com <strong class="text-red-500"><?php echo count($itens_carrinho); ?> item(ns)</strong>
                no valor total de <strong class="text-red-500">R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></strong> foi registrada com sucesso.
            </p>
        <?php else: ?>
            <p class="text-neutral-500 text-sm mb-8">
                Sua compra de <strong class="text-red-500"><?php echo $produto['nome']; ?></strong> foi registrada com sucesso.
            </p>
        <?php endif; ?>
        <a href="index.php?pg=produtos" class="bg-red-600 hover:bg-red-500 text-white px-8 py-3 rounded uppercase font-black tracking-widest transition-colors shadow-[0_0_15px_rgba(220,38,38,0.3)]">
            Continuar Comprando
        </a>
    </div>

<?php else: ?>

    <?php if ($erro_estoque): ?>
        <div class="max-w-3xl mx-auto mb-8 bg-red-950/40 border border-red-800 rounded-lg px-6 py-4 flex items-center gap-3">
            <i class="ph-fill ph-warning-circle text-red-500 text-2xl shrink-0"></i>
            <p class="text-red-300 text-sm font-bold"><?php echo htmlspecialchars($erro_estoque); ?></p>
        </div>
    <?php endif; ?>

    <div class="flex flex-col lg:flex-row gap-10">

        <!-- Resumo da Compra -->
        <div class="w-full lg:w-2/5">
            <div class="bg-neutral-900 border border-neutral-800 rounded-lg overflow-hidden sticky top-32 shadow-lg">

                <?php if ($modo_carrinho): ?>

                    <!-- RESUMO: MODO CARRINHO (vários itens) -->
                    <div class="p-6">
                        <h3 class="font-black text-white text-lg uppercase tracking-wide mb-5 flex items-center gap-2 border-b border-neutral-800 pb-3">
                            <i class="ph-fill ph-shopping-cart-simple text-red-600"></i> Seu Carrinho
                        </h3>

                        <div class="space-y-4 mb-5">
                            <?php foreach ($itens_carrinho as $item): ?>
                                <div class="flex items-center gap-3 bg-neutral-950 border border-neutral-800 rounded-lg p-3">
                                    <img src="<?php echo $item['produto']['imagem']; ?>" alt="<?php echo htmlspecialchars($item['produto']['nome']); ?>" class="w-14 h-14 object-cover rounded border border-neutral-800 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white text-sm font-bold uppercase tracking-wide line-clamp-1"><?php echo htmlspecialchars($item['produto']['nome']); ?></p>
                                        <p class="text-neutral-500 text-xs">
                                            <?php echo $item['quantidade']; ?> x R$ <?php echo number_format($item['produto']['preco'], 2, ',', '.'); ?>
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-red-500 font-black text-sm">R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></p>
                                        <a href="index.php?pg=finalizar&remove_cart=<?php echo $item['produto']['id']; ?>" class="text-neutral-600 hover:text-red-500 text-[10px] uppercase tracking-widest font-bold transition-colors">
                                            Remover
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="border-t border-neutral-800 pt-5 flex items-center justify-between">
                            <span class="text-[10px] text-neutral-500 font-bold uppercase tracking-widest">Total</span>
                            <span class="text-2xl font-black text-red-500">
                                R$ <?php echo number_format($valor_total, 2, ',', '.'); ?>
                            </span>
                        </div>
                    </div>

                <?php else: ?>

                    <!-- RESUMO: MODO PRODUTO ÚNICO (Comprar Agora) -->
                    <div class="relative overflow-hidden bg-black aspect-[4/3]">
                        <img src="<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>" class="w-full h-full object-cover opacity-90">
                        <span class="absolute top-3 left-3 bg-black/80 backdrop-blur text-red-500 border border-red-900/50 text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded shadow-md">
                            <?php echo $produto['categoria']; ?>
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-black text-white text-lg uppercase tracking-wide mb-2">
                            <?php echo $produto['nome']; ?>
                        </h3>
                        <p class="text-neutral-400 text-sm mb-6 leading-relaxed">
                            <?php echo $produto['descricao']; ?>
                        </p>
                        <div class="border-t border-neutral-800 pt-5 flex items-center justify-between">
                            <span class="text-[10px] text-neutral-500 font-bold uppercase tracking-widest">Total</span>
                            <span class="text-2xl font-black text-red-500">
                                R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                            </span>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>

        <!-- Formulário de Dados e Pagamento -->
        <div class="w-full lg:w-3/5">
            <div class="bg-neutral-900/80 border border-red-900/30 rounded-lg p-8 shadow-lg">

                <form method="POST" action="<?php echo $form_action; ?>" class="space-y-8">

                    <!-- Dados Pessoais -->
                    <div>
                        <h3 class="font-black text-white uppercase tracking-widest text-sm mb-5 flex items-center gap-2 border-b border-neutral-800 pb-3">
                            <i class="ph-fill ph-user text-red-600"></i> Dados Pessoais
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">Nome Completo</label>
                                <input type="text" name="nome" required class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Seu nome completo">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">CPF</label>
                                <input type="text" name="cpf" required maxlength="14" class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="000.000.000-00">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">E-mail</label>
                                <input type="email" name="email" required class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="seuemail@exemplo.com">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">Telefone</label>
                                <input type="text" name="telefone" required class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="(00) 00000-0000">
                            </div>
                        </div>
                    </div>

                    <!-- Endereço de Entrega -->
                    <div>
                        <h3 class="font-black text-white uppercase tracking-widest text-sm mb-5 flex items-center gap-2 border-b border-neutral-800 pb-3">
                            <i class="ph-fill ph-map-pin-line text-red-600"></i> Endereço de Entrega
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">Endereço</label>
                                <input type="text" name="endereco" required class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Rua, número, bairro">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">CEP</label>
                                <input type="text" name="cep" required class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="00000-000">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">Cidade</label>
                                <input type="text" name="cidade" required class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Sua cidade">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">Estado</label>
                                <input type="text" name="estado" required class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="UF">
                            </div>
                        </div>
                    </div>

                    <!-- Pagamento -->
                    <div>
                        <h3 class="font-black text-white uppercase tracking-widest text-sm mb-5 flex items-center gap-2 border-b border-neutral-800 pb-3">
                            <i class="ph-fill ph-credit-card text-red-600"></i> Pagamento
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                            <label class="flex items-center gap-3 bg-neutral-800 border border-neutral-700 rounded px-4 py-3 cursor-pointer hover:border-red-600 transition-colors has-[:checked]:border-red-600 has-[:checked]:bg-red-900/10">
                                <input type="radio" name="pagamento" value="cartao" checked onchange="alternarPagamento(this.value)" class="accent-red-600">
                                <span class="text-sm font-bold text-neutral-300">Cartão de Crédito</span>
                            </label>
                            <label class="flex items-center gap-3 bg-neutral-800 border border-neutral-700 rounded px-4 py-3 cursor-pointer hover:border-red-600 transition-colors has-[:checked]:border-red-600 has-[:checked]:bg-red-900/10">
                                <input type="radio" name="pagamento" value="pix" onchange="alternarPagamento(this.value)" class="accent-red-600">
                                <span class="text-sm font-bold text-neutral-300">Pix</span>
                            </label>
                            <label class="flex items-center gap-3 bg-neutral-800 border border-neutral-700 rounded px-4 py-3 cursor-pointer hover:border-red-600 transition-colors has-[:checked]:border-red-600 has-[:checked]:bg-red-900/10">
                                <input type="radio" name="pagamento" value="boleto" onchange="alternarPagamento(this.value)" class="accent-red-600">
                                <span class="text-sm font-bold text-neutral-300">Boleto</span>
                            </label>
                        </div>

                        <!-- Campos do Cartão -->
                        <div id="campos-cartao" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">Número do Cartão</label>
                                <input type="text" name="numero_cartao" class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="0000 0000 0000 0000">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">Validade / CVV</label>
                                <div class="flex gap-2">
                                    <input type="text" name="validade" class="w-1/2 bg-neutral-800 border border-neutral-700 rounded px-3 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="MM/AA">
                                    <input type="text" name="cvv" class="w-1/2 bg-neutral-800 border border-neutral-700 rounded px-3 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="CVV">
                                </div>
                            </div>
                        </div>

                        <!-- Campos do Pix -->
                        <?php
                            // Código Pix copia-e-cola informado
                            $pix_codigo = "00020126360014br.gov.bcb.pix0114+55449843826875204000053039865802BR5924MENDONCAADONAI20230728086009SaoPaulo610901227-20062240520daqr14357445729580746304118E";
                        ?>
                        <div id="campos-pix" class="hidden">

                            <!-- Etapa 1: Nome Completo e CPF -->
                            <div id="pix-dados" class="bg-neutral-950 border border-neutral-800 rounded-lg p-6 mb-5">
                                <p class="text-[11px] text-neutral-500 uppercase tracking-widest font-bold mb-4">Confirme seus dados para gerar o Pix</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">Nome Completo</label>
                                        <input type="text" id="pix-nome" class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Seu nome completo">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold uppercase tracking-widest text-neutral-500 mb-2">CPF</label>
                                        <input type="text" id="pix-cpf" maxlength="14" class="w-full bg-neutral-800 border border-neutral-700 rounded px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="000.000.000-00">
                                    </div>
                                </div>
                                <p id="pix-erro" class="text-red-500 text-xs mb-3 hidden">Preencha nome completo e CPF para gerar o QR Code.</p>
                                <button type="button" onclick="gerarQrCodePix()" class="w-full bg-red-600 hover:bg-red-500 text-white text-[11px] font-black uppercase tracking-widest py-3 rounded transition-colors flex items-center justify-center gap-2">
                                    <i class="ph-fill ph-qr-code"></i> Gerar QR Code
                                </button>
                            </div>

                            <!-- Etapa 2: QR Code (aparece só depois de preencher nome/cpf) -->
                            <div id="pix-qrcode" class="hidden flex-col items-center bg-neutral-950 border border-neutral-800 rounded-lg p-6 mb-5">
                                <p class="text-sm text-neutral-300 mb-4 text-center">
                                    Gerando Pix para <strong id="pix-nome-exibido" class="text-white"></strong> — CPF <strong id="pix-cpf-exibido" class="text-white"></strong>
                                </p>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?php echo urlencode($pix_codigo); ?>" alt="QR Code Pix" class="rounded-lg border border-neutral-700 mb-4 bg-white p-2">
                                <p class="text-[11px] text-neutral-500 uppercase tracking-widest font-bold mb-2">Pix Copia e Cola</p>
                                <div class="w-full flex gap-2">
                                    <input type="text" id="pix-input" readonly value="<?php echo htmlspecialchars($pix_codigo); ?>" class="flex-1 bg-neutral-800 border border-neutral-700 rounded px-3 py-3 text-neutral-400 text-xs focus:outline-none">
                                    <button type="button" onclick="copiarPix()" class="bg-red-600 hover:bg-red-500 text-white text-[11px] font-black uppercase tracking-widest px-4 rounded transition-colors">
                                        <i class="ph-bold ph-copy"></i> Copiar
                                    </button>
                                </div>
                                <p class="text-neutral-600 text-[11px] mt-3 text-center">
                                    Abra o app do seu banco, escolha pagar via Pix (QR Code ou Copia e Cola) e confirme o pagamento.
                                </p>
                                <!-- Campos ocultos para irem junto no envio do formulário -->
                                <input type="hidden" name="pix_nome" id="pix-nome-hidden">
                                <input type="hidden" name="pix_cpf" id="pix-cpf-hidden">
                            </div>
                        </div>

                        <!-- Campos do Boleto -->
                        <div id="campos-boleto" class="hidden">
                            <p class="text-neutral-500 text-sm bg-neutral-950 border border-neutral-800 rounded-lg p-6 text-center">
                                O boleto será gerado após a confirmação do pedido e enviado para o e-mail informado.
                            </p>
                        </div>

                        <p class="text-neutral-600 text-[11px] mt-3">
                            <i class="ph ph-lock-key"></i> Ambiente simulado para fins de TCC — nenhum dado de pagamento é processado de verdade.
                        </p>
                    </div>

                    <!-- Botão Confirmar -->
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-black uppercase tracking-widest py-4 rounded transition-colors shadow-[0_0_20px_rgba(220,38,38,0.3)] flex items-center justify-center gap-3">
                        <i class="ph-fill ph-lock-key"></i> Confirmar Pedido — R$ <?php echo number_format($valor_total, 2, ',', '.'); ?>
                    </button>

                </form>
            </div>
        </div>

    </div>

<script>
    function alternarPagamento(metodo) {
        document.getElementById('campos-cartao').classList.add('hidden');
        document.getElementById('campos-pix').classList.add('hidden');
        document.getElementById('campos-boleto').classList.add('hidden');

        if (metodo === 'cartao') {
            document.getElementById('campos-cartao').classList.remove('hidden');
        } else if (metodo === 'pix') {
            document.getElementById('campos-pix').classList.remove('hidden');
            // Sempre volta pra etapa de dados ao entrar na aba Pix
            document.getElementById('pix-dados').classList.remove('hidden');
            document.getElementById('pix-qrcode').classList.add('hidden');
            document.getElementById('pix-qrcode').classList.remove('flex');
        } else if (metodo === 'boleto') {
            document.getElementById('campos-boleto').classList.remove('hidden');
        }
    }

    function gerarQrCodePix() {
        const nome = document.getElementById('pix-nome').value.trim();
        const cpf = document.getElementById('pix-cpf').value.trim();
        const erro = document.getElementById('pix-erro');

        if (nome === '' || cpf === '') {
            erro.classList.remove('hidden');
            return;
        }

        erro.classList.add('hidden');

        // Preenche os campos ocultos que vão junto no envio do formulário
        document.getElementById('pix-nome-hidden').value = nome;
        document.getElementById('pix-cpf-hidden').value = cpf;

        // Exibe os dados confirmados acima do QR Code
        document.getElementById('pix-nome-exibido').textContent = nome;
        document.getElementById('pix-cpf-exibido').textContent = cpf;

        // Esconde a etapa de dados e mostra o QR Code
        document.getElementById('pix-dados').classList.add('hidden');
        document.getElementById('pix-qrcode').classList.remove('hidden');
        document.getElementById('pix-qrcode').classList.add('flex');
    }

    function copiarPix() {
        const input = document.getElementById('pix-input');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);

        const botao = event.currentTarget;
        const textoOriginal = botao.innerHTML;
        botao.innerHTML = '<i class="ph-bold ph-check"></i> Copiado!';
        setTimeout(() => { botao.innerHTML = textoOriginal; }, 2000);
    }
</script>

<?php endif; ?>