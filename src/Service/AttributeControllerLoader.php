<?php

namespace ProductCoreBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\RouteCollection;

#[AutoconfigureTag('controller.service_arguments')]
class AttributeControllerLoader
{
    public function __construct(
        private readonly AnnotationClassLoader $controllerLoader
    ) {
    }

    /**
     * 自动加载控制器
     */
    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        
        $controllers = [
            \ProductCoreBundle\Controller\TempController::class,
        ];
        
        foreach ($controllers as $controller) {
            $collection->addCollection($this->controllerLoader->load($controller));
        }
        
        return $collection;
    }
}