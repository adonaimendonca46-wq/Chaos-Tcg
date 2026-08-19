<?php
/**
 * pages/ajax-gerar-pix.php
 *
 * Endpoint chamado via JavaScript (fetch) quando o cliente clica em
 * "Gerar QR Code" na tela de finalizar compra. Gera uma cobrança Pix
 * REAL no Mercado Pago, com o valor exato da compra, e devolve o
 * QR Code (imagem) + Pix Copia e Cola em JSON.
 *
 * Coloque este arquivo dentro da pasta pages/, ao lado do finalizar.php.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/pix-config.php';

$dados = json_decode(file_get_contents('php://input'), true);

$valor = isset($dados['valor']) ? (float) $dados['valor'] : 0;
$nome  = trim($dados['nome'] ?? '');
$cpf   = trim($dados['cpf'] ?? '');
$email = trim($dados['email'] ?? '');

if ($valor <= 0 || $nome === '' || $cpf === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'erro' => 'Dados inválidos para gerar o Pix.']);
    exit;
}

// O Mercado Pago exige e-mail do pagador; se o cliente não preencheu, usamos um genérico
if ($email === '') {
    $email = 'cliente@exemplo.com';
}

// Referência única pra você conseguir achar esse pedido depois (ex: no webhook)
$referencia = uniqid('pedido_');

$pix = criarCobrancaPix(
    valor: $valor,
    descricao: "Compra Chaos TCG Store - {$nome}",
    emailCliente: $email,
    referenciaExterna: $referencia
);

// $pix já vem no formato ['ok' => true/false, 'qr_code_base64' => ..., 'qr_code_copia_e_cola' => ..., 'payment_id' => ...]
echo json_encode($pix);
