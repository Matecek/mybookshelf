<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testCannotSetRatingIfStatusNotFinished()
    {
        $client = static::createClient();

        // Symulacja formularza z nieprawidłowym statusem
        $client->request('POST', '/book/1', [
            'status' => 'reading',
            'rating' => 8,
        ]);

        // Sprawdź, czy przekierowano z komunikatem o błędzie
        $this->assertResponseRedirects('/book/1');
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-danger', 'Można wystawić ocenę tylko przeczytanym książkom.');
    }

    public function testCanSetRatingIfStatusFinished()
    {
        $client = static::createClient();

        // Symulacja formularza z prawidłowym statusem
        $client->request('POST', '/book/1', [
            'status' => 'finished',
            'rating' => 8,
        ]);

        // Sprawdź, czy dane zostały zapisane poprawnie
        $this->assertResponseRedirects('/book/1');
        $client->followRedirect();
        $this->assertSelectorExists('input[name="rating"][value="8"]');
    }
}