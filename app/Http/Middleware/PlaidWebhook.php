<?php

namespace App\Http\Middleware;

use Closure;
use Jose\Easy\JWT;
use Jose\Easy\Load;
use App\Services\Plaid;

class PlaidWebhook
{
    /** @var Plaid $service */
    private $service;

    public function __construct(Plaid $service)
    {
        $this->service = $service;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $verificationHeader = $request->header('Plaid-Verification');
        if (!$verificationHeader) return response(403);

        try {
            $parts = explode('.', $verificationHeader);
            $decodedHeader = base64_decode($parts[0]);
            $jsonDecoded = json_decode($decodedHeader, true);
        } catch (\Exception $e) {
            return response(403);
        }

        if ($jsonDecoded['alg'] !== 'ES256') return response(403);
        if (strlen($jsonDecoded['kid']) !== 36) return response(403);

        $kid = $jsonDecoded['kid'];

        try {
            $jwk = $this->service->getWebhookVerificationKey($kid);
        } catch (\Exception $e) {
            return response(403);
        }

        $validatedJwt = Load::jws($verificationHeader)->alg('ES256')->iat(1000 * 5 * 60)->key($jwk)->run();

        if (!($validatedJwt instanceof JWT)) return response(403);

        $requestBodySha256 = $validatedJwt->claims->get('request_body_sha256');

        $bodyHash = hash('sha256', $request->getContent());

        if ($bodyHash !== $requestBodySha256) return response('Body hash did not match validated validated hash', 403);

        return $next($request);
    }
}
