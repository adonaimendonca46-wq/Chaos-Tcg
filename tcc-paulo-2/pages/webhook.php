<?php
/**
 * webhook.php
 *
 * Coloque este arquivo na RAIZ do projeto (mesma pasta do index.php).
 *
 * O Mercado Pago chama essa URL automaticamente quando o status de um
 * pagamento muda (ex: de "pending" para "approved").
 *
 * CONFIGURAÇÃO NO PAINEL:
 *   1. Acesse https://www.mercadopago.com.br/developers/panel/notifications/webhooks
 *   2. Cadastre a URL pública deste arquivo, ex: https://seusite.com/tcc-paulo/webhook.php
 *   3. Selecione o evento "Pagamentos".
 *
 * OBS: enquanto estiver testando local (localhost/XAMPP), o Mercado Pago
 * não consegue chamar seu computador. Pra testar o webhook localmente,
 * use uma ferramenta como o ngrok pra expor seu localhost com uma URL pública.
 */

require __DIR__ . '/includes/pix-config.php';

use MercadoPago\Client\Payment\PaymentClient;

$corpo = json_decode(file_get_contents('php://input'), true);

// O Mercado Pago manda notificações de vários tipos; só nos interessa "payment"
if (isset($corpo['type']) && $corpo['type'] === 'payment' && isset($corpo['data']['id'])) {

    $paymentId = $corpo['data']['id'];

    try {
        $client  = new PaymentClient();
        $payment = $client->get($paymentId);

        if ($payment->status === 'approved') {
            // ✅ Pagamento confirmado automaticamente.
            // Atualize o pedido no seu banco (pasta data/ ou seu MySQL), ex:
            //
            // $pdo->prepare("UPDATE pedidos SET status = 'pago' WHERE mp_payment_id = ?")
            //     ->execute([$paymentId]);

            error_log("Pix aprovado: payment_id={$paymentId}, ref={$payment->external_reference}");
        }

    } catch (\Throwable $e) {
        error_log("Erro ao processar webhook Pix: " . $e->getMessage());
    }
}

// Sempre responda 200 rápido, senão o Mercado Pago fica reenviando a notificação
http_response_code(200);
echo "ok";
