<?php
/**
 * Created by PhpStorm.
 * User: sunqingjiang
 * Date: 2020/9/8
 * Time: 7:39 下午
 */

namespace IEXBase\TronAPI;

use Exception;
use IEXBase\TronAPI\Support\Base58;
use IEXBase\TronAPI\Support\Crypto;
use IEXBase\TronAPI\Support\Hash;
use IEXBase\TronAPI\Support\Keccak;
use IEXBase\TronAPI\Exception\TronException;
use Phactor\Key;

class TronAddress
{
    /**
     * 生成地址
     *
     * @return array
     * @throws TronException
     */
    public static function generate()
    {
        $attempts = 0;
        $validAddress = false;

        do {
            if ($attempts++ === 5) {
                throw new TronException('Could not generate valid key');
            }

            // 生成密钥对
            $keyPair = self::generateKeyPair();
            $privateKeyHex = $keyPair['private_key_hex'];
            $pubKeyHex = $keyPair['public_key'];

            //We cant use hex2bin unless the string length is even.
            if (strlen($pubKeyHex) % 2 !== 0) {
                continue;
            }

            $pubKeyBin = hex2bin($pubKeyHex);
            $addressHex = self::getAddressHex($pubKeyBin);
            $addressBin = hex2bin($addressHex);
            $addressBase58 = self::getBase58CheckAddress($addressBin);

            $validAddress = self::validateAddress($addressBase58);

        } while (!$validAddress);

        return [
            'privateKey' => $privateKeyHex,
            'address' => $addressBase58,
            'hexAddress' => $addressHex
        ];
    }

    /**
     * 生成密钥对
     *
     * @return array
     */
    private static function generateKeyPair(): array
    {
        $key = new Key();

        return $key->GenerateKeypair();
    }

    /**
     * 获取地址Hex
     *
     * @param string $pubKeyBin
     * @return string
     * @throws Exception
     */
    private static function getAddressHex(string $pubKeyBin): string
    {
        if (strlen($pubKeyBin) == 65) {
            $pubKeyBin = substr($pubKeyBin, 1);
        }

        $hash = Keccak::hash($pubKeyBin, 256);

        return Tron::ADDRESS_PREFIX . substr($hash, 24);
    }

    /**
     * 获取base58
     *
     * @param string $addressBin
     * @return string
     */
    private static function getBase58CheckAddress(string $addressBin): string
    {
        $hash0 = Hash::SHA256($addressBin);
        $hash1 = Hash::SHA256($hash0);
        $checksum = substr($hash1, 0, 4);
        $checksum = $addressBin . $checksum;

        return Base58::encode(Crypto::bin2bc($checksum));
    }

    /**
     * 验证地址
     *
     * @param $address
     * @return bool
     * @throws TronException
     */
    private static function validateAddress($address): bool
    {
        $tron = new Tron();

        if (!$tron->isAddress($address)) {
            return false;
        }

        $result = $tron->validateAddress($address);

        return $result['result'];
    }
}