<?php
/**
 * admin/pages/produtos_excluir.php
 *
 * Remove um produto do produtos.json a partir do ?id= na URL, e volta pra listagem.
 * Ex: index.php?pg=produtos_excluir&id=5
 *
 * OBS: esse arquivo é incluído DENTRO do admin/index.php, que já mandou HTML
 * pro navegador antes de chegar aqui (o menu lateral, etc). Por isso não dá
 * pra usar header('Location: ...') -- os cabeçalhos HTTP já foram enviados.
 * O redirecionamento é feito via JavaScript, que funciona nesse cenário.
 */

$caminho_produtos = __DIR__ . '/../../data/produtos.json';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($id !== null && file_exists($caminho_produtos)) {
    $produtos = json_decode(file_get_contents($caminho_produtos), true);

    if (is_array($produtos)) {
        $produtos = array_values(array_filter($produtos, function ($p) use ($id) {
            return (int) ($p['id'] ?? 0) !== $id;
        }));

        file_put_contents($caminho_produtos, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
?>
<script>
    window.location.replace('index.php?pg=produtos&msg=excluido');
</script>
<noscript>
    <p style="color:#fff;padding:2rem;">
        Produto excluído. <a href="index.php?pg=produtos&msg=excluido" style="color:#dc2626;">Clique aqui para voltar</a>.
    </p>
</noscript>