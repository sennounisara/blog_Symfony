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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/blog", name="blog_page" )
 */
class BlogController extends AbstractController{

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
                "data" => $items 
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
     * @Route("/post/{id}", name="blog_by_id" , methods={"GET"} )
     */
    public function post(BlogPost $post){
        return $this->json(
            // It's the same as doing find($id) on repository
            $post
        );
    }

    /**
     * @Route("/{slag}", name="blog_by_slug" )
     */
    public function postBySlug(BlogPost $post){
        return $this->json(
            // It's the same as doing findBy(['slag' => contents of {slag}]) on repository
            $post
        );
    }

    /**
     * @Route("/post/{id}", name="delete_blog_by_id", methods={"DELETE"} )
     */
    public function delete(ManagerRegistry $doctrine,BlogPost $post){
        $entityManager = $doctrine->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}