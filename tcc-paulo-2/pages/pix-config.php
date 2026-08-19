<?php
/**
 * includes/pix-config.php
 *
 * Configuração da API do Mercado Pago + função para gerar cobrança Pix.
 * Coloque este arquivo dentro da pasta includes/ do seu projeto.
 *
 * Depende do composer instalado na RAIZ do projeto (tcc-paulo/vendor).
 */

require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

MercadoPagoConfig::setAccessToken('APP_USR-xxxxxxxx-xxxxxx-xxxxxxxxxxxxxxxxxxxxxxxx-xxxxxxxxx');

/**
 * Cria uma cobrança Pix e retorna os dados do QR Code.
 *
 * @param float  $valor              Valor da transação, ex: 49.90
 * @param string $descricao          Descrição do pedido, ex: "Pedido #1234"
 * @param string $emailCliente       E-mail do cliente (obrigatório pela API)
 * @param string $referenciaExterna  Um ID seu (número do pedido) pra rastrear depois
 */
function criarCobrancaPix(float $valor, string $descricao, string $emailCliente, string $referenciaExterna): array
{
    $client = new PaymentClient();

    $request = [
        "transaction_amount" => $valor,
        "description"        => $descricao,
        "payment_method_id"  => "pix",
        "external_reference" => $referenciaExterna,
        "payer" => [
            "email" => $emailCliente,
        ],
    ];

    try {
        $payment = $client->create($request);

        return [
            "ok"                    => true,
            "payment_id"            => $payment->id,
            "status"                => $payment->status, // "pending" até o cliente pagar
            "qr_code_base64"        => $payment->point_of_interaction->transaction_data->qr_code_base64,
            "qr_code_copia_e_cola"  => $payment->point_of_interaction->transaction_data->qr_code,
        ];
    } catch (\MercadoPago\Exceptions\MPApiException $e) {
        return [
            "ok"   => false,
            "erro" => $e->getApiResponse()->getContent(),
        ];
    }
}
