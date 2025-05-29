<?php

namespace NettChain;

class Encryption
{
    /**
     * Deriva una clave segura usando PBKDF2
     * 
     * @param string $userKey Clave del usuario
     * @param string $salt Sal para la derivación
     * @param int $length Longitud de la clave derivada
     * @param int $iterations Número de iteraciones
     * @return string
     */
    private function deriveKey($userKey, $salt, $length = 32, $iterations = 100000): string
    {
        // Si la clave del usuario ya es fuerte, la utilizamos directamente
        if (strlen($userKey) >= $length) {
            return substr($userKey, 0, $length);
        }
        // Derivamos una clave segura usando PBKDF2
        return hash_pbkdf2('sha256', $userKey, $salt, $iterations, $length, true);
    }

    /**
     * Encripta datos usando una clave flexible
     * 
     * @param string $plaintext Texto a encriptar
     * @param string $userKey Clave del usuario
     * @return string
     */
    public function encryptFlexibleKey(string $plaintext, string $userKey): string
    {
        $plaintext = utf8_encode($plaintext);
        $iv = random_bytes(16); // IV para AES-GCM
        $salt = random_bytes(16); // Sal para derivar la clave
        $key = $this->deriveKey($userKey, $salt);

        // Ciframos usando AES-GCM
        $cipher = 'aes-256-gcm';
        $tag = '';
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);

        // Combinamos IV, Sal, Tag y el texto cifrado en un solo paquete
        return base64_encode($iv . $salt . $tag . $ciphertext);
    }

    /**
     * Desencripta datos usando una clave flexible
     * 
     * @param string $encrypted Texto encriptado
     * @param string $userKey Clave del usuario
     * @return string
     */
    public function decryptFlexibleKey(string $encrypted, string $userKey): string
    {
        $encryptedData = base64_decode($encrypted);

        $iv = substr($encryptedData, 0, 16);
        $salt = substr($encryptedData, 16, 16);
        $tag = substr($encryptedData, 32, 16);
        $ciphertext = substr($encryptedData, 48);

        $key = $this->deriveKey($userKey, $salt);

        // Desciframos usando AES-GCM
        $cipher = 'aes-256-gcm';
        $plaintext = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);

        return utf8_decode($plaintext);
    }

    /**
     * Encripta un string utilizando una clave personalizada
     * 
     * @param string $data Datos a encriptar
     * @param string $customKey Clave personalizada
     * @return string
     * @throws \Exception
     */
    public function encrypt(string $data, string $customKey): string
    {
        try {
            // Generamos un IV aleatorio
            $iv = random_bytes(16);
            
            // Aseguramos que la clave tenga el tamaño correcto (32 bytes para AES-256)
            $key = hash('sha256', $customKey, true);
            
            // Encriptamos usando AES-256-CBC
            $ciphertext = openssl_encrypt(
                $data,
                'AES-256-CBC',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );
            
            // Combinamos IV y texto cifrado
            $encrypted = $iv . $ciphertext;
            
            // Codificamos en base64 para almacenamiento seguro
            return base64_encode($encrypted);
        } catch (\Exception $e) {
            throw new \Exception("Error al encriptar los datos: " . $e->getMessage());
        }
    }

    /**
     * Desencripta un string utilizando una clave personalizada
     * 
     * @param string $encryptedData Datos encriptados
     * @param string $customKey Clave personalizada
     * @return string
     * @throws \Exception
     */
    public function decrypt(string $encryptedData, string $customKey): string
    {
        try {
            // Decodificamos los datos encriptados
            $encrypted = base64_decode($encryptedData);
            
            // Extraemos el IV (primeros 16 bytes)
            $iv = substr($encrypted, 0, 16);
            $ciphertext = substr($encrypted, 16);
            
            // Aseguramos que la clave tenga el tamaño correcto
            $key = hash('sha256', $customKey, true);
            
            // Desencriptamos usando AES-256-CBC
            $plaintext = openssl_decrypt(
                $ciphertext,
                'AES-256-CBC',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );
            
            if ($plaintext === false) {
                throw new \Exception("Error al desencriptar los datos");
            }
            
            return $plaintext;
        } catch (\Exception $e) {
            throw new \Exception("Error al desencriptar los datos: " . $e->getMessage());
        }
    }
} 