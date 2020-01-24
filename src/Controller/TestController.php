<?php

namespace App\Controller;

use App\Model\Product;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Liip\Serializer\Context;
use Liip\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Safe\json_encode;

class TestController extends AbstractController
{
    /**
     * @var string
     */
    private $cacheDirectory;
    /**
     * @var SerializerInterface
     */
    private $jms;

    public function __construct(string $cacheDirectory, SerializerInterface $jms)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->jms = $jms;
    }

    /**
     * @Route("/products")
     */
    public function getProducts(Request $request)
    {
        $version = $request->query->get('version');
        $view = $request->query->get('view');
        $groups = ['api', 'product-details'];

        $product = new Product();
        $product->id = '1337';
        $product->name = 'ElePHPant';
        $product->region = 'Benelux';
        $product->addTag('at-benelux');
        $product->addTag('fluffy');

        $serializer = new Serializer($this->cacheDirectory);

        // The JMS serializer way
        $jmsContext = new SerializationContext();
        $jmsContext->setVersion($version);
        $jmsContext->setGroups($groups);

        $jmsdata = $this->jms->serialize($product, 'json', $jmsContext);

        // The Liip serializer way
        $liipContext = new Context();
        $liipContext->setVersion($version);
        $liipContext->setGroups($groups);

        $liipdata = $serializer->serialize($product, 'json', $liipContext);

        return new Response($liipdata);
    }
}
