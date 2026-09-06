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

    public function test_posts_a_valid_jwt_token_to_cbt_without_exposing_it_in_a_url()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'username' => 'testusersso',
            'name' => 'Test User SSO',
            'role' => UserRole::Guru,
            'status' => true,
        ]);

        $response = $this->actingAs($user)->get('/sso/to-cbt');

        $response->assertOk()
            ->assertViewIs('auth.sso-post-redirect')
            ->assertSee('action="http://127.0.0.1:8000/sso/verify"', false)
            ->assertDontSee('/sso/verify?token=', false);

        preg_match('/name="token" value="([^"]+)"/', $response->getContent(), $matches);
        $token = html_entity_decode($matches[1] ?? '');
        $this->assertNotSame('', $token);

        $secretKey = env('SYNC_SECRET_KEY', 'isi_kunci_rahasia_sso_123');
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

        $this->assertEquals('testusersso', $decoded->uid);
        $this->assertEquals('Test User SSO', $decoded->nama);
        $this->assertEquals('guru', $decoded->role);
        $this->assertTrue($decoded->exp > time());
    }
}
