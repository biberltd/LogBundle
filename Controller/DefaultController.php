<?php

namespace BiberLtd\Bundle\LogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BiberLtdLogBundle:Default:index.html.twig', array('name' => $name));
    }
}
