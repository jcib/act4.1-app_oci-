<?php

namespace App\Controller;

use App\Entity\Preferencia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpClient\HttpClient;


class ListController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function index(Request $request)
    {
        $pref = new Preferencia();
        $content = array();

        $form = $this->createFormBuilder($pref)
            ->add('tipus', ChoiceType::class, [
                'choices' =>[
                    'Cine' => 'cine',
                    'Llibres' => 'llibres',
                    'Música' => 'musica'                ]
            ])
            ->add('localitzacio', ChoiceType::class, [
                'choices' =>[
                    'Barcelona' => 'barcelona',
                    'Madrid' => 'madrid',
                    'València' => 'valencia',
                    'Bilbao' => 'bilbao',
                ]
            ])       
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $tipus_pref = $form['tipus']->getData();
            $localitzacio_pref = $form['localitzacio']->getData();
            
            if($tipus_pref == "cine") {
                // API de cine creada por nosotros 
                $client = HttpClient::create();
                $response = $client->request('GET', 'http://127.0.0.1:43253/api/pelis');
                $content = $response->getContent();
                $content = $response->toArray();

                return $this->render('list/index.html.twig', [
                    'form' => $form->createView(),
                    'content' => $content
                ]);
            // API externas
           } else if($tipus_pref == "llibres") {
                $client = HttpClient::create();
                $response = $client->request('GET', 'http://127.0.0.1:43253/api/pelis');
                $content = $response->getContent();
                $content = $response->toArray();

                return $this->render('list/index.html.twig', [
                    'form' => $form->createView(),
                    'content' => $content
                ]);
            } else if($tipus_pref == "musica") {
                $client = HttpClient::create();
                $response = $client->request('GET', 'https://deezerdevs-deezer.p.rapidapi.com/search?q=eminem', [
                    'headers' => [
                        "x-rapidapi-host" => "deezerdevs-deezer.p.rapidapi.com",
                        "x-rapidapi-key" => "ac81091c7fmsh3487d369d1749f9p16ab36jsn00ff3337a5b3"
                    ]]);
                $content = $response->getContent();
                $content = $response->toArray();

                return $this->render('list/index.html.twig', [
                    'form' => $form->createView(),
                    'content' => $content
                ]);
            }            
        }

        return $this->render('list/index.html.twig', [
            'form' => $form->createView(),
            'content' => $content
        ]);
    }
}