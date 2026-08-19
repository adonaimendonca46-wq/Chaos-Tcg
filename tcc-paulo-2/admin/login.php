<?php
session_start();

//Importamos o centralizador (Repare que o caminho aqui muda pois o login está na raiz da pasta admin/)
require_once "../includes/banco_ficticio.php";

$erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioDigitado = htmlspecialchars(trim($_POST['usuario'] ?? ''));
    $senhaDigitada = $_POST['senha'] ?? '';

    //1. Puxamos a lista de usuarios reais gravados no arquivo JSON
    $usuariosCadastrados = listarUsuarios();
    $usuarioEncontrado = null;

    //2. Procuramos se existe algum usuario com o login digitado
    foreach ($usuariosCadastrados as $u) {
        if (strtolower($u['login']) === strtolower($usuarioDigitado)) {
            $usuarioEncontrado = $u;
            break;
        }
    }
    if ($usuarioEncontrado && password_verify($senhaDigitada, $usuarioEncontrado['senha'])) {
        //verificação adicional: o usuario esta ativo no sistema?
        $usuarioAtivo = isset($usuarioEncontrado['ativo']) ? $usuarioEncontrado['ativo'] : true;
        if(!$usuarioAtivo) {
            $erro = "Esta conta foi desativada";
        } else {
            //Se estiver ativo e a senha estiver correta, loga com sucesso!!
            $_SESSION['logado'] = true;
            $_SESSION['usuario_nome'] = $usuarioEncontrado['nome'];

            header('Location: index.php');
            exit;
        } 
    }else {
        $erro = "Usuario ou senha invalidos";
    }    
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login | Chaos TCG Admin</title>
</head>

<body class="bg-black">

<div class="min-h-screen grid lg:grid-cols-2">

    <!-- COLUNA ESQUERDA -->
    <div class="hidden lg:block relative">

        <img
            src="rayquaza-bg.png"
            class="absolute inset-0 w-full h-full object-cover"
            alt="">

        <div class="absolute inset-0 bg-gradient-to-br from-black/90 via-red-950/80 to-black/90"></div>

        <div class="relative h-full flex flex-col justify-between p-16 text-white">

            <div>

                <div class="w-20 h-20 rounded-2xl bg-red-600/20 backdrop-blur border border-red-600/40 flex items-center justify-center overflow-hidden">
    <img
        src="logo-chaos.png"
        alt="Chaos TCG"
        class="w-14 h-14 object-contain">
</div>

                <h1 class="mt-8 text-5xl font-black text-white">
                    Chaos <span class="text-red-600">TCG</span>
                </h1>

                <p class="mt-4 text-xl text-red-100 max-w-lg leading-relaxed">
                    Gerencie sua loja virtual Chaos TCG de forma simples, rápida e segura.
                </p>

            </div>

            <div class="text-sm text-gray-400">
                Sistema Administrativo © <?php echo date('Y'); ?>
            </div>

        </div>

    </div>


    <!-- COLUNA DIREITA -->
    <div class="flex items-center justify-center px-6 py-10 bg-black">

        <div class="w-full max-w-md">

            <div class="mb-10 lg:hidden text-center">

                <div class="mx-auto w-20 h-20 rounded-2xl bg-red-600 flex items-center justify-center text-4xl text-white">
                    🛍️
                </div>

                <h1 class="mt-5 text-3xl font-black text-white">
                    Chaos <span class="text-red-600">TCG</span>
                </h1>

                <p class="text-gray-400 mt-2">
                    Painel Administrativo
                </p>

            </div>

            <div class="bg-zinc-950 rounded-3xl shadow-2xl shadow-red-900/20 border border-red-900/40 p-10">

                <div class="mb-8">

                    <h2 class="text-3xl font-bold text-white">
                        Entrar
                    </h2>

                    <p class="text-gray-400 mt-2">
                        Informe seu usuário e senha para acessar o painel.
                    </p>

                </div>

                <?php if ($erro): ?>

                    <div class="mb-6 rounded-xl bg-red-950/60 border border-red-600 p-4 text-red-400 text-sm font-semibold">
                        <?php echo $erro; ?>
                    </div>

                <?php endif; ?>

                <form method="POST" class="space-y-6">

                    <div>

                        <label class="block mb-2 text-sm font-semibold text-gray-300">
                            Usuário
                        </label>

                        <input
                            type="text"
                            name="usuario"
                            required
                            placeholder="Digite seu usuário"
                            class="w-full rounded-xl border border-zinc-700 bg-zinc-900 text-white placeholder-gray-500 px-4 py-3 focus:ring-4 focus:ring-red-900/40 focus:border-red-600 outline-none transition">

                    </div>

                    <div>

                        <label class="block mb-2 text-sm font-semibold text-gray-300">
                            Senha
                        </label>

                        <input
                            type="password"
                            name="senha"
                            required
                            placeholder="Digite sua senha"
                            class="w-full rounded-xl border border-zinc-700 bg-zinc-900 text-white placeholder-gray-500 px-4 py-3 focus:ring-4 focus:ring-red-900/40 focus:border-red-600 outline-none transition">

                    </div>

                    <button
                        type="submit"
                        class="w-full py-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold transition shadow-lg shadow-red-900/40">

                        Entrar no Painel

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>

</html>