<?php

namespace Tourze\ProductCoreBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Repository\SpuRepository;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '* * * * *')]
#[AsCommand(name: self::NAME, description: '自动上架商品')]
class AutoReleaseSpuCommand extends Command
{
    public const NAME = 'product:auto-release-spu';
    public function __construct(
        private readonly SpuRepository $spuRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = CarbonImmutable::now()->format('Y-m-d H:i:s');
        $query = $this->spuRepository->createQueryBuilder('s')
            ->where('(s.valid =:valid OR s.valid IS NULL)')
            ->andWhere('s.autoReleaseTime < :now')
            ->setParameter('valid', false)
            ->setParameter('now', $now)
            ->getQuery()
            ->toIterable();
        /** @var Spu $spu */
        foreach ($query as $spu) {
            $skus = $spu->getSkus();
            if ($skus->isEmpty()) {
                $output->writeln("当前spu没有sku【{$spu->getId()}】");
                continue;
            }
            // 过了自动下架时间的不要再上架
            if (!empty($spu->getAutoTakeDownTime()) && $spu->getAutoTakeDownTime()->format('Y-m-d H:i:s') < $now) {
                continue;
            }
            try {
                $spu->setValid(true);
                $this->entityManager->persist($spu);
                $this->entityManager->flush();
            } catch (\Exception $exception) {
                $this->logger->error('自动上架商品,一个错误的话，不全部跳过', [
                    'error' => $exception,
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
