<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Post;


#[AsCommand(
    name: 'app:get-posts',
    description: 'Get posts from jsonplaceholder.typicode.com/users and save to database',
)]

class GetPostsCommand extends Command
{
    public function __construct(HttpClientInterface $client, ManagerRegistry $doctrine)
    {
        $this->client = $client;
        $this->doctrine = $doctrine;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $posts_response = $this->client->request(
            'GET',
            'https://jsonplaceholder.typicode.com/posts'
        );
        $users_response = $this->client->request(
            'GET',
            'https://jsonplaceholder.typicode.com/users'
        );

        $posts = $posts_response->getContent();
        $posts = $posts_response->toArray();

        $users = $users_response->getContent();
        $users = $users_response->toArray();

        $entityManager = $this->doctrine->getManager();

        $currentValue = 0;
        
        while (!empty($posts[$currentValue]['title'])){

            $userId = $posts[$currentValue]['userId'];

            $post = new Post();
            $post->setName($users[$userId-1]['name']);
            $post->setTitle($posts[$currentValue]['title']);
            $post->setBody($posts[$currentValue]['body']);

            $entityManager->persist($post);

            $entityManager->flush();

            $currentValue++;
        }
        
        $io = new SymfonyStyle($input, $output);

        $io->success('Data saved in the database');

        return Command::SUCCESS;
    }
}