<?php
/**
 * pages/historia.php
 *
 * Página institucional contando a história por trás da Chaos TCG Store.
 */
?>

<!-- Cabeçalho da página -->
<div class="text-center max-w-2xl mx-auto mb-16">
    <span class="text-red-600 text-xs font-black uppercase tracking-widest flex items-center justify-center gap-2 mb-3">
        <i class="ph-fill ph-scroll"></i> Nossa História
    </span>
    <h1 class="text-4xl md:text-5xl font-black tracking-tighter text-white uppercase mb-4">
        De Onde Vem o <span class="text-red-600">Chaos</span>
    </h1>
    <p class="text-neutral-400 text-sm md:text-base">
        A jornada de dois duelistas que transformaram uma paixão em uma loja.
    </p>
</div>

<!-- Corpo da história -->
<div class="max-w-3xl mx-auto space-y-6 text-neutral-300 leading-relaxed">

    <div class="bg-neutral-900/60 border border-red-900/30 rounded-lg p-8 md:p-10 shadow-lg">
        <p class="mb-5">
            A <strong class="text-white">Chaos TCG Store</strong> nasceu da amizade entre
            <strong class="text-red-500">Adonai</strong> e <strong class="text-red-500">Matheus</strong>,
            dois colecionadores que cresceram abrindo boosters, trocando cartas no recreio e
            discutindo estratégias de deck até tarde da noite. O que começou como uma coleção
            pessoal foi crescendo pasta por pasta, caixa por caixa, até virar bem mais do que um hobby.
        </p>

        <p class="mb-5">
            Cansados de correr atrás de lojas que nunca tinham as expansões mais recentes, ou que
            vendiam produtos sem procedência, Adonai e Matheus tiveram uma ideia simples:
            <em class="text-neutral-200">"e se a gente construísse a loja que a gente sempre quis encontrar?"</em>
            Foi esse pensamento que deu origem à Chaos TCG Store — um espaço pensado por duelistas,
            para duelistas.
        </p>

        <p class="mb-5">
            O nome "Chaos" não foi escolhido por acaso. Para os dois fundadores, cada partida de
            TCG é um pequeno caos controlado: baralhos embaralhados, estratégias que mudam a cada
            carta comprada, e a emoção de nunca saber exatamente o que vem a seguir. É essa mesma
            energia que eles quiseram trazer para a loja — um lugar vivo, cheio de surpresas boas,
            onde cada booster aberto pode mudar o rumo do seu deck.
        </p>

        <p>
            Hoje, a Chaos TCG Store segue com o mesmo compromisso do primeiro dia: produtos
            originais, preços justos e o mesmo cuidado que Adonai e Matheus sempre quiseram receber
            como clientes. Seja você um iniciante montando seu primeiro deck ou um veterano
            caçando aquela carta rara que falta na coleção, essa loja foi feita pensando em você.
        </p>
    </div>

    <!-- Cards dos fundadores -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
        <div class="bg-neutral-900 border border-red-900/30 rounded-lg p-6 text-center hover:border-red-600/50 transition-colors">
            <div class="w-16 h-16 mx-auto rounded-full bg-red-600/20 border border-red-600/40 flex items-center justify-center text-red-500 text-xl font-black mb-4">
                A
            </div>
            <h3 class="text-white font-black uppercase tracking-wide mb-1">Adonai</h3>
            <p class="text-neutral-500 text-xs uppercase tracking-widest font-bold">Fundador</p>
        </div>
        <div class="bg-neutral-900 border border-red-900/30 rounded-lg p-6 text-center hover:border-red-600/50 transition-colors">
            <div class="w-16 h-16 mx-auto rounded-full bg-red-600/20 border border-red-600/40 flex items-center justify-center text-red-500 text-xl font-black mb-4">
                M
            </div>
            <h3 class="text-white font-black uppercase tracking-wide mb-1">Matheus</h3>
            <p class="text-neutral-500 text-xs uppercase tracking-widest font-bold">Cofundador</p>
        </div>
    </div>

    <!-- Chamada final -->
    <div class="text-center pt-10">
        <a href="index.php?pg=produtos" class="bg-red-600 hover:bg-red-500 text-white px-8 py-4 rounded uppercase font-black tracking-widest transition-colors shadow-[0_0_20px_rgba(220,38,38,0.3)] inline-flex items-center gap-2">
            <i class="ph-fill ph-sparkle"></i> Conhecer os Produtos
        </a>
    </div>

</div>
