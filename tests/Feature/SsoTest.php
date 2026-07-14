<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\UserRole;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SsoTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_to_cbt_with_valid_jwt_token()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'username' => 'testusersso',
            'name' => 'Test User SSO',
            'role' => UserRole::Guru,
            'status' => true,
        ]);

        $response = $this->actingAs($user)->get('/sso/to-cbt');

        $response->assertRedirect();
        
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('http://127.0.0.1:8000/sso/verify?token=', $targetUrl);

        // Parse token
        $urlParts = parse_url($targetUrl);
        parse_str($urlParts['query'], $query);
        $token = $query['token'];

        $secretKey = env('SYNC_SECRET_KEY', 'isi_kunci_rahasia_sso_123');
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

        $this->assertEquals('testusersso', $decoded->uid);
        $this->assertEquals('Test User SSO', $decoded->nama);
        $this->assertEquals('guru', $decoded->role);
        $this->assertTrue($decoded->exp > time());
    }
}
