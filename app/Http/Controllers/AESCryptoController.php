<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AESCryptoController extends Controller
{
    //배열 길이 대치시켜주고 배열반환하기
    public function arraycopy($src, $srcPos, &$dest, $destPos, $length)
    {
        for ($i = $srcPos; $i < $srcPos + $length; $i++) {
            $dest[$destPos] = $src[$i];
            $destPos++;
        }

    }
    public function xor16(&$t, $x1, $x2)
    {
        $t[0] = $x1[0] ^ $x2[0];
        $t[1] = $x1[1] ^ $x2[1];
        $t[2] = $x1[2] ^ $x2[2];
        $t[3] = $x1[3] ^ $x2[3];
        $t[4] = $x1[4] ^ $x2[4];
        $t[5] = $x1[5] ^ $x2[5];
        $t[6] = $x1[6] ^ $x2[6];
        $t[7] = $x1[7] ^ $x2[7];
        $t[8] = $x1[8] ^ $x2[8];
        $t[9] = $x1[9] ^ $x2[9];
        $t[10] = $x1[10] ^ $x2[10];
        $t[11] = $x1[11] ^ $x2[11];
        $t[12] = $x1[12] ^ $x2[12];
        $t[13] = $x1[13] ^ $x2[13];
        $t[14] = $x1[14] ^ $x2[14];
        $t[15] = $x1[15] ^ $x2[15];
    }

    private function convertMinus128($bytes)
    {
        if(PHP_INT_SIZE > 4) { // 64비트가 아닌 경우 그대로 출력
            return $bytes;
        }

        if (is_array($bytes)) {
            $ret = array();
            foreach($bytes as $val) {
                $ret[] = (($val+128) % 256) -128;
            }
            return $ret;
        }
        return (($bytes+128) % 256) -128;
    }

    public function encrypt($str)
    {
        $str = iconv($this->serverEncoding, $this->innerEncoding, $str);
        $planBytes = array_slice(unpack('c*',$str), 0); // 평문을 바이트 배열로 변환
        if (count($planBytes) == 0) {
            return $str;
        }

        $seed = new Seed();
        $seed->SeedRoundKey($pdwRoundKey, $this->pbUserKey); // 라운드키 생성

        $planBytesLength = count($planBytes);
        $start = 0;
        $end = 0;
        $cipherBlockBytes = array();
        $cbcBlockBytes = array();
        $this->arraycopy($this->IV, 0, $cbcBlockBytes, 0, $this->block); // CBC블록을 IV 바이트로 초기화
        $ret = null;
        while ($end < $planBytesLength) {
            $end = $start + $this->block;
            if ($end > $planBytesLength) {
                $end = $planBytesLength;
            }

            $this->arraycopy($planBytes, $start, $cipherBlockBytes, 0, $end - $start); // 암호블록을 평문 블록으로 대치

            $nPad = $this->block - ($end - $start); // 블록내 바이트 패딩값 계산
            for ($i = ($end - $start); $i < $this->block; $i++) {
                $cipherBlockBytes[$i] = $nPad; // 비어있는 바이트에 패딩 추가
            }

            $this->xor16($cipherBlockBytes, $cbcBlockBytes, $cipherBlockBytes); // CBC운영모드로 새로운 암호화 블록 생성
            $seed->SeedEncrypt($cipherBlockBytes, $pdwRoundKey, $encryptCbcBlockBytes); // 암호블록을 SEED로 암호화
            $this->arraycopy($encryptCbcBlockBytes, 0, $cbcBlockBytes, 0, $this->block); // 다음 블록에서 사용할 CBC블록을 SEED암호 블록으로 대치

            foreach($encryptCbcBlockBytes as $encryptedString) {
                $ret .= bin2hex(chr($encryptedString)); // 암호화된 16진수 스트링 추가 저장
            }
            $start = $end;
        }
        return $ret;
    }

    public function decrypt($str)
    {

        $pdwKey='Zmxhc3lzdGVtZGV2ZWxvcG1lbnQ=';

        $planBytes = array();
        for ($i = 0; $i < strlen($str); $i += 2) {
            $planBytes[] = $this->convertMinus128(hexdec(substr($str, $i, 2))); // 16진수를 바이트 배열로 변환
        }

        if (count($planBytes) == 0) {
            return $str;
        }

        $seed = new Seed();
        $seed->SeedRoundKey($pdwKey, $this->pbUserKey);

        $planBytesLength = count($planBytes);
        $start = 0;
        $isEnd = false;
        $cipherBlockBytes = array();
        $cbcBlockBytes = array();
        $thisEE = array();
        $this->arraycopy($this->IV, 0, $cbcBlockBytes, 0, $this->block); // CBC블록을 IV 바이트로 초기화

        while (!$isEnd) {
            if ($start + $this->block >= $planBytesLength) {
                $isEnd = true;
            }

            $this->arraycopy($planBytes, $start, $cipherBlockBytes, 0, $this->block); // 암호블록을 평문블록으로 대치
            $seed->SeedDecrypt($cipherBlockBytes, $pdwRoundKey, $ee); // 암호블록을 SEED로 복호화
            $this->xor16($thisEE, $cbcBlockBytes, $ee); // CBC운영모드로 새로운 복호화 블록 생성
            $thisEE = $this->convertMinus128($thisEE);

            $this->arraycopy($thisEE, 0, $planBytes, $start, $this->block); // 평문블록을 생성한 복호화 블록으로 대치
            $this->arraycopy($cipherBlockBytes, 0, $cbcBlockBytes, 0, $this->block); // 다음 블록에서 사용할 CBC블록을 암호 블록으로 대치
            $start += $this->block; // 다음블록의 시작 위치 계산
        }
        $rst = iconv($this->innerEncoding, $this->serverEncoding, call_user_func_array("pack", array_merge(array("c*"), $planBytes))); // 평문블록 바이트 배열을 문자열로 변환
        return $this->pkcs5Unpad($rst);
    }

    public function cryptoAES($str)
    {
      $crypto= Crypt::encryptString($str);
      return $crypto;
    }

    public function decryptAES($str)
    {
        $decrypt = Crypt::decryptString($str);
        return $decrypt;
    }

}
