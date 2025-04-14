<?php
namespace App\Helpers;
use Elliptic\EC;
use kornrunner\Keccak;

class KeyHelper{

    // This function is to generate the private and public key
    public static function generateWalletKeys(){
        $privateKey = '0x' . bin2hex(random_bytes(32));

        $ec = new EC('secp256k1');
        $keyPair = $ec->keyFromPrivate(substr($privateKey, 2), 'hex');
        $publicKey = $keyPair->getPublic(false, 'hex'); // uncompressed key
        $publicKeyHex = substr($publicKey, 2); // remove '04' prefix

        // Hash the public key
        $hash = Keccak::hash(hex2bin($publicKeyHex), 256);
        $address = '0x' . substr($hash, -40);

        return [
            'private_key' => $privateKey,
            'public_address' => strtolower($address),
        ];
    }
}
