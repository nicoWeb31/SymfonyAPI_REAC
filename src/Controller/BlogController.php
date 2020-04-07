<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{

    
    /**
     * @Route("/{page}", name="blog_list", defaults={"page":5},requirements = {"page"="\d+"},methods={"GET"})
     */
    public function list($page=1, Request $req,BlogPostRepository $repo)
    {

        $limit = $req->get('limit',10);
        $post = $repo->findAll();
        return $this->json(
            [
                'page'=>$page,
                'limit'=>$limit,
                'data'=> array_map(function(BlogPost $post){
                    return $this->generateUrl('blog_by_slug',['slug'=>$post->getSlug()]);
                },$post)
            ]
        );       
        

        
    }


    /**
     * @Route("/post/{id}", name="blog_by_id", requirements = {"id"="\d+"},methods={"GET"})
     */
    public function post(BlogPost $post,BlogPostRepository $repo)
    {
        return $this->json (

            $repo->find($post)
        );
    }


    /**
     * @Route("/post/{slug}", name="blog_by_slug",methods={"GET"})
     */
    public function postBySlug(BlogPost $post,BlogPostRepository $repo)
    {
        return $this->json (
            $repo->find($post)
        );
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add(Request $req, EntityManagerInterface $man, SerializerInterface $serializer)
    {
        $blogPost = $serializer->deserialize($req->getContent(),BlogPost::class,'json');
        $man->persist($blogPost);
        $man->flush();
        return $this->json($blogPost);
    }

    /**
     * @Route("/post/delete/{id}", name="blog_delete",methods={"DELETE"})
     */
    public function delete(BlogPost $post,Request $req, EntityManagerInterface $man)
    {
        $man->remove($post);
    
        $man->persist($post);
        $man->flush();
        return $this->json(null,Response::HTTP_NO_CONTENT);
    }
    

}
