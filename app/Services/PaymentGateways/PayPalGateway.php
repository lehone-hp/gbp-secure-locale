<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalGateway implements PaymentGatewayInterface
{
    protected PayPalClient $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        
        // Use config/paypal.php configuration
        $config = config('paypal');

        $this->provider->setApiCredentials($config);
        
        Log::info('PayPal: Gateway initialized', [
            'mode' => $config['mode'],
            'client_id_set' => !empty($config[$config['mode']]['client_id']),
            'secret_set' => !empty($config[$config['mode']]['client_secret']),
        ]);
    }

    /**
     * Create a PayPal order
     */
    public function createPayment(Order $order, array $paymentData): array
    {
        Log::info('PayPal: Starting payment creation', [
            'order_id' => $order->id,
            'amount' => $order->total,
            'currency' => $order->currency,
        ]);

        try {
            // Get access token
            $accessToken = $this->provider->getAccessToken();
            
            if (isset($accessToken['error'])) {
                Log::error('PayPal: Authentication failed', [
                    'error' => $accessToken['error'],
                    'error_description' => $accessToken['error_description'] ?? '',
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Failed to authenticate with PayPal',
                ];
            }

            Log::info('PayPal: Access token obtained successfully');

            // Create order data
            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => (string) $order->id,
                        'description' => "Order #{$order->id}",
                        'amount' => [
                            'currency_code' => $order->currency,
                            'value' => number_format($order->total, 2, '.', ''),
                        ],
                        'shipping' => [
                            'name' => [
                                'full_name' => "{$order->customer_first_name} {$order->customer_last_name}",
                            ],
                            'address' => [
                                'address_line_1' => $order->address_line_1,
                                'address_line_2' => $order->address_line_2,
                                'admin_area_2' => $order->city,
                                'admin_area_1' => $order->state,
                                'postal_code' => $order->postal_code,
                                'country_code' => $order->country,
                            ],
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => route('checkout.success', $order->id),
                    'cancel_url' => route('checkout.cancel'),
                    'brand_name' => config('app.name'),
                    'locale' => 'en-US',
                    'landing_page' => 'LOGIN',
                    'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                    'user_action' => 'PAY_NOW',
                ],
            ];

            Log::info('PayPal: Creating order', [
                'order_data' => $orderData,
            ]);

            // Create PayPal order
            $response = $this->provider->createOrder($orderData);

            Log::info('PayPal: Order creation response', [
                'response' => $response,
            ]);

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                $approvalUrl = null;
                
                // Extract approval URL
                if (isset($response['links'])) {
                    foreach ($response['links'] as $link) {
                        if ($link['rel'] === 'approve') {
                            $approvalUrl = $link['href'];
                            break;
                        }
                    }
                }

                Log::info('PayPal: Order created successfully', [
                    'order_id' => $order->id,
                    'paypal_order_id' => $response['id'],
                    'status' => $response['status'],
                    'approval_url' => $approvalUrl,
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $response['id'],
                    'status' => $response['status'],
                    'approval_url' => $approvalUrl,
                    'response' => [
                        'id' => $response['id'],
                        'status' => $response['status'],
                        'amount' => $order->total,
                        'currency' => $order->currency,
                        'created_at' => $response['create_time'] ?? now()->format('Y-m-d H:i:s'),
                    ],
                ];
            }

            Log::error('PayPal: Failed to create order', [
                'order_id' => $order->id,
                'response' => $response,
            ]);

            return [
                'success' => false,
                'error' => $response['message'] ?? 'Failed to create PayPal order',
                'response' => $response,
            ];

        } catch (\Exception $e) {
            Log::error('PayPal: Exception creating payment', [
                'message' => $e->getMessage(),
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => [
                    'exception' => $e->getMessage(),
                ],
            ];
        }
    }

    /**
     * Capture an authorized PayPal order
     */
    public function capturePayment(string $transactionId): array
    {
        Log::info('PayPal: Starting payment capture', [
            'transaction_id' => $transactionId,
        ]);

        try {
            // Get access token
            $accessToken = $this->provider->getAccessToken();
            
            if (isset($accessToken['error'])) {
                Log::error('PayPal: Authentication failed for capture', [
                    'error' => $accessToken['error'],
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Failed to authenticate with PayPal',
                ];
            }

            Log::info('PayPal: Capturing order', [
                'transaction_id' => $transactionId,
            ]);

            // Capture the order
            $response = $this->provider->capturePaymentOrder($transactionId);

            Log::info('PayPal: Capture response', [
                'transaction_id' => $transactionId,
                'response' => $response,
            ]);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                $capture = $response['purchase_units'][0]['payments']['captures'][0] ?? null;
                $captureId = $capture['id'] ?? null;
                $captureStatus = $capture['status'] ?? null;

                // The order-level COMPLETED only means PayPal processed the request;
                // the individual capture can still be DECLINED at the processor level.
                if ($captureStatus !== 'COMPLETED') {
                    Log::error('PayPal: Capture declined by processor', [
                        'transaction_id' => $transactionId,
                        'capture_id' => $captureId,
                        'capture_status' => $captureStatus,
                        'processor_response' => $capture['processor_response'] ?? null,
                    ]);

                    return [
                        'success' => false,
                        'error' => 'Payment was declined by the card processor',
                        'capture_status' => $captureStatus,
                        'response' => $response,
                    ];
                }

                Log::info('PayPal: Payment captured successfully', [
                    'transaction_id' => $transactionId,
                    'capture_id' => $captureId,
                    'status' => $captureStatus,
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'capture_id' => $captureId,
                    'status' => $captureStatus,
                    'response' => $response,
                ];
            }

            Log::error('PayPal: Failed to capture payment', [
                'transaction_id' => $transactionId,
                'response' => $response,
            ]);

            return [
                'success' => false,
                'error' => $response['message'] ?? 'Failed to capture PayPal payment',
                'response' => $response,
            ];

        } catch (\Exception $e) {
            Log::error('PayPal: Exception capturing payment', [
                'message' => $e->getMessage(),
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund a captured PayPal payment
     */
    public function refundPayment(string $transactionId, float $amount): array
    {
        Log::info('PayPal: Starting refund process', [
            'transaction_id' => $transactionId,
            'amount' => $amount,
        ]);

        try {
            // Get access token
            $accessToken = $this->provider->getAccessToken();
            
            if (isset($accessToken['error'])) {
                Log::error('PayPal: Authentication failed for refund', [
                    'error' => $accessToken['error'],
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Failed to authenticate with PayPal',
                ];
            }

            // First, get the order details to find the capture ID
            Log::info('PayPal: Retrieving order details for refund', [
                'transaction_id' => $transactionId,
            ]);

            $orderDetails = $this->provider->showOrderDetails($transactionId);

            if (!isset($orderDetails['purchase_units'][0]['payments']['captures'][0]['id'])) {
                Log::error('PayPal: No capture found for refund', [
                    'transaction_id' => $transactionId,
                ]);
                
                return [
                    'success' => false,
                    'error' => 'No capture found for this order',
                ];
            }

            $captureId = $orderDetails['purchase_units'][0]['payments']['captures'][0]['id'];

            Log::info('PayPal: Capture ID retrieved', [
                'transaction_id' => $transactionId,
                'capture_id' => $captureId,
            ]);

            // Refund the capture
            $refundData = [
                'amount' => [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => $orderDetails['purchase_units'][0]['amount']['currency_code'] ?? 'USD',
                ],
            ];

            Log::info('PayPal: Sending refund request', [
                'capture_id' => $captureId,
                'refund_data' => $refundData,
            ]);

            $response = $this->provider->refundCapturedPayment($captureId, '', $refundData);

            Log::info('PayPal: Refund response', [
                'capture_id' => $captureId,
                'response' => $response,
            ]);

            if (isset($response['status']) && in_array($response['status'], ['COMPLETED', 'PENDING'])) {
                Log::info('PayPal: Refund processed successfully', [
                    'transaction_id' => $transactionId,
                    'refund_id' => $response['id'],
                    'status' => $response['status'],
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'refund_id' => $response['id'],
                    'status' => $response['status'],
                ];
            }

            Log::error('PayPal: Failed to process refund', [
                'transaction_id' => $transactionId,
                'capture_id' => $captureId,
                'response' => $response,
            ]);

            return [
                'success' => false,
                'error' => $response['message'] ?? 'Failed to refund PayPal payment',
                'response' => $response,
            ];

        } catch (\Exception $e) {
            Log::error('PayPal: Exception refunding payment', [
                'message' => $e->getMessage(),
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get client token (PayPal uses client ID for frontend)
     */
    public function getClientToken(): ?string
    {
        return config('payment.gateways.paypal.client_id');
    }

    /**
     * Verify PayPal webhook signature
     */
    public function verifyWebhook(Request $request): bool
    {
        Log::info('PayPal: Starting webhook verification', [
            'event_type' => $request->input('event_type'),
            'resource_type' => $request->input('resource_type'),
        ]);

        try {
            $webhookId = config('payment.gateways.paypal.webhook_id');
            
            if (!$webhookId) {
                Log::warning('PayPal: Webhook ID not configured');
                return false;
            }

            // Get access token
            $accessToken = $this->provider->getAccessToken();
            
            if (isset($accessToken['error'])) {
                Log::error('PayPal: Authentication failed for webhook verification');
                return false;
            }

            $headers = [
                'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url' => $request->header('PAYPAL-CERT-URL'),
                'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
            ];

            $webhookData = [
                'auth_algo' => $headers['auth_algo'],
                'cert_url' => $headers['cert_url'],
                'transmission_id' => $headers['transmission_id'],
                'transmission_sig' => $headers['transmission_sig'],
                'transmission_time' => $headers['transmission_time'],
                'webhook_id' => $webhookId,
                'webhook_event' => $request->all(),
            ];

            Log::info('PayPal: Verifying webhook signature', [
                'webhook_id' => $webhookId,
            ]);

            $response = $this->provider->verifyWebHook($webhookData);

            $verified = isset($response['verification_status']) && $response['verification_status'] === 'SUCCESS';

            Log::info('PayPal: Webhook verification completed', [
                'verified' => $verified,
                'response' => $response,
            ]);

            return $verified;

        } catch (\Exception $e) {
            Log::error('PayPal: Exception verifying webhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
