<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/blog", name="blog_page" )
 */
class BlogController extends AbstractController{

    private const POSTS = [
        [
            'id' => 1,
            'slug' => 'hello-world',
            'title'=> 'HELLO WORLD',
        ],
        [
            'id' => 2,
            'slug' => 'hello-01',
            'title'=> 'HELLO 01',
        ]
    ];

    /**
     * @Route("/", name="blog_list", defaults={"page": 1},requirements={"page"="\d+"} )
     */
    public function liste(ManagerRegistry $doctrine,$page = 1, Request $request){
        $limit = $request->get('limit',10);
        $repository = $doctrine->getRepository(BlogPost::class);
        $items = $repository->findAll();
        return $this->json(
            [
                "page" => $page,
                "limit"=> $limit,
                "data" => self::POSTS
            ]
        );
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     */
    public function add(ManagerRegistry $doctrine){
        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $request = new Request();

        $serializer = new Serializer($normalizers, $encoders);
        
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $entityManager = $doctrine->getManager();
        $entityManager->persist($blogPost);
        $entityManager->flush();
 
        return $this->json($blogPost);
    }

    /**
     * @Route("/{id}", name="blog_by_id" )
     */
    public function post($id){
        return new JsonResponse(
            array_search($id, array_column(self::POSTS,'id'))
        );
    }

    /**
     * @Route("/{slug}", name="blog_by_slug" )
     */
    public function postBySlug($slug){
        return new JsonResponse(
            array_search($slug, array_column(self::POSTS,'slug'))
        );
    }

}