<?php
/**
 * admin/pages/produtos.php
 *
 * Lista todos os produtos cadastrados, com botões de editar/excluir e um
 * botão para cadastrar um novo produto.
 */

$caminho_produtos = __DIR__ . '/../../data/produtos.json';

function lerProdutos(string $caminho): array
{
    if (!file_exists($caminho)) return [];
    $dados = json_decode(file_get_contents($caminho), true);
    return is_array($dados) ? $dados : [];
}

$produtos = lerProdutos($caminho_produtos);

// Mensagem de feedback vinda de outra página (ex: depois de salvar ou excluir)
$mensagem = $_GET['msg'] ?? null;
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h1 class="text-3xl font-black tracking-tighter text-white uppercase mb-1">Produtos</h1>
        <p class="text-zinc-500 text-sm"><?php echo count($produtos); ?> produto<?php echo count($produtos) !== 1 ? 's' : ''; ?> cadastrado<?php echo count($produtos) !== 1 ? 's' : ''; ?></p>
    </div>
    <a href="index.php?pg=editar_produto" class="bg-red-600 hover:bg-red-500 text-white text-xs font-black uppercase tracking-widest px-6 py-3 rounded-xl transition-colors flex items-center gap-2">
        <i class="ph-fill ph-plus-circle"></i> Novo Produto
    </a>
</div>

<?php if ($mensagem === 'salvo'): ?>
    <div class="bg-green-900/20 border border-green-700/40 text-green-400 text-sm rounded-xl px-5 py-4 mb-6 flex items-center gap-2">
        <i class="ph-fill ph-check-circle"></i> Produto salvo com sucesso.
    </div>
<?php elseif ($mensagem === 'excluido'): ?>
    <div class="bg-green-900/20 border border-green-700/40 text-green-400 text-sm rounded-xl px-5 py-4 mb-6 flex items-center gap-2">
        <i class="ph-fill ph-check-circle"></i> Produto excluído com sucesso.
    </div>
<?php endif; ?>

<?php if (empty($produtos)): ?>
    <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-16 text-center">
        <i class="ph ph-package text-6xl text-zinc-700 mb-4"></i>
        <p class="text-zinc-500 text-sm mb-6">Nenhum produto cadastrado ainda.</p>
        <a href="index.php?pg=editar_produto" class="bg-red-600 hover:bg-red-500 text-white text-xs font-black uppercase tracking-widest px-6 py-3 rounded-xl transition-colors inline-flex items-center gap-2">
            <i class="ph-fill ph-plus-circle"></i> Cadastrar Primeiro Produto
        </a>
    </div>
<?php else: ?>
    <div class="bg-zinc-950 border border-zinc-800 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-800 text-left text-[11px] uppercase tracking-widest text-zinc-500">
                    <th class="p-4 font-bold">Produto</th>
                    <th class="p-4 font-bold">Categoria</th>
                    <th class="p-4 font-bold">Preço</th>
                    <th class="p-4 font-bold text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr class="border-b border-zinc-900 last:border-0 hover:bg-zinc-900/50 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="../<?php echo htmlspecialchars($produto['imagem'] ?? ''); ?>" alt="" class="w-12 h-12 object-cover rounded-lg border border-zinc-800 bg-white/5 shrink-0" onerror="this.style.display='none'">
                                <span class="text-white font-bold line-clamp-1"><?php echo htmlspecialchars($produto['nome'] ?? ''); ?></span>
                            </div>
                        </td>
                        <td class="p-4 text-zinc-400"><?php echo htmlspecialchars($produto['categoria'] ?? '—'); ?></td>
                        <td class="p-4 text-red-500 font-black">R$ <?php echo number_format((float) ($produto['preco'] ?? 0), 2, ',', '.'); ?></td>
                        <td class="p-4">
                            <div class="flex justify-end gap-2">
                                <a href="index.php?pg=editar_produto&id=<?php echo $produto['id']; ?>" class="p-2 rounded-lg hover:bg-zinc-800 text-zinc-400 hover:text-white transition-colors" title="Editar">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </a>
                                <a href="index.php?pg=produtos_excluir&id=<?php echo $produto['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este produto?');" class="p-2 rounded-lg hover:bg-red-900/20 text-zinc-400 hover:text-red-500 transition-colors" title="Excluir">
                                    <i class="ph ph-trash text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>