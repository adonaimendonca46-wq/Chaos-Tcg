<?php 
require_once "../includes/banco_ficticio.php";
$erro = null;
$sucesso = null;

if(ISSET($_GET['acao']) && $_GET['acao'] === 'status' && isset($_GET['id'])){
    $idStatus = intval($_GET['id']);
    $userStatus = buscarUsuarioPorId($idStatus);

    if($userStatus) {
        $usuariosCadastrados = listarUsuarios();
        foreach($usuariosCadastrados as $uc) {
            if($uc['id'] == $idStatus && $_SESSION['usuario_nome'] === $uc['nome']) {
                $erro = "Voce nao pode inativar sua propria conta burro";
                $idStatus = null;
                break;
            }
        }
        if($idStatus) {
            $novoStatus = isset($userStatus['ativo']) ? !$userStatus['ativo'] : false;
            atualizarUsuario($idStatus, ['ativo' => $novoStatus]);
            header("Location: index.php?pg=usuarios");
            exit;
        }
    }
}

// Processa o envio do formulário de novo cadastro (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars(trim($_POST['nome_usuario'] ?? ''));
    $login = htmlspecialchars(trim($_POST['login_usuario'] ?? ''));
    $senha = $_POST['senha_usuario'] ?? '';

    if (empty($nome) || empty($login) || empty($senha)) {
        $erro = "Todos os campos são obrigatórios para o cadastro.";
    } else {
        $usuariosExistentes = listarUsuarios();
        $loginJaExiste = false;
        
        foreach ($usuariosExistentes as $u) {
            if (strtolower($u['login']) === strtolower($login)) {
                $loginJaExiste = true;
                break;
            }
        }

        if ($loginJaExiste) {
            $erro = "Este usuário de login já está em uso por outro administrador.";
        } else {
            $novoAdmin = [
                "nome" => $nome,
                "login" => $login,
                "senha" => $senha,
                "ativo" => true // Todo usuário já nasce ativo
            ];

            if (salvarUsuario($novoAdmin)) {
                $sucesso = "Administrador <strong>$nome</strong> cadastrado com sucesso!";
                $_POST = array(); 
            } else {
                $erro = "Erro técnico ao tentar salvar o usuário.";
            }
        }
    }
}
$usuarios = listarUsuarios();
?>
<div class="mb-10">
    <h1 class="text-3xl font-black text-white">Administradores do Sistema</h1>
    <p class="text-gray-500 text-sm">Gerencie quem tem acesso ao painel de controle da loja.</p>
</div>

<?php if ($erro && !isset($_POST['nome_usuario'])): ?>
    <div class="mb-6 bg-red-950/40 border border-red-800/60 text-red-400 p-4 rounded-xl text-sm flex items-center gap-3">
        <i class="ph ph-warning-circle text-xl text-red-600"></i>
        <div><?php echo $erro; ?></div>
    </div>
<?php endif; ?>

<div class="space-y-8">

    <!-- Cadastro -->
    <div class="bg-zinc-950 rounded-3xl border border-red-900/30 shadow-sm p-6">

        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-red-900/30 flex items-center justify-center">
                <i class="ph ph-user-plus text-xl text-red-600"></i>
            </div>

            <div>
                <h2 class="font-bold text-white text-lg">
                    Novo Administrador
                </h2>

                <p class="text-sm text-gray-500">
                    Cadastre um novo usuário para acessar o sistema.
                </p>
            </div>
        </div>

        <?php if ($sucesso): ?>
            <div class="mb-5 bg-green-950/40 border border-green-800/60 text-green-400 p-4 rounded-xl text-sm font-medium">
                <?php echo $sucesso; ?>
            </div>
        <?php endif; ?>

        <?php if ($erro && isset($_POST['nome_usuario'])): ?>
            <div class="mb-5 bg-red-950/40 border border-red-800/60 text-red-400 p-4 rounded-xl text-sm font-medium">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?pg=usuarios" method="POST">

            <div class="grid lg:grid-cols-4 gap-5">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Nome Completo
                    </label>

                    <input
                        type="text"
                        name="nome_usuario"
                        value="<?php echo $_POST['nome_usuario'] ?? ''; ?>"
                        required
                        class="w-full border border-zinc-700 rounded-xl p-3 bg-zinc-900 focus:bg-zinc-950 focus:border-red-600 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Usuário
                    </label>

                    <input
                        type="text"
                        name="login_usuario"
                        value="<?php echo $_POST['login_usuario'] ?? ''; ?>"
                        required
                        class="w-full border border-zinc-700 rounded-xl p-3 bg-zinc-900 focus:bg-zinc-950 focus:border-red-600 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Senha
                    </label>

                    <input
                        type="password"
                        name="senha_usuario"
                        required
                        class="w-full border border-zinc-700 rounded-xl p-3 bg-zinc-900 focus:bg-zinc-950 focus:border-red-600 outline-none transition">
                </div>

                <div class="flex items-end">

                    <button
                        type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl py-3 flex justify-center items-center gap-2 transition">

                        <i class="ph ph-floppy-disk"></i>

                        Cadastrar

                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- Tabela -->
    <div class="bg-zinc-950 rounded-3xl border border-red-900/30 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-red-900/30 flex items-center justify-between">

            <div>

                <h2 class="font-bold text-lg text-white">
                    Administradores Cadastrados
                </h2>

                <p class="text-sm text-gray-500">
                    Lista de usuários com acesso ao painel.
                </p>

            </div>

            <span class="bg-red-950/40 text-red-400 px-3 py-1 rounded-full text-sm font-semibold">
                <?php echo count($usuarios); ?> usuários
            </span>

        </div>

        <table class="w-full">

            <thead class="bg-zinc-900">

                <tr>

                    <th class="p-4 text-left text-xs uppercase tracking-wider text-gray-500">
                        Nome
                    </th>

                    <th class="p-4 text-left text-xs uppercase tracking-wider text-gray-500">
                        Login
                    </th>

                    <th class="p-4 text-left text-xs uppercase tracking-wider text-gray-500">
                        Status
                    </th>

                    <th class="p-4 text-right text-xs uppercase tracking-wider text-gray-500">
                        Ações
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-zinc-800">

                <?php foreach ($usuarios as $user):
                    $userAtivo = isset($user['ativo']) ? $user['ativo'] : true;
                ?>

                <tr class="hover:bg-zinc-900 transition <?php echo !$userAtivo ? 'opacity-60' : ''; ?>">

                    <td class="p-4 font-semibold text-gray-200">
                        <?php echo $user['nome']; ?>
                    </td>

                    <td class="p-4 text-gray-500">
                        @<?php echo $user['login']; ?>
                    </td>

                    <td class="p-4">

                        <?php if($userAtivo): ?>

                            <span class="bg-green-900/40 text-green-400 border border-green-800/60 px-3 py-1 rounded-full text-xs font-bold">
                                Ativo
                            </span>

                        <?php else: ?>

                            <span class="bg-zinc-800 text-gray-400 border border-zinc-700 px-3 py-1 rounded-full text-xs font-bold">
                                Inativo
                            </span>

                        <?php endif; ?>

                    </td>

                    <td class="p-4 text-right">

                        <a href="index.php?pg=editar_usuario&id=<?php echo $user['id']; ?>"
                           class="inline-flex p-2 rounded-lg hover:bg-blue-950/40 text-blue-400 transition">

                            <i class="ph ph-pencil-simple text-xl"></i>

                        </a>

                        <a href="index.php?pg=usuarios&acao=status&id=<?php echo $user['id']; ?>"
                           class="inline-flex p-2 rounded-lg transition <?php echo $userAtivo ? 'hover:bg-amber-950/40 text-amber-600' : 'hover:bg-green-950/40 text-green-400'; ?>">

                            <i class="ph <?php echo $userAtivo ? 'ph-user-minus' : 'ph-user-switch'; ?> text-xl"></i>

                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>