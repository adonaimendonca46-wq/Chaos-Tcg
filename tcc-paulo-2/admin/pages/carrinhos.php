<?php
require_once "../includes/banco_ficticio.php";

//Oculta um pedido (não aparece mais na lista principal, mas continua contando no faturamento)
if (isset($_GET['ocultar'])) {
    $idOcultar = (int) $_GET['ocultar'];
    atualizarPedido($idOcultar, ['oculto' => true]);
}

//Ativa de volta um pedido que estava oculto
if (isset($_GET['ativar'])) {
    $idAtivar = (int) $_GET['ativar'];
    atualizarPedido($idAtivar, ['oculto' => false]);
}

//Decide se estamos vendo a lista normal ou a lista de ocultados
$verOcultados = isset($_GET['ver']) && $_GET['ver'] === 'ocultados';

$todosPedidos = listarPedidos();

//Filtra: na tela normal só mostra quem NÃO está oculto; na tela de ocultados só mostra quem ESTÁ oculto
$pedidosExibir = array_filter($todosPedidos, function ($p) use ($verOcultados) {
    $oculto = isset($p['oculto']) ? $p['oculto'] : false;
    return $verOcultados ? $oculto : !$oculto;
});
$pedidosExibir = array_reverse($pedidosExibir);

function formaPagamentoTexto($valor) {
    return $valor === 'pix' ? 'Pix' : 'Cartão de Crédito/Débito';
}

// ==========================================
// Controle de "Mostrar Mais / Mostrar Menos"
// ==========================================
$limiteInicial = 3;
$verTodas = isset($_GET['ver_todas']) && $_GET['ver_todas'] === '1';
$totalPedidos = count($pedidosExibir);

if (!$verTodas && $totalPedidos > $limiteInicial) {
    $pedidosExibidos = array_slice($pedidosExibir, 0, $limiteInicial);
} else {
    $pedidosExibidos = $pedidosExibir;
}

// Helper para manter a URL limpa preservando a visualização de ocultados
$urlBase = "index.php?pg=carrinhos" . ($verOcultados ? "&ver=ocultados" : "");
?>

<div class="mb-10 border-b-4 border-[#4a2e1b] pb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
        <h1 class="text-4xl font-black text-[#2b1a10] uppercase tracking-widest">
            <?php echo $verOcultados ? 'Pedidos Ocultados' : 'Compras Realizadas'; ?>
        </h1>
        <p class="text-[#633e25] font-bold mt-2">
            <?php echo $verOcultados
                ? 'Pedidos que foram ocultados da lista principal. Você pode ativá-los de volta.'
                : 'Acompanhe tudo que já foi comprado pelo carrinho do site.'; ?>
        </p>
    </div>

    <?php if ($verOcultados): ?>
        <a href="index.php?pg=carrinhos#painel-pedidos"
           class="inline-flex items-center gap-2 bg-[#d4a373] hover:bg-[#e6b98a] text-[#2b1a10] font-black py-3 px-6 rounded-md transition text-sm border-2 border-[#2b1a10] shadow-[4px_4px_0_#2b1a10] active:shadow-none active:translate-y-1 active:translate-x-1 uppercase tracking-widest shrink-0">
            <i class="ph ph-arrow-left text-lg"></i> Voltar aos Pedidos
        </a>
    <?php else: ?>
        <a href="index.php?pg=carrinhos&ver=ocultados#painel-pedidos"
           class="inline-flex items-center gap-2 bg-[#4a2e1b] hover:bg-[#633e25] text-[#f4e4d8] font-black py-3 px-6 rounded-md transition text-sm border-2 border-[#2b1a10] shadow-[4px_4px_0_#2b1a10] active:shadow-none active:translate-y-1 active:translate-x-1 uppercase tracking-widest shrink-0">
            <i class="ph ph-eye-slash text-lg"></i> Ver Pedidos Ocultados
        </a>
    <?php endif; ?>
</div>

<div id="painel-pedidos" class="scroll-mt-6">

    <?php if (empty($pedidosExibir)): ?>

        <div class="bg-[#f4e4d8] border-4 border-[#2b1a10] rounded-lg p-10 text-center shadow-[8px_8px_0_#2b1a10]">
            <i class="ph ph-shopping-cart text-6xl text-[#633e25] mb-4 inline-block"></i>
            <p class="font-bold text-[#633e25] uppercase tracking-wider">
                <?php echo $verOcultados ? 'Nenhum pedido oculto no momento.' : 'Nenhuma compra realizada ainda.'; ?>
            </p>
        </div>

    <?php else: ?>

        <?php if ($verTodas && $totalPedidos > $limiteInicial): ?>
            <div class="mb-6 text-right">
                <a href="<?php echo $urlBase; ?>#painel-pedidos"
                   class="inline-flex items-center gap-2 bg-[#d4a373] hover:bg-[#e6b98a] text-[#2b1a10] font-black py-2 px-4 rounded-md transition text-xs border-2 border-[#2b1a10] shadow-[3px_3px_0_#2b1a10] active:shadow-none active:translate-y-1 active:translate-x-1 uppercase tracking-widest">
                    <i class="ph ph-caret-up text-lg"></i> Mostrar Menos
                </a>
            </div>
        <?php endif; ?>

        <div class="flex flex-col gap-6">
            <?php foreach ($pedidosExibidos as $pedido): ?>
                <div class="relative bg-[#f4e4d8] border-4 border-[#2b1a10] rounded-lg overflow-hidden shadow-[8px_8px_0_#2b1a10]">

                    <?php if (!$verOcultados): ?>
                        <a href="<?php echo $urlBase; ?>&ocultar=<?php echo $pedido['id']; ?><?php echo $verTodas ? '&ver_todas=1' : ''; ?>#painel-pedidos"
                           title="Ocultar pedido"
                           class="absolute top-3 right-3 z-10 bg-red-700 hover:bg-red-800 text-white w-9 h-9 rounded-full flex items-center justify-center border-2 border-[#2b1a10] shadow-[3px_3px_0_#2b1a10] transition">
                            <i class="ph ph-x text-lg font-black"></i>
                        </a>
                    <?php endif; ?>

                    <div class="bg-[#4a2e1b] border-b-4 border-[#2b1a10] p-4 flex flex-wrap items-center justify-between gap-3 pr-14">
                        <div class="flex items-center gap-3">
                            <span class="bg-[#d4a373] text-[#2b1a10] text-xs font-black px-3 py-1.5 rounded-sm uppercase tracking-widest border-2 border-[#2b1a10]">
                                Pedido #<?php echo $pedido['id']; ?>
                            </span>
                        </div>
                        <span class="text-[#f4e4d8] text-xs font-black uppercase tracking-widest flex items-center gap-2">
                            <i class="ph <?php echo $pedido['forma_pagamento'] === 'pix' ? 'ph-qr-code' : 'ph-credit-card'; ?>"></i>
                            <?php echo formaPagamentoTexto($pedido['forma_pagamento']); ?>
                        </span>
                    </div>

                    <div class="p-5">
                        <table class="w-full text-left border-collapse mb-4">
                            <thead>
                                <tr class="border-b-2 border-[#4a2e1b]">
                                    <th class="py-2 text-[10px] font-black text-[#633e25] uppercase tracking-widest">Produto</th>
                                    <th class="py-2 text-[10px] font-black text-[#633e25] uppercase tracking-widest text-center">Qtd</th>
                                    <th class="py-2 text-[10px] font-black text-[#633e25] uppercase tracking-widest text-right">Unitário</th>
                                    <th class="py-2 text-[10px] font-black text-[#633e25] uppercase tracking-widest text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#4a2e1b]/20">
                                <?php foreach ($pedido['itens'] as $item): ?>
                                    <tr>
                                        <td class="py-2 font-bold text-[#2b1a10]"><?php echo htmlspecialchars($item['nome']); ?></td>
                                        <td class="py-2 font-bold text-[#2b1a10] text-center"><?php echo $item['quantidade']; ?></td>
                                        <td class="py-2 font-bold text-[#2b1a10] text-right"><?php echo formatarPreco($item['preco_unitario']); ?></td>
                                        <td class="py-2 font-bold text-[#2b1a10] text-right"><?php echo formatarPreco($item['subtotal']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="flex flex-wrap items-center justify-between gap-4 border-t-2 border-[#4a2e1b] pt-4">
                            <?php if ($verOcultados): ?>
                                <a href="<?php echo $urlBase; ?>&ativar=<?php echo $pedido['id']; ?><?php echo $verTodas ? '&ver_todas=1' : ''; ?>#painel-pedidos"
                                   class="inline-flex items-center gap-2 bg-[#d4a373] hover:bg-[#e6b98a] text-[#2b1a10] font-black py-2 px-4 rounded-md transition text-xs border-2 border-[#2b1a10] shadow-[3px_3px_0_#2b1a10] active:shadow-none active:translate-y-1 active:translate-x-1 uppercase tracking-widest">
                                    <i class="ph ph-eye text-lg"></i> Ativar Pedido
                                </a>
                            <?php else: ?>
                                <span></span>
                            <?php endif; ?>

                            <div class="flex items-center gap-6 ml-auto">
                                <span class="text-xs font-black text-[#633e25] uppercase tracking-widest">
                                    <?php echo $pedido['quantidade_total']; ?> ite(ns) no total
                                </span>
                                <span class="text-xl font-black text-[#4a2e1b]">
                                    <?php echo formatarPreco($pedido['valor_total']); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$verTodas && $totalPedidos > $limiteInicial): ?>
            <div class="mt-8 text-center">
                <a href="<?php echo $urlBase; ?>&ver_todas=1#painel-pedidos"
                   class="inline-flex items-center gap-2 bg-[#4a2e1b] hover:bg-[#633e25] text-[#f4e4d8] font-black py-3 px-6 rounded-md transition text-sm border-2 border-[#2b1a10] shadow-[4px_4px_0_#2b1a10] active:shadow-none active:translate-y-1 active:translate-x-1 uppercase tracking-widest">
                    <i class="ph ph-caret-down text-lg"></i> Mostrar Mais (<?php echo $totalPedidos - $limiteInicial; ?>)
                </a>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>