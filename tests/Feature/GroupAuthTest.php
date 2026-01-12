<?php

namespace Tests\Feature;

use Tests\TestCase;

class GroupAuthTest extends TestCase
{
    /**
     * Test: Misafir kullanıcı gruplar listesine erişemez.
     */
    public function test_misafir_kullanici_gruplar_listesine_erisemez(): void
    {
        $this->get('/groups')->assertRedirect('/login');
    }

    /**
     * Test: Misafir kullanıcı grup oluşturma sayfasına erişemez.
     */
    public function test_misafir_kullanici_grup_olusturma_sayfasina_erisemez(): void
    {
        $this->get('/groups/create')->assertRedirect('/login');
    }
}
