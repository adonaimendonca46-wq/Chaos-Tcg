<?php
/**
 * pages/pagamento-pix.php
 *
 * Página de checkout que gera e exibe o QR Code Pix pro cliente.
 * Coloque este arquivo dentro da pasta pages/ do seu projeto.
 *
 * Exemplo de uso: link o cliente pra cá passando o valor e o ID do pedido,
 * ex: pages/pagamento-pix.php?pedido=1234&valor=49.90
 */

require __DIR__ . '/../includes/pix-config.php';

// Troque isso pelos dados reais do pedido (vindos do banco, sessão, etc.)
$valor       = isset($_GET['valor']) ? (float) $_GET['valor'] : 49.90;
$pedidoId    = $_GET['pedido'] ?? '1234';
$emailCliente = 'cliente@email.com'; // pegue do usuário logado / formulário

$pix = criarCobrancaPix(
    valor: $valor,
    descricao: "Pedido #{$pedidoId}",
    emailCliente: $emailCliente,
    referenciaExterna: $pedidoId
);

if (!$pix['ok']) {
    http_response_code(400);
    echo "Erro ao gerar Pix: " . json_encode($pix['erro']);
    exit;
}

// IMPORTANTE: salve $pix['payment_id'] vinculado ao pedido no seu banco (data/).
// É por esse ID que o webhook vai identificar qual pedido foi pago.
// Ex: salvarPagamentoPendente($pedidoId, $pix['payment_id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pagamento via Pix</title>
</head>
<body>
    <h2>Escaneie o QR Code para pagar</h2>
    <img src="data:image/png;base64,<?= $pix['qr_code_base64'] ?>" width="300" alt="QR Code Pix">

    <p>Ou copie o código abaixo (Pix Copia e Cola):</p>
    <textarea rows="4" cols="60" readonly><?= htmlspecialchars($pix['qr_code_copia_e_cola']) ?></textarea>

    <p><small>ID do pagamento: <?= htmlspecialchars($pix['payment_id']) ?></small></p>
</body>
</html>
