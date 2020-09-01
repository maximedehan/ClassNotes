<?php

namespace App\Tests\Controller;

use App\Controller\AverageController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AverageControllerTest extends WebTestCase
{
    private $markHelper;
    private $studentRepository;
    private $controller;
    private $client;

    public function setUp()
    {
        // Les trois premiers objets mockés sont inutiles ici, c'est pour vous montrer la façon de refacto certains objets mockés.
        $this->markHelper = $this->getMockBuilder('App\Service\MarkHelper')
                            ->disableOriginalConstructor()
                            ->getMock();
        $this->studentRepository = $this->getMockBuilder('App\Repository\StudentRepository')
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->controller = new AverageController($this->markHelper, $this->studentRepository);

        $this->client = $this->createClient();
    }

    public function testViewWithoutValidationForm()
    {
        $crawler  = $this->client->request('GET', '/api/average');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testViewWithValidationForm()
    {
        $crawler = $this->client->request('GET', '/api/average');

        $form = $crawler->selectButton('form[valider]')->form(array(
            'form[student]' => 10,
        ));

        $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
