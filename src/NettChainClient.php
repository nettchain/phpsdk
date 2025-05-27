<?php

namespace NettChain;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NettChainClient
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl;
    private ?string $globalPassword;

    public function __construct(string $apiKey, ?string $globalPassword = null, string $baseUrl = 'https://api.nettchain.com/v1')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->globalPassword = $globalPassword;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
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
     * Creates a new wallet
     * @param string $name Wallet name
     * @param string $blockchain Blockchain type (BTC, LTC, ETH, TRON, SOLANA, BSC, DOGE, MATIC)
     * @param string|null $password Password between 1 and 32 characters (optional if set globally)
     * @return array
     * @throws GuzzleException
     */
    public function createWallet(string $name, string $blockchain, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/wallet/create', [
            'json' => [
                'name' => $name,
                'blockchain' => $blockchain,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
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
     * @throws GuzzleException
     */
    public function sendEth(string $from, string $to, float $amount, int $gasPrice, int $gasLimit, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/eth/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'gasPrice' => $gasPrice,
                'gasLimit' => $gasLimit,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
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
     * @throws GuzzleException
     */
    public function sendErc20(string $from, string $to, float $amount, int $gasPrice, int $gasLimit, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/eth/erc20/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'gasPrice' => $gasPrice,
                'gasLimit' => $gasLimit,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Sends Tron (TRX)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws GuzzleException
     */
    public function sendTron(string $from, string $to, float $amount, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/tron/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Sends TRC20 token
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string $tokenId TRC20 token ID
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws GuzzleException
     */
    public function sendTrc20(string $from, string $to, float $amount, string $tokenId, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/tron/trc20/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'token_id' => $tokenId,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Sends Solana (SOL)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws GuzzleException
     */
    public function sendSolana(string $from, string $to, float $amount, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/solana/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
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
     * @throws GuzzleException
     */
    public function sendDoge(string $from, string $to, float $amount, string $addressReturn, float $fee, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/doge/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'address_return' => $addressReturn,
                'fee' => $fee,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Sends Ripple (XRP)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws GuzzleException
     */
    public function sendRipple(string $from, string $to, float $amount, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/xrp/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Sends AVAX (Avalanche)
     * @param string $from Source address
     * @param string $to Destination address
     * @param float $amount Amount to send
     * @param string|null $password Password (optional if set globally)
     * @return array
     * @throws GuzzleException
     */
    public function sendAvax(string $from, string $to, float $amount, ?string $password = null): array
    {
        $password = $this->getPassword($password);
        if ($password === null) {
            throw new \InvalidArgumentException('Password is required. Set it globally or in the operation.');
        }

        $response = $this->client->post('/avalanche/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'password' => $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Gets all user wallets
     * @return array
     * @throws GuzzleException
     */
    public function getAllWallets(): array
    {
        $response = $this->client->get('/wallet/get');
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Gets a specific wallet by address
     * @param string $address Wallet address
     * @return array
     * @throws GuzzleException
     */
    public function getWalletByAddress(string $address): array
    {
        $response = $this->client->get("/wallet/find/{$address}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Gets a specific wallet by name
     * @param string $name Wallet name
     * @return array
     * @throws GuzzleException
     */
    public function getWalletByName(string $name): array
    {
        $response = $this->client->get("/wallet/findBy/{$name}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Gets Dogecoin UTXOs
     * @param string $address Dogecoin address
     * @return array
     * @throws GuzzleException
     */
    public function getDogeUtxos(string $address): array
    {
        $response = $this->client->get("/doge/utxos/{$address}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Gets current price of a cryptocurrency
     * @param string $symbol Cryptocurrency symbol (BTC, ETH, etc.)
     * @return array
     * @throws GuzzleException
     */
    public function getCoinPrice(string $symbol): array
    {
        $response = $this->client->get("/price/{$symbol}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Validates a blockchain address
     * @param string $address Address to validate
     * @param string $blockchain Blockchain type
     * @return array
     * @throws GuzzleException
     */
    public function validateAddress(string $address, string $blockchain): array
    {
        $response = $this->client->get("/validate/{$blockchain}/{$address}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Obtiene el balance de una dirección
     * @param string $address Dirección de la wallet
     * @return array
     * @throws GuzzleException
     */
    public function getBalance(string $address): array
    {
        $response = $this->client->get("/balance/{$address}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Obtiene el historial de transacciones de una dirección
     * @param string $address Dirección de la wallet
     * @return array
     * @throws GuzzleException
     */
    public function getTransactionHistory(string $address): array
    {
        $response = $this->client->get("/transactions/{$address}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Envía una transacción
     * @param string $from Dirección de origen
     * @param string $to Dirección de destino
     * @param float $amount Cantidad a enviar
     * @return array
     * @throws GuzzleException
     */
    public function sendTransaction(string $from, string $to, float $amount): array
    {
        $response = $this->client->post('/send', [
            'json' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Obtiene información de un bloque
     * @param string $blockHash Hash del bloque
     * @return array
     * @throws GuzzleException
     */
    public function getBlockInfo(string $blockHash): array
    {
        $response = $this->client->get("/block/{$blockHash}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Obtiene el estado actual de la red
     * @return array
     * @throws GuzzleException
     */
    public function getNetworkStatus(): array
    {
        $response = $this->client->get('/network/status');
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Imports an existing wallet
     * @param string $name Wallet name
     * @param string $blockchain Blockchain type (BTC, LTC, ETH, TRON, SOLANA, BSC, DOGE, MATIC)
     * @param string $network Network type (mainnet, testnet)
     * @param string $address Wallet address
     * @param string $encryptedKey Encrypted private key
     * @return array
     * @throws GuzzleException
     */
    public function importWallet(string $name, string $blockchain, string $network, string $address, string $encryptedKey): array
    {
        $response = $this->client->post('/wallet/import', [
            'json' => [
                'name' => $name,
                'blockchain' => $blockchain,
                'network' => $network,
                'address' => $address,
                'encrypted_key' => $encryptedKey
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Exports a wallet's information
     * @param string $address Wallet address
     * @return array
     * @throws GuzzleException
     */
    public function exportWallet(string $address): array
    {
        $response = $this->client->post('/wallet/export', [
            'json' => [
                'address' => $address
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
} 