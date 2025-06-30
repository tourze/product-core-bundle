<?php

namespace Tourze\ProductCoreBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\RouteCollection;

#[AutoconfigureTag(name: 'controller.service_arguments')]
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
            \Tourze\ProductCoreBundle\Controller\TempController::class,
        ];
        
        foreach ($controllers as $controller) {
            $collection->addCollection($this->controllerLoader->load($controller));
        }
        
        return $collection;
    }
}