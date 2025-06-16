<?php

namespace ProductBundle\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\Spu;
use ProductBundle\Repository\SpuRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask('* * * * *')]
#[AsCommand(name: 'product:auto-take-down-spu', description: '自动下架商品')]
class AutoTakeDownSpuCommand extends Command
{
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
        $query = $this->spuRepository->createQueryBuilder('s')
            ->where('s.valid=:valid')
            ->andWhere('s.autoTakeDownTime IS NOT NULL')
            ->andWhere('s.autoTakeDownTime < :now')
            ->setParameter('valid', true)
            ->setParameter('now', Carbon::now()->format('Y-m-d H:i:s'))
            ->getQuery()
            ->toIterable();
        /** @var Spu $spu */
        foreach ($query as $spu) {
            try {
                $spu->setValid(false);
                $this->entityManager->persist($spu);
                $this->entityManager->flush();
            } catch (\Throwable $exception) {
                $this->logger->error('自动下架商品，一个错误的话，不全部跳过', [
                    'error' => $exception,
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
