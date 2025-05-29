# NettChain PHP SDK

This is the official PHP SDK for interacting with the NettChain API, a blockchain platform that supports multiple cryptocurrencies including Bitcoin, Ethereum, Litecoin, Dogecoin, TRON, Binance Coin, Solana, Ripple, and USDT.

## Requirements

- PHP 7.4 or higher
- Composer
- PHP JSON extension

## Installation

```bash
composer require nettchain/phpsdk
```

## Usage

```php
<?php

require 'vendor/autoload.php';

use NettChain\NettChainClient;

// Initialize client with global password (optional)
$client = new NettChainClient('your-api-key', 'global-password');

// Or change global password later
$client->setGlobalPassword('new-global-password');

// Create a new wallet (using global password)
$wallet = $client->createWallet(
    'my-wallet',
    'BTC'
);

// Or specify a different password for this operation
$wallet = $client->createWallet(
    'my-wallet',
    'BTC',
    'specific-password'
);

// Import an existing wallet
$wallet = $client->importWallet(
    'my-imported-wallet',
    'ETH',
    'mainnet',
    '0x123abc...456',
    'ENCRYPTED_PRIVATE_KEY'
);

// Export a wallet
$walletInfo = $client->exportWallet('0x123abc...456');
// Returns wallet information including:
// - encrypted: boolean
// - encrypted_version: string
// - name: string
// - blockchain: string
// - network: string
// - address: string

// Send Ethereum (ETH)
$transaction = $client->sendEth(
    'source-address',
    'destination-address',
    1.5,
    20000000000, // gasPrice in wei
    21000,       // gasLimit
    'password'   // optional if set globally
);

// Send ERC20 token
$transaction = $client->sendErc20(
    'source-address',    // from address
    'destination-address', // to address
    1000,               // amount
    'token-contract-address', // contract address
    20000000000,        // gasprice
    21000               // gaslimit
);

// Send Tron (TRX)
$transaction = $client->sendTron(
    'source-address',
    'destination-address',
    1.5
);

// Send TRC20 token
$transaction = $client->sendTrc20(
    'source-address',    // from address
    'destination-address', // to address
    0.000001,           // amount (minimum 0.000001)
    'token-contract-address', // contract address
    'password'          // optional if set globally
);

// Send Solana (SOL)
$transaction = $client->sendSolana(
    'source-address',
    'destination-address',
    1.5
);

// Send Dogecoin
$transaction = $client->sendDoge(
    'source-address',
    'destination-address',
    1.5,
    'change-address',
    0.0001
);

// Send Ripple (XRP)
$transaction = $client->sendRipple(
    'source-address',
    'destination-address',
    1.5
);

// Send AVAX (Avalanche)
$transaction = $client->sendAvax(
    'source-address',
    'destination-address',
    1.5
);

// Get all wallets
$wallets = $client->getAllWallets();

// Get specific wallet
$wallet = $client->getWalletByAddress('wallet-address');

// Get wallet by name
$wallet = $client->getWalletByName('wallet-name');

// Get Dogecoin UTXOs
$utxos = $client->getDogeUtxos('doge-address');

// Get cryptocurrency price
$price = $client->getCoinPrice('BTC');

// Validate blockchain address
$validation = $client->validateAddress('address', 'BTC');
```

## Password Management

The client allows password management in two ways:

1. **Global Password**: Can be set when creating the client or later using `setGlobalPassword()`. This password will be used for all operations that require it.

2. **Specific Password**: Can be provided for each individual operation. This password takes precedence over the global password.

If no password is provided (neither global nor specific), the client will throw an exception.

## Available Methods

### Wallet Management
- `createWallet(string $name, string $blockchain, ?string $password = null)`: Creates a new wallet
- `getAllWallets()`: Gets all user wallets
- `getWalletByAddress(string $address)`: Gets a wallet by its address
- `getWalletByName(string $name)`: Gets a wallet by its name
- `importWallet(string $name, string $blockchain, string $network, string $address, string $encryptedKey)`: Imports an existing wallet
- `exportWallet(string $address)`: Exports a wallet's information

### Cryptocurrency Operations
- `sendEth(string $from, string $to, float $amount, int $gasPrice, int $gasLimit)`: Sends Ethereum (ETH)
- `sendErc20(string $from, string $to, int $amount, string $contractAddress, int $gasPrice, int $gasLimit)`: Sends ERC20 token
- `sendTron(string $from, string $to, float $amount)`: Sends Tron (TRX)
- `sendTrc20(string $from, string $to, float $amount, string $contractAddress, ?string $password = null)`: Sends TRC20 token
- `sendSolana(string $from, string $to, float $amount, ?string $password = null)`: Sends Solana (SOL)
- `sendDoge(string $from, string $to, float $amount, string $addressReturn, float $fee, ?string $password = null)`: Sends Dogecoin
- `sendRipple(string $from, string $to, float $amount, ?string $password = null)`: Sends Ripple (XRP)
- `sendAvax(string $from, string $to, float $amount, ?string $password = null)`: Sends AVAX (Avalanche)

### Utilities
- `getCoinPrice(string $symbol)`: Gets current price of a cryptocurrency
- `validateAddress(string $address, string $blockchain)`: Validates a blockchain address
- `getDogeUtxos(string $address)`: Gets Dogecoin UTXOs

### Encryption
The SDK provides encryption capabilities for secure data handling:

```php
// Encrypt data
$encryptedData = $client->encryption->encrypt('sensitive-data', 'encryption-password');

// Decrypt data
$decryptedData = $client->encryption->decrypt($encryptedData, 'encryption-password');
```

The encryption method uses AES-256-GCM for secure encryption and includes:
- Automatic IV (Initialization Vector) generation
- Authentication tag for data integrity
- Secure key derivation using PBKDF2
- Base64 encoding for encrypted output

**Important Security Notes:**
- All encryption/decryption operations are performed locally on your machine
- Private keys are never sent to the server in plain text
- When importing or exporting wallets, private keys are always encrypted before transmission
- The encryption password is never stored or transmitted to the server

## Error Handling

The client uses cURL for HTTP requests. All cURL exceptions are propagated, so you should handle `CurlException` in your code:

```php
try {
    $wallet = $client->createWallet('my-wallet', 'BTC');
} catch (\CurlException $e) {
    // Handle API error
} catch (\InvalidArgumentException $e) {
    // Handle missing password error
}
```

## Supported Blockchains

- Bitcoin (BTC)
- Litecoin (LTC)
- Dogecoin (DOGE)
- Ethereum (ETH)
- TRON (TRX)
- Binance Coin (BNB)
- Solana (SOL)
- Ripple (XRP)
- USDT (Tether, both ERC-20 and TRC-20)
- Avalanche (AVAX) 