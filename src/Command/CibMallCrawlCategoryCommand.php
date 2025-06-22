<?php

namespace ProductBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use HttpClientBundle\Service\SmartHttpClient;
use ProductBundle\Entity\Category;
use ProductBundle\Repository\CategoryRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Json\Json;

#[AsCommand(name: self::NAME, description: '采集兴业银行商城分类数据')]
class CibMallCrawlCategoryCommand extends Command
{
    public const NAME = 'product:cib-mall:crawl-category';
    public function __construct(
        private readonly SmartHttpClient $httpClient,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->httpClient
            ->request('POST', 'https://mall.cib.com.cn/unified-mall-web/api/goods/getGoodsCategory', [
                'body' => [
                    'json' => Json::encode((object) []),
                ],
            ])
            ->getContent();
        $response = Json::decode($response);
        $this->loopSave($response['goodsCategorys']);

        return Command::SUCCESS;
    }

    private function loopSave(array $data, int $level = 0, ?Category $parent = null)
    {
        foreach ($data as $datum) {
            $remark = "CIB-CATEGORY-{$datum['id']}";
            $category = $this->categoryRepository->findOneBy(['remark' => $remark]);
            if ($category === null) {
                $category = new Category();
                $category->setRemark($remark);
            }

            $category->setTitle($datum['name']);
            $category->setParent($parent);
            $category->setLogoUrl("https://file.cibfintech.com/{$datum['pic']}");
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            if (isset($datum['subList']) && (is_countable($datum['subList']) ? count($datum['subList']) : 0) > 0) {
                $this->loopSave($datum['subList'], $level + 1, $category);
            }
        }
    }
}
