<?php
    // Inicia a sessão antes de qualquer saída HTML (necessário para o carrinho funcionar).
    // Precisa ser a primeira coisa do arquivo: se algum HTML já tiver sido enviado
    // ao navegador, session_start() falha com "headers already sent".
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Captura a página atual. Padrão: início.
    // basename() evita que alguém injete caminhos (ex: ?pg=../../algum_arquivo)
    // para incluir arquivos fora da pasta pages/.
    $pagina = basename($_GET['pg'] ?? 'inicio');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <title>CHAOS TCG STORE | Cartas, Boosters e Acessórios</title>
</head>
<body class="bg-neutral-950 text-neutral-300 flex flex-col min-h-screen font-sans antialiased selection:bg-red-600 selection:text-white">

    <!-- Header / Navbar -->
    <header class="bg-neutral-900/95 border-b border-red-900/50 sticky top-0 z-50 backdrop-blur-md shadow-[0_4px_30px_rgba(220,38,38,0.1)]">
        <div class="container mx-auto px-6 h-24 flex justify-between items-center">

            <!-- Logo -->
            <a href="index.php?pg=inicio" class="text-3xl font-black tracking-tighter text-white uppercase italic flex items-center gap-3 drop-shadow-md hover:scale-105 transition-transform">
                <img src="logo-chaos.png" alt="Logo da Loja" class="h-12 w-auto">
                CHAOS <span class="text-red-600 font-bold border-l-2 border-red-600 pl-2 ml-1 text-xl">TCG STORE</span>
            </a>

            <!-- Menu de Navegação -->
            <nav>
                <ul class="flex gap-8 text-sm font-bold uppercase tracking-widest text-neutral-400">
                    <li>
                        <a href="index.php?pg=inicio" class="hover:text-red-500 transition-colors duration-300 flex items-center gap-2 <?php echo $pagina == 'inicio' ? 'text-red-500 border-b-2 border-red-500 pb-1' : ''; ?>">
                            <i class="ph ph-house"></i> Início
                        </a>
                    </li>
                    <li>
                        <a href="index.php?pg=historia" class="hover:text-red-500 transition-colors duration-300 flex items-center gap-2 <?php echo $pagina == 'historia' ? 'text-red-500 border-b-2 border-red-500 pb-1' : ''; ?>">
                            <i class="ph ph-book-open-text"></i> Nossa História
                        </a>
                    </li>
                    <li>
                        <a href="index.php?pg=produtos" class="hover:text-red-500 transition-colors duration-300 flex items-center gap-2 <?php echo $pagina == 'produtos' ? 'text-red-500 border-b-2 border-red-500 pb-1' : ''; ?>">
                            <i class="ph ph-sparkle"></i> Cartas & Boosters
                        </a>
                    </li>
                    <li>
                        <a href="index.php?pg=sobre" class="hover:text-red-500 transition-colors duration-300 flex items-center gap-2 <?php echo $pagina == 'sobre' ? 'text-red-500 border-b-2 border-red-500 pb-1' : ''; ?>">
                            <i class="ph ph-scroll"></i> Regras & Eventos
                        </a>
                    </li>
                    <li>
                        <a href="index.php?pg=contato" class="hover:text-red-500 transition-colors duration-300 flex items-center gap-2 <?php echo $pagina == 'contato' ? 'text-red-500 border-b-2 border-red-500 pb-1' : ''; ?>">
                            <i class="ph ph-envelope-simple"></i> Contato
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Área de Conteúdo Dinâmico -->
    <main class="container mx-auto px-6 flex-grow py-12">
        <?php
            $arquivo = "pages/" . $pagina . ".php";

            if (file_exists($arquivo)) {
                include($arquivo);
            } else {
                echo "
                <div class='flex flex-col items-center justify-center py-24 text-center'>
                    <div class='relative'>
                        <i class='ph-fill ph-warning-octagon text-8xl text-red-600 mb-6 drop-shadow-[0_0_15px_rgba(220,38,38,0.5)]'></i>
                        <i class='ph ph-skull absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-3xl text-neutral-950 mb-6'></i>
                    </div>
                    <h1 class='text-4xl font-black text-white uppercase tracking-wider mb-2'>O Caos Venceu!</h1>
                    <p class='text-neutral-400 mb-8 max-w-md'>A página que você procura foi banida para a dimensão das trevas ou simplesmente não existe.</p>
                    <a href='index.php?pg=produtos' class='bg-red-600 text-white px-8 py-3 rounded uppercase font-bold tracking-widest hover:bg-red-700 transition-colors shadow-[0_0_15px_rgba(220,38,38,0.3)]'>
                        Retornar ao Deck
                    </a>
                </div>";
            }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-neutral-900 border-t border-red-900/30 py-12 mt-20 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-3/4 h-1 bg-gradient-to-r from-transparent via-red-600/50 to-transparent"></div>

        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <i class="ph-fill ph-cards text-red-600 text-xl"></i>
                <p class="text-sm font-bold text-neutral-500 uppercase tracking-widest">&copy; 2026 CHAOS TCG. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

</body>
</html>