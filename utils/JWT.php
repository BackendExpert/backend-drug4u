<?php 
    class JWT {
        public static function genarete (
            $payload
        ) {
            $secret = $_ENV['JWT_SECRET'];

            $header = base64_encode(json_encode([
                'typ' => 'JWT',
                'alg' => 'HS256'
            ]));
            
            $payload['exp'] = time() + (int)$_ENV['JWT_EXPIRE'];
            $payload = base64_encode(json_encode($payload));

            $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
            $signature = base64_encode($signature);

            return "$header.$payload.$signature";
        }

        public static function verify($token) {

            $secret = $_ENV['JWT_SECRET'];

            $parts = explode('.', $token);
            if (count($parts) !== 3) return false;

            [$header, $payload, $signature] = $parts;

            $valid = base64_encode(
                hash_hmac('sha256', "$header.$payload", $secret, true)
            );

            if ($valid !== $signature) return false;

            $decoded = json_decode(base64_decode($payload), true);

            if (isset($decoded['exp']) && $decoded['exp'] < time()) {
                return false;
            }

            return $decoded;
        }
    }

?>