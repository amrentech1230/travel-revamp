<?php
/**
 * Authorize.Net Payment Gateway Class - TravenzoTravel
 * Handles credit card charges, refunds, and transaction queries
 */
class AuthorizeNet
{
    private $loginId;
    private $transactionKey;
    private $endpoint;

    public function __construct()
    {
        $this->loginId        = AUTHNET_LOGIN_ID;
        $this->transactionKey = AUTHNET_TRANSACTION_KEY;
        $this->endpoint       = AUTHNET_ENDPOINT;
    }

    // ─── Charge (Auth + Capture) ─────────────────────────────────────

    /**
     * Charge a credit card
     *
     * @param array $cardData  Card details (number, expiry, cvv)
     * @param float $amount    Amount to charge
     * @param array $billing   Billing address
     * @param array $orderInfo Order details (invoice, description)
     * @return array Result with success status, transaction ID, etc.
     */
    public function charge($cardData, $amount, $billing = [], $orderInfo = [])
    {
        $payload = [
            'createTransactionRequest' => [
                'merchantAuthentication' => [
                    'name'           => $this->loginId,
                    'transactionKey' => $this->transactionKey,
                ],
                'transactionRequest' => [
                    'transactionType' => 'authCaptureTransaction',
                    'amount'          => number_format($amount, 2, '.', ''),
                    'payment'         => [
                        'creditCard' => [
                            'cardNumber'     => preg_replace('/\D/', '', $cardData['number']),
                            'expirationDate' => $cardData['expiry'], // Format: YYYY-MM or MMYY
                            'cardCode'       => $cardData['cvv'],
                        ],
                    ],
                    'order' => [
                        'invoiceNumber' => $orderInfo['invoice'] ?? generateRef('INV'),
                        'description'   => $orderInfo['description'] ?? 'Flight Booking - TravenzoTravel',
                    ],
                    'billTo' => [
                        'firstName' => $billing['first_name'] ?? '',
                        'lastName'  => $billing['last_name'] ?? '',
                        'address'   => $billing['address'] ?? '',
                        'city'      => $billing['city'] ?? '',
                        'state'     => $billing['state'] ?? '',
                        'zip'       => $billing['zip'] ?? '',
                        'country'   => $billing['country'] ?? 'US',
                    ],
                    'customerIP' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                ],
            ],
        ];

        $response = $this->sendRequest($payload);
        return $this->parseChargeResponse($response);
    }

    // ─── Refund ──────────────────────────────────────────────────────

    /**
     * Refund a previously charged transaction
     *
     * @param string $transactionId Original Authorize.Net transaction ID
     * @param float  $amount        Amount to refund
     * @param string $lastFour      Last 4 digits of card
     * @param string $expiry        Card expiration date
     * @return array Result
     */
    public function refund($transactionId, $amount, $lastFour, $expiry)
    {
        $payload = [
            'createTransactionRequest' => [
                'merchantAuthentication' => [
                    'name'           => $this->loginId,
                    'transactionKey' => $this->transactionKey,
                ],
                'transactionRequest' => [
                    'transactionType' => 'refundTransaction',
                    'amount'          => number_format($amount, 2, '.', ''),
                    'payment'         => [
                        'creditCard' => [
                            'cardNumber'     => $lastFour,
                            'expirationDate' => $expiry,
                        ],
                    ],
                    'refTransId' => $transactionId,
                ],
            ],
        ];

        $response = $this->sendRequest($payload);
        return $this->parseRefundResponse($response);
    }

    // ─── Void (Cancel before settlement) ─────────────────────────────

    /**
     * Void an unsettled transaction
     */
    public function voidTransaction($transactionId)
    {
        $payload = [
            'createTransactionRequest' => [
                'merchantAuthentication' => [
                    'name'           => $this->loginId,
                    'transactionKey' => $this->transactionKey,
                ],
                'transactionRequest' => [
                    'transactionType' => 'voidTransaction',
                    'refTransId'      => $transactionId,
                ],
            ],
        ];

        $response = $this->sendRequest($payload);
        return $this->parseVoidResponse($response);
    }

    // ─── Transaction Details ─────────────────────────────────────────

    /**
     * Get transaction details
     */
    public function getTransaction($transactionId)
    {
        $payload = [
            'getTransactionDetailsRequest' => [
                'merchantAuthentication' => [
                    'name'           => $this->loginId,
                    'transactionKey' => $this->transactionKey,
                ],
                'transId' => $transactionId,
            ],
        ];

        $response = $this->sendRequest($payload);
        return $response;
    }

    // ─── Internal Methods ────────────────────────────────────────────

    /**
     * Send HTTP request to Authorize.Net
     */
    private function sendRequest($payload)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => 'Connection error: ' . $error];
        }

        // Authorize.Net returns BOM character, strip it
        $response = preg_replace('/^\xEF\xBB\xBF/', '', $response);
        return json_decode($response, true) ?? ['error' => 'Invalid response'];
    }

    /**
     * Parse charge/authCapture response
     */
    private function parseChargeResponse($response)
    {
        if (isset($response['error'])) {
            return [
                'success' => false,
                'error'   => $response['error'],
            ];
        }

        $trans = $response['transactionResponse'] ?? null;
        if (!$trans) {
            $messages = $response['messages']['message'] ?? [];
            $errText = !empty($messages) ? $messages[0]['text'] : 'Unknown error';
            return ['success' => false, 'error' => $errText];
        }

        $responseCode = $trans['responseCode'] ?? '3';

        if ($responseCode == '1') {
            // Approved
            return [
                'success'        => true,
                'transaction_id' => $trans['transId'] ?? '',
                'auth_code'      => $trans['authCode'] ?? '',
                'card_type'      => $trans['accountType'] ?? '',
                'card_last_four' => substr($trans['accountNumber'] ?? '', -4),
                'avs_response'   => $trans['avsResultCode'] ?? '',
                'cvv_response'   => $trans['cvvResultCode'] ?? '',
                'response_code'  => $responseCode,
                'message'        => 'Payment approved successfully.',
            ];
        } elseif ($responseCode == '2') {
            // Declined
            $errMsg = $trans['errors'][0]['errorText'] ?? 'Transaction declined.';
            return [
                'success'       => false,
                'error'         => $errMsg,
                'response_code' => $responseCode,
            ];
        } else {
            // Error
            $errMsg = $trans['errors'][0]['errorText'] ?? 'Transaction error.';
            return [
                'success'       => false,
                'error'         => $errMsg,
                'response_code' => $responseCode,
            ];
        }
    }

    /**
     * Parse refund response
     */
    private function parseRefundResponse($response)
    {
        if (isset($response['error'])) {
            return ['success' => false, 'error' => $response['error']];
        }

        $trans = $response['transactionResponse'] ?? null;
        if (!$trans) {
            return ['success' => false, 'error' => 'No transaction response.'];
        }

        if (($trans['responseCode'] ?? '3') == '1') {
            return [
                'success'        => true,
                'transaction_id' => $trans['transId'] ?? '',
                'message'        => 'Refund processed successfully.',
            ];
        }

        $errMsg = $trans['errors'][0]['errorText'] ?? 'Refund failed.';
        return ['success' => false, 'error' => $errMsg];
    }

    /**
     * Parse void response
     */
    private function parseVoidResponse($response)
    {
        if (isset($response['error'])) {
            return ['success' => false, 'error' => $response['error']];
        }

        $trans = $response['transactionResponse'] ?? null;
        if ($trans && ($trans['responseCode'] ?? '3') == '1') {
            return [
                'success' => true,
                'message' => 'Transaction voided successfully.',
            ];
        }

        return ['success' => false, 'error' => 'Void failed.'];
    }
}
