<?php

namespace Supermetrolog\Synchronizer;

use Supermetrolog\Synchronizer\interfaces\AlreadySynchronizedRepositoryInterface;
use Supermetrolog\Synchronizer\interfaces\SourceRepositoryInterface;
use Supermetrolog\Synchronizer\interfaces\TargetRepositoryInterface;

class Repositories
{
    public AlreadySynchronizedRepositoryInterface $alreadyRepo;
    public SourceRepositoryInterface $sourceRepo;
    public TargetRepositoryInterface $targetRepo;

    public function __construct(
        AlreadySynchronizedRepositoryInterface $alreadyRepo,
        SourceRepositoryInterface $sourceRepo,
        TargetRepositoryInterface $targetRepo
    ) {
        $this->alreadyRepo = $alreadyRepo;
        $this->sourceRepo = $sourceRepo;
        $this->targetRepo = $targetRepo;
    }
}
