<?php

namespace ProductCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class TempController extends AbstractController
{
    #[Route('/goods/temp/image')]
    #[Route('/mall/temp/image')]
    public function __invoke()
    {
        $images = [
            'https://img20.360buyimg.com/jdcms/s300x300_jfs/t1/109165/10/34331/55262/63ccb8d4Fd3e2422c/0195804b335d3e6d.jpg.webp',
            'https://img14.360buyimg.com/jdcms/s300x300_jfs/t1/184813/6/32407/93578/63c4b78cF8cd1d5e0/a625dc352c71a368.jpg.webp',
            'https://img14.360buyimg.com/jdcms/s300x300_jfs/t1/48939/19/17650/87792/63d33be0F22289066/834790f0c8394c6f.jpg.webp',
            'https://img13.360buyimg.com/jdcms/s300x300_jfs/t1/112167/28/26499/1018938/626c0cc1E5dae3d4d/4eadf7e37ae6455f.png.webp',
            'https://img11.360buyimg.com/jdcms/s300x300_jfs/t1/107270/10/33069/113683/63ae90c2Fde4a9535/3a9cdcb8939b9f1d.jpg.webp',
            'https://img12.360buyimg.com/jdcms/s300x300_jfs/t1/119056/35/31950/161619/63d4d6f4F4e4f80ce/19903d3472e75ddc.jpg.webp',
            'https://img14.360buyimg.com/jdcms/s300x300_jfs/t1/80836/39/21395/320590/62e1043cEbbc0babe/a6597dd951886e97.jpg.webp',
            'https://img13.360buyimg.com/jdcms/s300x300_jfs/t1/192593/12/28733/105793/634e41ecE7388a76a/ea34928c7b795664.jpg.webp',
            'https://img10.360buyimg.com/jdcms/s300x300_jfs/t1/71937/32/22892/136165/63929e73E4190caf5/47f1cc73a2a74fe6.jpg.webp',
            'https://img14.360buyimg.com/jdcms/s300x300_jfs/t1/204678/11/30680/62563/63a91ff7E69e1dec2/185cad0a784bda66.jpg.webp',
            'https://img10.360buyimg.com/jdcms/s300x300_jfs/t1/88411/32/25573/160800/62480547Ea3b90590/09951622ad0f3dfe.jpg.webp',
            'https://img20.360buyimg.com/jdcms/s300x300_jfs/t1/188656/39/29392/163274/63d1f353F9d8031fe/2ea012940887875d.jpg.webp',
        ];

        $k = array_rand($images);

        return $this->redirect($images[$k]);
    }
}
