<?php
require_once "../includes/banco_ficticio.php";

$mensagemSucesso = null;
$mensagemErro = null;

// 1. Processa o cadastro se o formulário for enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosFornecedor = [
        "nome"     => $_POST['nome'] ?? '',
        "cnpj"     => $_POST['cnpj'] ?? '',
        "telefone" => $_POST['telefone'] ?? '',
        "rua"      => $_POST['rua'] ?? '',
        "numero"   => $_POST['numero'] ?? '',
        "bairro"   => $_POST['bairro'] ?? '',
        "cidade"   => $_POST['cidade'] ?? '',
        "estado"   => $_POST['estado'] ?? ''
    ];

    if (empty($dadosFornecedor['nome']) || empty($dadosFornecedor['cnpj'])) {
        $mensagemErro = "Os campos Nome e CNPJ são obrigatórios!";
    } else {
        if (salvarFornecedor($dadosFornecedor)) {
            $mensagemSucesso = "Fornecedor cadastrado com sucesso!";
            // Limpa os campos para o próximo cadastro
            $dadosFornecedor = [];
        } else {
            $mensagemErro = "Erro ao tentar salvar o fornecedor no arquivo JSON.";
        }
    }
}

// 2. Puxa a lista de fornecedores atualizada para exibir na tabela
$fornecedores = listarFornecedores();
?>

<div class="space-y-6">
    <!-- Cabeçalho da Página -->
    <div>
        <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest block mb-1">Painel Admin</span>
        <h1 class="text-2xl font-black text-white uppercase tracking-tighter">Gerenciamento de Fornecedores<span class="text-red-600">.</span></h1>
    </div>

    <!-- Alertas de Feedback Visuais -->
    <?php if ($mensagemSucesso): ?>
        <div class="bg-green-950/40 text-green-400 p-4 rounded-xl text-xs font-bold border border-green-800/60">
            ✅ <?php echo $mensagemSucesso; ?>
        </div>
    <?php endif; ?>

    <?php if ($mensagemErro): ?>
        <div class="bg-red-950/40 text-red-600 p-4 rounded-xl text-xs font-bold border border-red-800/60">
            ❌ <?php echo $mensagemErro; ?>
        </div>
    <?php endif; ?>

    <!-- Layout em Grid Flexível (Formulário à Esquerda, Listagem à Direita) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- COLUNA 1: FORMULÁRIO DE CADASTRO -->
        <div class="bg-zinc-950 p-6 rounded-3xl border border-red-900/30 shadow-sm sticky top-24">
            <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="ph ph-plus-circle text-red-600 text-lg"></i> Novo Fornecedor
            </h2>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-1">Razão Social / Nome</label>
                    <input type="text" name="nome" required placeholder="Ex: Distribuidora Brasil" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-2.5 text-sm focus:border-red-600 focus:bg-zinc-950 outline-none transition" value="<?php echo $dadosFornecedor['nome'] ?? ''; ?>">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-1">CNPJ</label>
                    <input type="text" name="cnpj" required placeholder="00.000.000/0001-00" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-2.5 text-sm focus:border-red-600 focus:bg-zinc-950 outline-none transition" value="<?php echo $dadosFornecedor['cnpj'] ?? ''; ?>">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-1">Telefone</label>
                        <input type="text" name="telefone" placeholder="(00) 00000-0000" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-2.5 text-sm focus:border-red-600 focus:bg-zinc-950 outline-none transition" value="<?php echo $dadosFornecedor['telefone'] ?? ''; ?>">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-1">Rua / Logradouro</label>
                        <input type="text" name="rua" placeholder="Rua..." class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-2.5 text-sm focus:border-red-600 focus:bg-zinc-950 outline-none transition" value="<?php echo $dadosFornecedor['rua'] ?? ''; ?>">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-1">Número</label>
                        <input type="text" name="numero" placeholder="123" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-2.5 text-sm focus:border-red-600 focus:bg-zinc-950 outline-none transition" value="<?php echo $dadosFornecedor['numero'] ?? ''; ?>">
                    </div>
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-1">Bairro</label>
                        <input type="text" name="bairro" placeholder="Bairro" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-2.5 text-sm focus:border-red-600 focus:bg-zinc-950 outline-none transition" value="<?php echo $dadosFornecedor['bairro'] ?? ''; ?>">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-1">Cidade</label>
                        <input type="text" name="cidade" placeholder="Cidade" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-2.5 text-sm focus:border-red-600 focus:bg-zinc-950 outline-none transition" value="<?php echo $dadosFornecedor['cidade'] ?? ''; ?>">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-1">UF</label>
                        <input type="text" name="estado" maxlength="2" placeholder="SP" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-2.5 text-sm focus:border-red-600 focus:bg-zinc-950 outline-none transition uppercase" value="<?php echo $dadosFornecedor['estado'] ?? ''; ?>">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition text-xs shadow-md shadow-red-900/30 flex items-center justify-center gap-2">
                    <i class="ph ph-floppy-disk text-base"></i> Cadastrar Fornecedor
                </button>
            </form>
        </div>

        <!-- COLUNA 2 e 3: LISTAGEM EM TABELA -->
        <div class="lg:col-span-2 bg-zinc-950 rounded-3xl border border-red-900/30 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-zinc-800">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="ph ph-list-bullet text-red-600 text-lg"></i> Fornecedores Cadastrados
                </h2>
            </div>

            <?php if (empty($fornecedores)): ?>
                <div class="text-center py-12">
                    <i class="ph ph-buildings text-4xl text-gray-600 mb-2 block"></i>
                    <p class="text-gray-500 text-sm">Nenhum fornecedor cadastrado até o momento.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-zinc-900 text-[10px] font-bold text-gray-500 uppercase tracking-wider border-b border-red-900/30">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Fornecedor / CNPJ</th>
                                <th class="px-6 py-4">Contato</th>
                                <th class="px-6 py-4">Localização</th>
                                <th class="px-6 py-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            <?php foreach ($fornecedores as $f): ?>
                                <tr class="hover:bg-zinc-900/50 transition">
                                    <td class="px-6 py-4 font-bold text-white">#<?php echo $f['id']; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-white"><?php echo $f['nome']; ?></div>
                                        <div class="text-xs text-gray-500"><?php echo $f['cnpj']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div><?php echo !empty($f['telefone']) ? $f['telefone'] : '---'; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="text-white"><?php echo $f['rua'] . ", " . $f['numero']; ?></div>
                                        <div class="text-gray-500"><?php echo $f['cidade'] . " - " . $f['estado']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                        <a href="index.php?pg=editar_fornecedores&id=<?php echo $f['id']; ?>" class="text-red-500 hover:text-red-300 font-bold text-xs">
                                            Editar
                                        </a>
                                        <a href="index.php?pg=fornecedores_excluir&id=<?php echo $f['id']; ?>" 
                                           onclick="return confirm('Deseja mesmo remover o fornecedor <?php echo $f['nome']; ?>?');" 
                                           class="text-red-500 hover:text-red-400 font-bold text-xs">
                                            Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>