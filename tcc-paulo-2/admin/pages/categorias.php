<?php 
require_once "../includes/banco_ficticio.php";
$erro = null;
$sucesso = null;

// Processa o envio do formulário de novo cadastro (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars(trim($_POST['nome_categoria'] ?? ''));

    if (empty($nome)) {
        $erro = "Todos os campos são obrigatórios para o cadastro.";
    } else {
        $categoriasExistentes = listarCategorias();
        $categoriaJaExiste = false;
        
        foreach ($categoriasExistentes as $u) {
            if (strtolower($u['nome']) === strtolower($nome)) {
                $categoriaJaExiste = true;
                break;
            }
        }

        if ($categoriaJaExiste) {
            $erro = "Esta categoria já está em uso.";
        } else {
            $novaCategoria = [
                "nome" => $nome,
                "ativo" => true // Todo usuário já nasce ativo
            ];

            if (salvarCategoria($novaCategoria)) {
                $sucesso = "Categoria <strong>$nome</strong> cadastrada com sucesso!";
                $_POST = array(); 
            } else {
                $erro = "Erro técnico ao tentar salvar a categoria.";
            }
        }
    }
}
$categorias = listarCategorias();
?>
<div class="mb-10">
    <h1 class="text-3xl font-black text-white">Categorias do Sistema</h1>
    <p class="text-gray-500 text-sm">Cadastre e organize os agrupamentos de produtos da sua loja.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <div class="bg-zinc-950 border border-red-900/30 rounded-3xl p-6 h-fit shadow-sm">
        <h2 class="font-bold text-white text-lg mb-4 flex items-center gap-2">
            <i class="ph ph-plus-circle text-red-600"></i> Nova Categoria
        </h2>

        <?php if ($sucesso): ?>
            <div class="mb-4 bg-green-950/40 border border-green-800/60 text-green-400 p-3 rounded-xl text-xs font-semibold">
                <?php echo $sucesso; ?>
            </div>
        <?php endif; ?>

        <?php if ($erro): ?>
            <div class="mb-4 bg-red-950/40 border border-red-800/60 text-red-400 p-3 rounded-xl text-xs font-semibold">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?pg=categorias" method="POST" class="space-y-4">
            <div class="flex flex-col gap-2">
                <label for="nome_categoria" class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nome da Categoria</label>
                <input type="text" id="nome_categoria" name="nome_categoria" value="<?php echo $_POST['nome_categoria'] ?? ''; ?>" required placeholder="Ex: Componentes, Monitores..."
                       class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-3 text-sm focus:outline-none focus:border-red-600 focus:bg-zinc-950 transition">
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition text-sm shadow-lg shadow-red-900/30 flex items-center justify-center gap-2">
                <i class="ph ph-floppy-disk"></i> Criar Categoria
            </button>
        </form>
    </div>

    <div class="md:col-span-2 bg-zinc-950 border border-red-900/30 rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-zinc-900 border-b border-red-900/30">
                <tr>
                    <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">ID</th>
                    <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nome da Categoria</th>
                    <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="3" class="p-8 text-center text-sm text-gray-500">Nenhuma categoria cadastrada ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categorias as $cat): ?>
                    <tr class="hover:bg-zinc-900 transition">
                        <td class="p-4 text-sm text-gray-500 font-mono">#<?php echo $cat['id']; ?></td>
                        <td class="p-4 font-bold text-gray-200"><?php echo $cat['nome']; ?></td>
                        <td class="p-4 text-right space-x-2">
                            <button>
                                <a href="index.php?pg=editar_categoria&id=<?php echo $cat['id']; ?>" class="inline-block text-blue-400 hover:bg-blue-950/40 p-2 rounded-lg transition" title="Editar">
                            <i class="ph ph-pencil-simple text-xl"></i>
                        </a>
                            </button>
                            <button class="text-red-500 hover:bg-red-950/40 p-2 rounded-lg transition" title="Excluir (Em breve)">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>