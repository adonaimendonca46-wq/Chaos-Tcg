<?php
//Inicia o sistema de sessões para podermos 
//verificar quem está acessando
session_start();
//SEGURANÇA: Se a variavel 'logado' não existir na sessão, 
//significa que o usuario nao fez login
if(!isset($_SESSION['logado'])){
    //chuta o usuario de volta para a tela de login
    header('Location: login.php');
    exit;//interrompe o carregamento da pagina por segurança
}

// LOGOFF: Se o link de 'sair' for clicado (index.php?sair=true)
if (isset($_GET['sair'])) {
    // Destrói todas as variáveis gravadas na sessão (apaga o crachá)
    session_destroy();
    // Manda de volta para a tela de login
    header('Location: login.php');
    exit;
}

//ROTEAMENTO: Captura qual pagina o administrador quer ver atras da URL(ex: index.php?pg=produtos)
//Se nao passar na URL, a pagina padrão carregada será a 'dashboard'
$pagina = $_GET['pg'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <title>Painel Administrativo | Chaos TCG</title>
</head>

<body class="bg-black text-white flex flex-col md:flex-row min-h-screen">

    <aside class="w-full md:w-64 bg-zinc-950 border-r border-red-900/30 text-white flex flex-col p-6">
        <h2 class="text-xl font-black mb-10 tracking-tighter">ADMIN<span class="text-red-600">.</span></h2>

        <nav class="flex-grow space-y-2">
            <a href="index.php?pg=dashboard" class="flex items-center gap-3 p-3 rounded-xl transition <?php echo $pagina == 'dashboard' ? 'bg-red-600' : 'hover:bg-zinc-800'; ?>">
                <i class="ph ph-chart-line-up"></i> Dashboard
            </a>
            <a href="index.php?pg=categorias" class="flex items-center gap-3 p-3 rounded-xl transition <?php echo $pagina == 'categorias' ? 'bg-red-600' : 'hover:bg-zinc-800'; ?>">
                <i class="ph ph-tag"></i> Categorias
            </a>
            <a href="index.php?pg=produtos" class="flex items-center gap-3 p-3 rounded-xl transition <?php echo $pagina == 'produtos' ? 'bg-red-600' : 'hover:bg-zinc-800'; ?>">
                <i class="ph ph-package"></i> Produtos
            </a>
            <a href="index.php?pg=fornecedores" class="flex items-center gap-3 p-3 rounded-xl transition <?php echo $pagina == 'fornecedores' ? 'bg-red-600' : 'hover:bg-zinc-800'; ?>">
                <i class="ph ph-users-three"></i> Fornecedores
            </a>
            <a href="index.php?pg=usuarios" class="flex items-center gap-3 p-3 rounded-xl transition <?php echo $pagina == 'usuarios' ? 'bg-red-600' : 'hover:bg-zinc-800'; ?>">
                <i class="ph ph-users-three"></i> Equipe / Usuários
            </a>
        </nav>

        <div class="pt-6 border-t border-zinc-800">
            <p class="text-[10px] text-gray-500 uppercase font-bold mb-4">Usuário: <?php echo $_SESSION['usuario_nome']; ?></p>
            <a href="index.php?sair=true" class="flex items-center gap-3 p-3 rounded-xl text-red-400 hover:bg-red-900/20 transition">
                <i class="ph ph-sign-out"></i> Sair do Sistema
            </a>
        </div>
    </aside>

    <main class="flex-grow p-6 md:p-12 overflow-y-auto bg-black">
        <?php
        // Monta o caminho do arquivo de forma dinâmica (ex: paginas/produtos.php)
        $arquivo = "pages/" . $pagina . ".php";

        // Verifica se o arquivo físico existe dentro da pasta antes de chamá-lo
        if (file_exists($arquivo)) {
            include($arquivo); // Carrega o conteúdo aqui dentro do <main>
        } else {
            // Caso o usuário tente inventar uma página na URL (ex: ?pg=banana)
            echo "<h1 class='text-2xl font-bold text-white'>Página não encontrada no Admin.</h1>";
        }
        ?>
    </main>

</body>

</html>