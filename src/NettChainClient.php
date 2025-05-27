<?php

namespace NettChain;

class NettChainClient
{
    private string $apiKey;
    private string $baseUrl;
    private ?string $globalPassword;

    public function __construct(string $apiKey, ?string $globalPassword = null, string $baseUrl = 'https://api.nettchain.com/v1')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->globalPassword = $globalPassword;
    }

    /**
     * Sets the global password for all operations
     * @param string|null $password
     */
    public function setGlobalPassword(?string $password): void
    {
        $this->globalPassword = $password;
    }

    /**
     * Gets the password to use, prioritizing the specific password over the global one
     * @param string|null $specificPassword
     * @return string|null
     */
    private function getPassword(?string $specificPassword): ?string
    {
        return $specificPassword ?? $this->globalPassword;
    }

    /**
     * Makes an HTTP request using cURL
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array
     * @throws \Exception
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        
        $headers = [
            'x-api-key: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new \Exception('cURL Error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new \Exception('HTTP Error: ' . $httpCode . ' Response: ' . $response);
        }

        return json_decode($response, true);
    }

    /**
     * Creates a new wallet
     * @param string $name Wallet name
     * @param string $blockchain Blockchain type (BTC, LTC, ETH, TRON, SOLANA, BSC, DOGE, MATIC)
     * @param string|null $password Password between 1 and 32 characters (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function createWallet(string $name, string $blockchain, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/wallet/create', [
            'name' => $name,
            'blockchain' => $blockchain,
            'password' => $password
        ]);
    }

    /**
     * Sends Ethereum (ETH)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param int $gasPrice Gas price in wei
     * @param int $gasLimit Gas limit for the transaction
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function sendEth(string $from, string $to, float $amount, int $gasPrice, int $gasLimit, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/eth/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'gasPrice' => $gasPrice,
            'gasLimit' => $gasLimit,
            'password' => $password
        ]);
    }

    /**
     * Sends ERC20 token
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param int $gasPrice Gas price in wei
     * @param int $gasLimit Gas limit for the transaction
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function sendErc20(string $from, string $to, float $amount, int $gasPrice, int $gasLimit, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/eth/erc20/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'gasPrice' => $gasPrice,
            'gasLimit' => $gasLimit,
            'password' => $password
        ]);
    }

    /**
     * Sends Tron (TRX)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function sendTron(string $from, string $to, float $amount, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/tron/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'password' => $password
        ]);
    }

    /**
     * Sends TRC20 token
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string $tokenId TRC20 token ID
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function sendTrc20(string $from, string $to, float $amount, string $tokenId, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/tron/trc20/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'token_id' => $tokenId,
            'password' => $password
        ]);
    }

    /**
     * Sends Solana (SOL)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function sendSolana(string $from, string $to, float $amount, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/solana/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'password' => $password
        ]);
    }

    /**
     * Sends Dogecoin
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string $addressReturn Change address
     * @param float $fee Transaction fee
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function sendDoge(string $from, string $to, float $amount, string $addressReturn, float $fee, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/doge/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'address_return' => $addressReturn,
            'fee' => $fee,
            'password' => $password
        ]);
    }

    /**
     * Sends Ripple (XRP)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function sendRipple(string $from, string $to, float $amount, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/xrp/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'password' => $password
        ]);
    }

    /**
     * Sends AVAX (Avalanche)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws \Exception
     */
    public function sendAvax(string $from, string $to, float $amount, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        return $this->makeRequest('POST', '/avalanche/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'password' => $password
        ]);
    }

    /**
     * Gets all user wallets
     * @return array
     * @throws \Exception
     */
    public function getAllWallets(): array
    {
        return $this->makeRequest('GET', '/wallet/get');
    }

    /**
     * Gets a specific wallet by address
     * @param string $address Wallet address
     * @return array
     * @throws \Exception
     */
    public function getWalletByAddress(string $address): array
    {
        return $this->makeRequest('GET', "/wallet/find/{$address}");
    }

    /**
     * Gets a specific wallet by name
     * @param string $name Wallet name
     * @return array
     * @throws \Exception
     */
    public function getWalletByName(string $name): array
    {
        return $this->makeRequest('GET', "/wallet/findBy/{$name}");
    }

    /**
     * Gets Dogecoin UTXOs
     * @param string $address Dogecoin address
     * @return array
     * @throws \Exception
     */
    public function getDogeUtxos(string $address): array
    {
        return $this->makeRequest('GET', "/doge/utxos/{$address}");
    }

    /**
     * Gets current price of a cryptocurrency
     * @param string $symbol Cryptocurrency symbol (BTC, ETH, etc.)
     * @return array
     * @throws \Exception
     */
    public function getCoinPrice(string $symbol): array
    {
        return $this->makeRequest('GET', "/price/{$symbol}");
    }

    /**
     * Validates a blockchain address
     * @param string $address Address to validate
     * @param string $blockchain Blockchain type
     * @return array
     * @throws \Exception
     */
    public function validateAddress(string $address, string $blockchain): array
    {
        return $this->makeRequest('GET', "/validate/{$blockchain}/{$address}");
    }

    /**
     * Obtiene el balance de una dirección
     * @param string $address Dirección de la wallet
     * @return array
     * @throws \Exception
     */
    public function getBalance(string $address): array
    {
        return $this->makeRequest('GET', "/balance/{$address}");
    }

    /**
     * Obtiene el historial de transacciones de una dirección
     * @param string $address Dirección de la wallet
     * @return array
     * @throws \Exception
     */
    public function getTransactionHistory(string $address): array
    {
        return $this->makeRequest('GET', "/transactions/{$address}");
    }

    /**
     * Envía una transacción
     * @param string $from Dirección de origen
     * @param string $to Dirección de destino
     * @param float $amount Cantidad a enviar
     * @return array
     * @throws \Exception
     */
    public function sendTransaction(string $from, string $to, float $amount): array
    {
        return $this->makeRequest('POST', '/send', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount
        ]);
    }

    /**
     * Obtiene información de un bloque
     * @param string $blockHash Hash del bloque
     * @return array
     * @throws \Exception
     */
    public function getBlockInfo(string $blockHash): array
    {
        return $this->makeRequest('GET', "/block/{$blockHash}");
    }

    /**
     * Obtiene el estado actual de la red
     * @return array
     * @throws \Exception
     */
    public function getNetworkStatus(): array
    {
        return $this->makeRequest('GET', '/network/status');
    }

    /**
     * Imports an existing wallet
     * @param string $name Wallet name
     * @param string $blockchain Blockchain type (BTC, LTC, ETH, TRON, SOLANA, BSC, DOGE, MATIC)
     * @param string $network Network type (mainnet, testnet)
     * @param string $address Wallet address
     * @param string $encryptedKey Encrypted private key
     * @return array
     * @throws \Exception
     */
    public function importWallet(string $name, string $blockchain, string $network, string $address, string $encryptedKey): array
    {
        return $this->makeRequest('POST', '/wallet/import', [
            'name' => $name,
            'blockchain' => $blockchain,
            'network' => $network,
            'address' => $address,
            'encrypted_key' => $encryptedKey
        ]);
    }

    /**
     * Exports a wallet's information
     * @param string $address Wallet address
     * @return array
     * @throws \Exception
     */
    public function exportWallet(string $address): array
    {
        return $this->makeRequest('POST', '/wallet/export', [
            'address' => $address
        ]);
    }
} 