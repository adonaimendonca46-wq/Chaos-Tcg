<?php
/**
 * admin/pages/editar_produto.php
 *
 * Formulário de cadastro/edição de produto. Se vier ?id=X na URL, carrega
 * os dados desse produto pra edição; senão, é um cadastro novo.
 */

$caminho_produtos   = __DIR__ . '/../../data/produtos.json';
$caminho_categorias = __DIR__ . '/../../data/categorias.json';
$pasta_imagens      = __DIR__ . '/../../data/';

function lerProdutosForm(string $caminho): array
{
    if (!file_exists($caminho)) return [];
    $dados = json_decode(file_get_contents($caminho), true);
    return is_array($dados) ? $dados : [];
}

function salvarProdutos(string $caminho, array $produtos): void
{
    file_put_contents($caminho, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function listarCategoriasForm(string $caminho, array $produtos): array
{
    $nomes = [];

    if (file_exists($caminho)) {
        $dados = json_decode(file_get_contents($caminho), true);
        if (is_array($dados)) {
            foreach ($dados as $item) {
                if (is_string($item)) {
                    $nomes[] = $item;
                } elseif (is_array($item) && isset($item['nome'])) {
                    $nomes[] = $item['nome'];
                }
            }
        }
    }

    // Fallback: se não achou nada no categorias.json, usa as categorias já usadas nos produtos
    if (empty($nomes)) {
        foreach ($produtos as $p) {
            if (!empty($p['categoria']) && !in_array($p['categoria'], $nomes)) {
                $nomes[] = $p['categoria'];
            }
        }
    }

    sort($nomes);
    return $nomes;
}

$produtos   = lerProdutosForm($caminho_produtos);
$categorias = listarCategoriasForm($caminho_categorias, $produtos);

$id_edicao   = isset($_GET['id']) ? (int) $_GET['id'] : null;
$modo_edicao = $id_edicao !== null;

$produto_atual = [
    'id'        => null,
    'nome'      => '',
    'preco'     => '',
    'categoria' => '',
    'imagem'    => '',
    'descricao' => '',
    'estoque'   => 0,
];

if ($modo_edicao) {
    foreach ($produtos as $p) {
        if ((int) $p['id'] === $id_edicao) {
            $produto_atual = $p;
            // Produtos cadastrados antes do campo de estoque existir não têm essa chave
            if (!isset($produto_atual['estoque'])) {
                $produto_atual['estoque'] = 0;
            }
            break;
        }
    }
}

$erro = null;

// ------------------------------------------------------------
// Processa o envio do formulário
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome         = trim($_POST['nome'] ?? '');
    $preco        = (float) str_replace(',', '.', trim($_POST['preco'] ?? '0'));
    $categoria    = trim($_POST['categoria'] ?? '');
    $descricao    = trim($_POST['descricao'] ?? '');
    $imagem_atual = trim($_POST['imagem_atual'] ?? '');
    $estoque      = isset($_POST['estoque']) ? max(0, (int) $_POST['estoque']) : 0;

    if ($nome === '' || $categoria === '' || $preco <= 0) {
        $erro = 'Preencha nome, categoria e um preço válido.';
    } else {

        $caminho_imagem_salva = $imagem_atual; // mantém a imagem antiga por padrão

        // Se veio um arquivo novo de imagem, salva ele em data/ e atualiza o caminho
        if (!empty($_FILES['imagem']['name'])) {
            $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $extensoes_permitidas = ['png', 'jpg', 'jpeg', 'webp'];

            if (!in_array($extensao, $extensoes_permitidas)) {
                $erro = 'Formato de imagem inválido. Use PNG, JPG ou WEBP.';
            } else {
                $nome_arquivo = uniqid('produto_') . '.' . $extensao;
                $destino = $pasta_imagens . $nome_arquivo;

                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                    $caminho_imagem_salva = 'data/' . $nome_arquivo;
                } else {
                    $erro = 'Não foi possível salvar a imagem enviada.';
                }
            }
        }

        if (!$erro) {
            $novo_produto = [
                'nome'      => $nome,
                'preco'     => $preco,
                'categoria' => $categoria,
                'imagem'    => $caminho_imagem_salva,
                'descricao' => $descricao,
                'estoque'   => $estoque,
            ];

            if ($modo_edicao) {
                // Atualiza o produto existente, mantendo o mesmo id
                foreach ($produtos as $indice => $p) {
                    if ((int) $p['id'] === $id_edicao) {
                        $novo_produto['id']  = $id_edicao;
                        $produtos[$indice]   = $novo_produto;
                        break;
                    }
                }
            } else {
                // Gera um novo id (maior id existente + 1)
                $maior_id = 0;
                foreach ($produtos as $p) {
                    $maior_id = max($maior_id, (int) ($p['id'] ?? 0));
                }
                $novo_produto['id'] = $maior_id + 1;
                $produtos[] = $novo_produto;
            }

            salvarProdutos($caminho_produtos, $produtos);

            // OBS: não dá pra usar header('Location: ...') aqui -- esse arquivo
            // é incluído dentro do admin/index.php, que já mandou HTML pro
            // navegador antes de chegar até aqui. Por isso o redirecionamento
            // é feito via JavaScript, que funciona nesse cenário.
            echo '<script>window.location.replace("index.php?pg=produtos&msg=salvo");</script>';
            echo '<noscript><p style="color:#fff;padding:2rem;">Produto salvo. <a href="index.php?pg=produtos&msg=salvo" style="color:#dc2626;">Clique aqui para voltar</a>.</p></noscript>';
            exit;
        }
    }
}
?>

<div class="mb-8">
    <a href="index.php?pg=produtos" class="text-xs font-black uppercase tracking-widest text-zinc-500 hover:text-red-500 flex items-center gap-2 transition-colors">
        <i class="ph-bold ph-arrow-left"></i> Voltar aos Produtos
    </a>
</div>

<h1 class="text-3xl font-black tracking-tighter text-white uppercase mb-10">
    <?php echo $modo_edicao ? 'Editar Produto' : 'Novo Produto'; ?>
</h1>

<?php if ($erro): ?>
    <div class="bg-red-900/20 border border-red-700/40 text-red-400 text-sm rounded-xl px-5 py-4 mb-6 flex items-center gap-2">
        <i class="ph-fill ph-warning-circle"></i> <?php echo htmlspecialchars($erro); ?>
    </div>
<?php endif; ?>

<div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-8 max-w-3xl">
    <form method="POST" enctype="multipart/form-data" class="space-y-6">

        <input type="hidden" name="imagem_atual" value="<?php echo htmlspecialchars($produto_atual['imagem'] ?? ''); ?>">

        <div>
            <label class="block text-[11px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Nome do Produto</label>
            <input type="text" name="nome" required value="<?php echo htmlspecialchars($produto_atual['nome'] ?? ''); ?>" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Ex: Booster Avulso Pokémon - Mega Evolução">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Preço (R$)</label>
                <input type="text" name="preco" required value="<?php echo $produto_atual['preco'] !== '' ? htmlspecialchars((string) $produto_atual['preco']) : ''; ?>" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Ex: 49.90">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Categoria</label>
                <input type="text" name="categoria" required list="lista-categorias" value="<?php echo htmlspecialchars($produto_atual['categoria'] ?? ''); ?>" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Ex: Boosters Avulsos">
                <datalist id="lista-categorias">
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Estoque (unidades)</label>
                <input type="number" name="estoque" min="0" step="1" required value="<?php echo (int) ($produto_atual['estoque'] ?? 0); ?>" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Ex: 20">
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Descrição</label>
            <textarea name="descricao" rows="4" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-red-600 transition-colors" placeholder="Detalhes do produto, conteúdo da caixa, etc."><?php echo htmlspecialchars($produto_atual['descricao'] ?? ''); ?></textarea>
        </div>

        <div>
            <label class="block text-[11px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Imagem</label>

            <?php if (!empty($produto_atual['imagem'])): ?>
                <div class="flex items-center gap-3 mb-3">
                    <img src="../<?php echo htmlspecialchars($produto_atual['imagem']); ?>" class="w-16 h-16 object-cover rounded-lg border border-zinc-800 bg-white/5">
                    <span class="text-zinc-500 text-xs">Imagem atual — envie um novo arquivo abaixo só se quiser trocá-la.</span>
                </div>
            <?php endif; ?>

            <input type="file" name="imagem" accept=".png,.jpg,.jpeg,.webp" class="w-full text-sm text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-red-600 file:text-white hover:file:bg-red-500 file:cursor-pointer cursor-pointer">
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white text-xs font-black uppercase tracking-widest px-8 py-3 rounded-xl transition-colors flex items-center gap-2">
                <i class="ph-fill ph-check-circle"></i> Salvar Produto
            </button>
            <a href="index.php?pg=produtos" class="bg-zinc-900 hover:bg-zinc-800 text-zinc-300 text-xs font-black uppercase tracking-widest px-8 py-3 rounded-xl transition-colors flex items-center gap-2">
                Cancelar
            </a>
        </div>

    </form>
</div>