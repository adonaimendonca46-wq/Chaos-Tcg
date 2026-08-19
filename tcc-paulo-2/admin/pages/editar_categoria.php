<?php
require_once "../includes/banco_ficticio.php";
$sucesso = null;
$erro = null;
//Captura o ID vindo da url e busca o usuario coorespondente
$id = $_GET['id'] ?? null;
$categoria = buscarCategoriaPorId($id);
if(!$categoria) {
    echo "<h2 class='text-x1 font-bold text-red-500 p-6'>Categoria não encontrado</h2>";
    exit;
}
//Intercepta o formulário de alteração (POST)
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars(trim($_POST['nome_categoria'] ?? ''));
       if(empty($nome)) {
        $erro = "Nome é obrigatório.";
    } else {
        //Verifica se o novo login já pertence a outro usuario para evitar colisões
        $todasCategorias = listarCategorias();
        $nomeDuplicado = false;
        foreach ($todasCategorias as $u) {
            if (strtolower($u['nome']) === strtolower($nome) && $u['id'] != $id){
                $nomeDuplicado = true;
                break;
            }
        }
        if ($nomeDuplicado) {
            $erro = "Nome da categoria ja esta em uso";
        } else {
            //Prepara o pacote de dados atualizados
            $dadosParaAtualizar = [
                "nome" => $nome,
 
            ];
           
            if(atualizarCategoria($id, $dadosParaAtualizar)) {
                $sucesso = "Dados da categoria atualizados com sucesso!";
                //Recarrega os dados novos do usuário na tela
                $categoria = buscarCategoriaPorId($id);
            } else {
                $erro = "Falha técnica ao salvar as alterações.";
            }
        }
    }
}

?>

<div class="max-w-xl mx-auto mt-8 bg-zinc-950 p-8 rounded-3xl shadow-sm border border-red-900/30">
    
    <div class="flex items-center justify-between mb-6">
        <div>
            <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest block mb-1">Painel Admin</span>
            <h1 class="text-xl font-black text-white uppercase tracking-tighter">Editar Categoria<span class="text-red-600">.</span></h1>
        </div>
        <a href="index.php?pg=categorias" class="text-xs text-gray-500 hover:text-red-600 font-bold transition flex items-center gap-1">
            <i class="ph ph-arrow-left"></i> Voltar para a lista
        </a>
    </div>

    <hr class="border-zinc-800 mb-6">

    <?php if ($sucesso): ?>
        <div class="bg-green-950/40 text-green-400 p-4 rounded-xl text-xs font-bold mb-6 flex items-center gap-2 border border-green-800/60">
            <i class="ph ph-check-circle text-base"></i> <?php echo $sucesso; ?>
        </div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="bg-red-950/40 text-red-600 p-4 rounded-xl text-xs font-bold mb-6 flex items-center gap-2 border border-red-800/60">
            <i class="ph ph-warning-circle text-base"></i> <?php echo $erro; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
        <div>
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 block mb-2">Nome da Categoria</label>
            <input type="text" 
                   name="nome_categoria" 
                   value="<?php echo htmlspecialchars($categoria['nome']); ?>" 
                   required 
                   placeholder="Ex: Teclados Mecânicos"
                   class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-3.5 text-sm font-medium focus:border-red-600 focus:bg-zinc-950 outline-none transition">
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit" class="flex-grow bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-4 rounded-xl transition shadow-md shadow-red-900/30 text-sm flex items-center justify-center gap-2">
                <i class="ph ph-floppy-disk"></i> Salvar Alterações
            </button>
            <a href="index.php?pg=categorias" class="bg-zinc-800 hover:bg-zinc-700 text-gray-600 font-bold py-3.5 px-6 rounded-xl transition text-sm text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>