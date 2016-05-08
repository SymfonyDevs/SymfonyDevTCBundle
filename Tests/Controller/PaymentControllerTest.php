<?php

namespace SymfonyDev\TCBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    public function testSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/payment/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Name', $crawler->filter('form label')->first()->text());
        $this->assertContains('Credit Card Number', $crawler->filter('form label')->last()->text());

        $form = $crawler->selectButton('Save')->form();
        $form->setValues(array(
            'payment_info[name]' => 'Nirav',
            'payment_info[postCode]' => 1234,
            'payment_info[type]' => 'Visa',
            'payment_info[creditCardNumberPlain]' => '4408041234567893',
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('User information stored successfully.', $crawler->filter('body > div > div')->text());
    }

    public function testErrorInvalidCardNumber()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/payment/');

        $form = $crawler->selectButton('Save')->form();
        $form->setValues(array(
            'payment_info[name]' => 'Nirav',
            'payment_info[postCode]' => 1234,
            'payment_info[type]' => 'Visa',
            'payment_info[creditCardNumberPlain]' => '4408041234567895',
        ));
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Invalid card number.', $crawler->filter('li')->text());
    }

    public function testErrorInvalidCardNumberLength()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/payment/');

        $form = $crawler->selectButton('Save')->form();
        $form->setValues(array(
            'payment_info[name]' => 'Nirav',
            'payment_info[postCode]' => 1234,
            'payment_info[type]' => 'Visa',
            'payment_info[creditCardNumberPlain]' => '440804123456789',
        ));
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Invalid card number length.', $crawler->filter('li')->text());
    }

    public function testErrorInvalidCardNumberType()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/payment/');

        $form = $crawler->selectButton('Save')->form();
        $form->setValues(array(
            'payment_info[name]' => 'Nirav',
            'payment_info[postCode]' => 1234,
            'payment_info[type]' => 'Visa',
            'payment_info[creditCardNumberPlain]' => '1408041234567893',
        ));
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Card number not matched with his type.', $crawler->filter('li')->text());
    }

    public function testErrorName()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/payment/');

        $form = $crawler->selectButton('Save')->form();
        $form->setValues(array(
            'payment_info[name]' => 'Nirav121 12121',
            'payment_info[postCode]' => 1234,
            'payment_info[type]' => 'Visa',
            'payment_info[creditCardNumberPlain]' => '4408041234567893',
        ));
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Your name should have only letters.', $crawler->filter('li')->text());
    }

    public function testErrorPostCode()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/payment/');

        $form = $crawler->selectButton('Save')->form();
        $form->setValues(array(
            'payment_info[name]' => 'Nirav',
            'payment_info[postCode]' => 12345,
            'payment_info[type]' => 'Visa',
            'payment_info[creditCardNumberPlain]' => '4408041234567893',
        ));
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('This value should have exactly 4 characters.', $crawler->filter('li')->text());
    }
}
