<?php
require_once "../includes/banco_ficticio.php";
$sucesso = null;
$erro = null;
//Captura o ID vindo da url e busca o usuario coorespondente
$id = $_GET['id'] ?? null;
$usuario = buscarUsuarioPorId($id);
if(!$usuario) {
    echo "<h2 class='text-x1 font-bold text-red-500 p-6'>Administrador não encontrado</h2>";
    exit;
}
//Intercepta o formulário de alteração (POST)
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars(trim($_POST['nome_usuario'] ?? ''));
    $login = htmlspecialchars(trim($_POST['login_usuario'] ?? ''));
    $senhaNova = $_POST['senha_usuario'] ?? ''; // Pode vir vazia caso ele não queira trocar a senha
    if(empty($nome) || empty($login)) {
        $erro = "Nome e Usuário de Login são campos obrigatórios.";
    } else {
        //Verifica se o novo login já pertence a outro usuario para evitar colisões
        $todosUsuarios = listarUsuarios();
        $loginDuplicado = false;
        foreach ($todosUsuarios as $u) {
            if (strtolower($u['login']) === strtolower($login) && $u['id'] != $id){
                $loginDuplicado = true;
                break;
            }
        }
        if ($loginDuplicado) {
            $erro = "Esse login já está sendo utilizado por outro administrador";
        } else {
            //Prepara o pacote de dados atualizados
            $dadosParaAtualizar = [
                "nome" => $nome,
                "login" => $login
            ];
            //Se o campo de senha não estiver vazio, adicionamos a nova senha no pacote
            if (!empty($senhaNova)) {
                $dadosParaAtualizar['senha'] = $senhaNova;
            }
            if(atualizarUsuario($id, $dadosParaAtualizar)) {
                $sucesso = "Dados do administrador atualizados com sucesso!";
                //Recarrega os dados novos do usuário na tela
                $usuario = buscarUsuarioPorId($id);
            } else {
                $erro = "Falha técnica ao salvar as alterações.";
            }
        }
    }
}

?>

<div class="mb-8">
    <a href="index.php?pg=usuarios" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-600 mb-4 transition">
        <i class="ph ph-arrow-left"></i> Voltar para a equipe
    </a>
    <h1 class="text-3xl font-black text-white">Editar Administrador</h1>
    <p class="text-gray-500 text-sm">Modifique as credenciais de @<?php echo $usuario['login']; ?>.</p>
</div>

<div class="bg-zinc-950 border border-red-900/30 rounded-3xl p-8 max-w-xl shadow-sm">

    <?php if ($sucesso): ?>
        <div class="mb-6 bg-green-950/40 border border-green-800/60 text-green-400 p-4 rounded-xl text-sm flex items-center gap-3">
            <i class="ph ph-check-circle text-xl text-green-400"></i>
            <div><?php echo $sucesso; ?></div>
        </div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="mb-6 bg-red-950/40 border border-red-800/60 text-red-400 p-4 rounded-xl text-sm flex items-center gap-3">
            <i class="ph ph-warning-circle text-xl text-red-600"></i>
            <div><?php echo $erro; ?></div>
        </div>
    <?php endif; ?>

    <form action="index.php?pg=editar_usuario&id=<?php echo $usuario['id']; ?>" method="POST" class="space-y-6">
        
        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nome Completo</label>
            <input type="text" name="nome_usuario" value="<?php echo $usuario['nome']; ?>" required class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-3 text-sm focus:outline-none focus:border-red-600 focus:bg-zinc-950 transition">
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Usuário de Login</label>
            <input type="text" name="login_usuario" value="<?php echo $usuario['login']; ?>" required class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-3 text-sm focus:outline-none focus:border-red-600 focus:bg-zinc-950 transition">
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nova Senha (Deixe em branco para NÃO alterar)</label>
            <input type="password" name="senha_usuario" placeholder="Digite apenas se quiser mudar a atual..." class="w-full bg-zinc-900 border border-zinc-700 rounded-xl p-3 text-sm focus:outline-none focus:border-red-600 focus:bg-zinc-950 transition">
        </div>

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
            <i class="ph ph-check"></i> Atualizar Cadastro
        </button>
    </form>
</div>