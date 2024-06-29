<?php

namespace App\Services;

use App\Models\JWTToken;
use Illuminate\Support\Str;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

class JwtService
{
    public $config;

    public function __construct()
    {
        $this->config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(storage_path('app/private.pem')),
            InMemory::file(storage_path('app/public.pem'))
        );
    }

    public function issueToken($user)
    {
        $now = new \DateTimeImmutable();
        $jti = Str::random(32);

        $token = $this->config->builder()
            ->issuedBy(config('app.url')) // change to your app URL
            ->permittedFor(config('app.url')) // change to your app URL
            ->identifiedBy($jti)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->relatedTo($user->id)
            ->withClaim('uid', $user->id)
            ->getToken($this->config->signer(), $this->config->signingKey());
        JWTToken::create([
            'user_id' => $user->id,
            'unique_id' => $jti,
            'last_used_at' => now(),
            'refreshed_at' => now(),
            'token_title' => 'Access Token',
            'created_at' => now(),
            'updated_at' => now(),
            'expires_at' => $now->modify('+1 hour'),
        ]);

        return $token->toString();
    }

    public function validateToken($tokenString)
    {
        $token = $this->config->parser()->parse($tokenString);

        $constraints = [
            new SignedWith($this->config->signer(), $this->config->verificationKey()),
            new LooseValidAt(SystemClock::fromUTC()),
        ];

        return $this->config->validator()->validate($token, ...$constraints);
    }
}
