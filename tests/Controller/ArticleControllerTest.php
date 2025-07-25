<?php

namespace App\Tests\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ArticleControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $articleRepository;
    private string $path = '/article/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->articleRepository = $this->manager->getRepository(Article::class);

        foreach ($this->articleRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Article index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'article[title]' => 'Testing',
            'article[description]' => 'Testing',
            'article[slug]' => 'Testing',
            'article[createdAt]' => 'Testing',
            'article[updatedAt]' => 'Testing',
            'article[category]' => 'Testing',
            'article[user]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->articleRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setSlug('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCategory('My Title');
        $fixture->setUser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Article');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setSlug('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setCategory('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'article[title]' => 'Something New',
            'article[description]' => 'Something New',
            'article[slug]' => 'Something New',
            'article[createdAt]' => 'Something New',
            'article[updatedAt]' => 'Something New',
            'article[category]' => 'Something New',
            'article[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/article/');

        $fixture = $this->articleRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getCategory());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setSlug('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setCategory('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/article/');
        self::assertSame(0, $this->articleRepository->count([]));
    }
}
