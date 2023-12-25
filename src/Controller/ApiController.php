<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/listeRegion", name="listeRegions")
     */
    public function listeRegion(SerializerInterface $serializer)
    {
        $mesRegions=file_get_contents('https://geo.api.gouv.fr/regions');
        $mesRegionsTab = $serializer->decode($mesRegions,'json');
        $mesRegionObjet = $serializer->denormalize($mesRegionsTab,'App\Entity\Region[]');
        $mesRegions = $serializer->deserlize($mesRegions);
        return $this->render('api/index.html.twig', [
            'mesRegions'=>$mesRegionObjet
        ]);
    }

     /**
     * @Route("/listeDepsParRegion", name="listeDepsParRegions")
     */
    public function listeDepsParRegion(Request $request, SerializerInterface $serializer)
    {
        $codeRegion = $request->query->get('region');
        
        // Fetch the regions
        $mesRegions = file_get_contents('https://geo.api.gouv.fr/regions');
        
        // Decode the regions
        $mesRegionsArray = $serializer->deserialize($mesRegions, 'App\Entity\Region[]', 'json');
        
        // Utilisez toutes les régions par défaut
        $mesRegion = $mesRegionsArray;
    
        // Convertir $mesRegion en tableau
        $mesRegion = array_values($mesRegion);
    
        if ($codeRegion == null || $codeRegion == "Toutes") {
            $mesDeps = file_get_contents('https://geo.api.gouv.fr/departements');
        } else {
            $mesDeps = file_get_contents('https://geo.api.gouv.fr/regions/'.$codeRegion.'/departements');
        }
    
        // Decode the departments
        $mesDepsArray = $serializer->decode($mesDeps, 'json');
    
        return $this->render('api/listeDep.html.twig', [
            'mesRegions' => $mesRegion,
            'mesDeps' => $mesDepsArray,
        ]);
    }
    
    



}