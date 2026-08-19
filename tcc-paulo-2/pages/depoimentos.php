<?php
/**
 * pages/depoimentos.php
 *
 * Seção de avaliações/depoimentos de clientes, para exibir na página inicial.
 * As avaliações são fictícias — feitas só pra dar credibilidade visual à loja
 * (bom pra fins de TCC/demonstração). Se um dia isso virar uma loja real,
 * troque pelos depoimentos de clientes de verdade.
 *
 * Como usar: dentro de pages/inicio.php, adicione esta linha no local onde
 * quiser que a seção apareça:
 *
 *   <?php include __DIR__ . '/depoimentos.php'; ?>
 */

$depoimentos = [
    [
        'nome'      => 'Rafael M.',
        'cidade'    => 'Curitiba, PR',
        'nota'      => 5,
        'texto'     => 'Comprei uma caixa de Megaevolução e chegou rapidinho, tudo lacrado e muito bem embalado. Já é a segunda compra que faço aqui!',
        'iniciais'  => 'RM',
    ],
    [
        'nome'      => 'Camila S.',
        'cidade'    => 'São Paulo, SP',
        'nota'      => 5,
        'texto'     => 'Atendimento excelente, tiraram todas as minhas dúvidas antes da compra. Os boosters vieram intactos e o preço é bem justo comparado a outras lojas.',
        'iniciais'  => 'CS',
    ],
    [
        'nome'      => 'Lucas T.',
        'cidade'    => 'Belo Horizonte, MG',
        'nota'      => 4,
        'texto'     => 'Site fácil de navegar e o pagamento via Pix foi super prático. Só demorou um pouco mais que o esperado na entrega, mas o produto compensou.',
        'iniciais'  => 'LT',
    ],
    [
        'nome'      => 'Beatriz A.',
        'cidade'    => 'Porto Alegre, RS',
        'nota'      => 5,
        'texto'     => 'Achei difícil encontrar a coleção Sol e Lua em outro lugar, e aqui tinha em estoque. Virei cliente fiel da Chaos TCG Store!',
        'iniciais'  => 'BA',
    ],
];
?>

<section class="py-16">
    <div class="text-center mb-12">
        <span class="text-red-600 text-xs font-black uppercase tracking-widest flex items-center justify-center gap-2 mb-3">
            <i class="ph-fill ph-chat-circle-text"></i> Depoimentos
        </span>
        <h2 class="text-3xl md:text-4xl font-black tracking-tighter text-white uppercase">
            O Que Nossos Duelistas Dizem
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($depoimentos as $d): ?>
            <div class="bg-neutral-900 border border-red-900/30 rounded-lg p-6 flex flex-col hover:border-red-600/50 transition-colors">

                <!-- Estrelas -->
                <div class="flex gap-1 mb-4 text-red-600">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="<?php echo $i <= $d['nota'] ? 'ph-fill' : 'ph'; ?> ph-star text-sm"></i>
                    <?php endfor; ?>
                </div>

                <!-- Texto do depoimento -->
                <p class="text-neutral-400 text-sm leading-relaxed mb-6 flex-grow">
                    "<?php echo htmlspecialchars($d['texto']); ?>"
                </p>

                <!-- Autor -->
                <div class="flex items-center gap-3 pt-4 border-t border-neutral-800">
                    <div class="w-10 h-10 rounded-full bg-red-600/20 border border-red-600/40 flex items-center justify-center text-red-500 text-xs font-black shrink-0">
                        <?php echo htmlspecialchars($d['iniciais']); ?>
                    </div>
                    <div>
                        <p class="text-white text-xs font-bold uppercase tracking-wide"><?php echo htmlspecialchars($d['nome']); ?></p>
                        <p class="text-neutral-600 text-[11px]"><?php echo htmlspecialchars($d['cidade']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>