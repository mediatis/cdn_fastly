<?php

declare(strict_types=1);

namespace HDNET\CdnFastly\Cache;

use HDNET\CdnFastly\Service\FastlyService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class FastlyBackend extends NullBackend implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var FastlyService
     */
    protected $fastlyService;

    public function initializeObject(): void
    {
        try {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $this->injectFastlyService($objectManager->get(FastlyService::class));
        } catch (\Exception $exception) {
            $this->logger->error('Fasty service was not build');
        }
    }

    public function injectFastlyService(FastlyService $fastlyService)
    {
        $this->fastlyService = $fastlyService;
    }

    public function flush(): void
    {
        if (null === $this->fastlyService) {
            $this->logger->error('Fasty service was not build');

            return;
        }
        $this->fastlyService->purgeAll();
    }

    /**
     * @param string $tag
     */
    public function flushByTag($tag)
    {
        if (null === $this->fastlyService) {
            $this->logger->error('Fasty service was not build');

            return;
        }
        $this->fastlyService->purgeKey((string) $tag);
    }

    public function flushByTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->flushByTag($tag);
        }
    }
}
