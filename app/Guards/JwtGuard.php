<?php

namespace App\Auth;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class JwtGuard implements Guard
{
    protected $jwtService;

    protected $user;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function check()
    {
        return ! is_null($this->user());
    }

    public function guest()
    {
        return ! $this->check();
    }

    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = request()->bearerToken();

        if ($token && $this->jwtService->validateToken($token)) {
            $claims = $this->jwtService->getTokenClaims($token);
            $this->user = User::find($claims['uid']);
        }

        return $this->user;
    }

    public function id()
    {
        return $this->user() ? $this->user()->getAuthIdentifier() : null;
    }

    public function validate(array $credentials = [])
    {
        // This method is not needed for JWT auth, but must be implemented
        return false;
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }

    public function hasUser()
    {
        return ! is_null($this->user());
    }
}
