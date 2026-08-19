<!-- Banner Principal -->
<div class="relative bg-gradient-to-br from-neutral-900 via-neutral-950 to-black rounded-lg p-8 md:p-16 flex flex-col md:flex-row items-center justify-between mb-12 border border-red-900/50 shadow-[0_0_30px_rgba(220,38,38,0.15)] overflow-hidden">
    <!-- Efeito de brilho no fundo -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-red-600/10 blur-[100px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-red-600/5 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="max-w-xl relative z-10 text-center md:text-left">
        <span class="text-[10px] font-black text-white bg-red-600 uppercase tracking-widest px-3 py-1 rounded border border-red-500 shadow-[0_0_10px_rgba(220,38,38,0.4)]">
            Bem-vindo
        </span>
        <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white uppercase mt-4 mb-4 drop-shadow-lg">
            CHAOS <span class="text-red-600">TCG STORE</span>
        </h1>
        <p class="text-neutral-400 text-sm md:text-base mb-8 font-medium">
            Cartas, boosters e acessórios selecionados para quem leva o metagame a sério. Encontre as peças que faltam no seu deck e domine a próxima batalha.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
            <a href="index.php?pg=produtos" class="bg-red-600 hover:bg-red-700 text-white text-xs uppercase tracking-widest font-black px-6 py-4 rounded shadow-[0_0_15px_rgba(220,38,38,0.3)] transition-colors flex items-center justify-center gap-2">
                <i class="ph-fill ph-cards-three"></i> Ver Produtos
            </a>
            <a href="index.php?pg=sobre" class="bg-neutral-800 hover:bg-neutral-700 border border-neutral-700 hover:border-red-600/50 text-white text-xs uppercase tracking-widest font-black px-6 py-4 rounded transition-colors flex items-center justify-center gap-2">
                <i class="ph-fill ph-scroll"></i> Regras & Eventos
            </a>
        </div>
    </div>

    <div class="hidden md:block w-1/3 relative z-10">
        <div class="relative group">
            <div class="absolute -inset-1 bg-red-600 blur opacity-20 group-hover:opacity-40 transition duration-500 rounded-lg"></div>
            <img src="pages/axorus_bgwp.png" alt="Carta em destaque" class="relative rounded-lg opacity-80 group-hover:opacity-100 transition-opacity duration-500 object-cover aspect-video border border-red-900/50">
        </div>
    </div>
</div>

<!-- Categorias em Destaque -->
<div class="mb-16">
    <div class="mb-6">
        <span class="text-[10px] font-black text-red-500 uppercase tracking-widest">Explore</span>
        <h2 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight mt-1">O que você procura?</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <a href="index.php?pg=produtos&cat=Cole%C3%A7%C3%A3o%20de%20Treinador" class="bg-neutral-900 border border-neutral-800 rounded-lg overflow-hidden group hover:shadow-[0_0_20px_rgba(220,38,38,0.2)] hover:border-red-600/50 transition-all duration-500 relative p-6 block">
            <div class="absolute top-0 left-0 w-full h-1 bg-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
            <i class="ph-fill ph-package text-red-600 text-2xl mb-3 block"></i>
            <h3 class="font-black text-white uppercase tracking-wide text-base group-hover:text-red-500 transition-colors">Coleção de Treinador</h3>
            <p class="text-neutral-400 text-sm mt-2 leading-relaxed">Boxes completos com boosters, acessórios e cartas exclusivas.</p>
        </a>
        <a href="index.php?pg=produtos&cat=Cartas%20Avulsas" class="bg-neutral-900 border border-neutral-800 rounded-lg overflow-hidden group hover:shadow-[0_0_20px_rgba(220,38,38,0.2)] hover:border-red-600/50 transition-all duration-500 relative p-6 block">
            <div class="absolute top-0 left-0 w-full h-1 bg-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
            <i class="ph-fill ph-cards-three text-red-600 text-2xl mb-3 block"></i>
            <h3 class="font-black text-white uppercase tracking-wide text-base group-hover:text-red-500 transition-colors">Cartas Avulsas</h3>
            <p class="text-neutral-400 text-sm mt-2 leading-relaxed">As peças certas para fechar sua estratégia de deck.</p>
        </a>
        <a href="index.php?pg=produtos&cat=Latas" class="bg-neutral-900 border border-neutral-800 rounded-lg overflow-hidden group hover:shadow-[0_0_20px_rgba(220,38,38,0.2)] hover:border-red-600/50 transition-all duration-500 relative p-6 block">
            <div class="absolute top-0 left-0 w-full h-1 bg-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
            <i class="ph-fill ph-gift text-red-600 text-2xl mb-3 block"></i>
            <h3 class="font-black text-white uppercase tracking-wide text-base group-hover:text-red-500 transition-colors">Latas</h3>
            <p class="text-neutral-400 text-sm mt-2 leading-relaxed">Edições especiais com brindes e cartas de destaque.</p>
        </a>
    </div>
</div>

<!-- Por que comprar aqui -->
<div class="mb-4">
    <div class="mb-6">
        <span class="text-[10px] font-black text-red-500 uppercase tracking-widest">Vantagens</span>
        <h2 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight mt-1">Por que jogar do nosso lado</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="flex gap-3 items-start">
            <i class="ph-fill ph-shield-check text-red-600 text-2xl mt-0.5 shrink-0"></i>
            <div>
                <h4 class="font-black text-white uppercase text-sm tracking-wide">Cartas Originais</h4>
                <p class="text-neutral-500 text-sm mt-1">Produtos 100% licenciados, sem falsificações.</p>
            </div>
        </div>
        <div class="flex gap-3 items-start">
            <i class="ph-fill ph-truck text-red-600 text-2xl mt-0.5 shrink-0"></i>
            <div>
                <h4 class="font-black text-white uppercase text-sm tracking-wide">Envio Rápido</h4>
                <p class="text-neutral-500 text-sm mt-1">Pedido embalado e despachado com cuidado.</p>
            </div>
        </div>
        <div class="flex gap-3 items-start">
            <i class="ph-fill ph-headset text-red-600 text-2xl mt-0.5 shrink-0"></i>
            <div>
                <h4 class="font-black text-white uppercase text-sm tracking-wide">Suporte Direto</h4>
                <p class="text-neutral-500 text-sm mt-1">Dúvidas? Fale com a gente pelo WhatsApp.</p>
            </div>
        </div>
    </div>
</div>
