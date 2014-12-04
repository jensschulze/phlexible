<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Queue;

use Phlexible\Bundle\GuiBundle\Properties\Properties;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Exception\AlreadyRunningException;
use Phlexible\Bundle\MediaCacheBundle\Queue as BaseQueue;
use Phlexible\Bundle\MediaCacheBundle\Worker\WorkerResolver;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Queue processor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class QueueProcessor
{
    /**
     * @var WorkerResolver
     */
    private $workerResolver;

    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var Properties
     */
    private $properties;

    /**
     * @var string
     */
    private $lockDir;

    /**
     * @param WorkerResolver           $workerResolver
     * @param SiteManager              $siteManager
     * @param TemplateManagerInterface $templateManager
     * @param Properties               $properties
     * @param string                   $lockDir
     */
    public function __construct(
        WorkerResolver $workerResolver,
        SiteManager $siteManager,
        TemplateManagerInterface $templateManager,
        Properties $properties,
        $lockDir)
    {
        $this->workerResolver = $workerResolver;
        $this->siteManager = $siteManager;
        $this->templateManager = $templateManager;
        $this->properties = $properties;
        $this->lockDir = $lockDir;
    }

    /**
     * @param Queue    $queue
     * @param callable $callback
     *
     * @return CacheItem
     */
    public function processQueue(Queue $queue, callable $callback = null)
    {
        $lock = $this->lock();
        foreach ($queue->all() as $cacheItem) {
            $this->doProcess($cacheItem, $callback);
        }
        $lock->release();
    }

    /**
     * @param CacheItem $cacheItem
     * @param callable  $callback
     *
     * @return CacheItem
     */
    public function processItem(CacheItem $cacheItem, callable $callback = null)
    {
        $lock = $this->lock();
        $cacheItem = $this->doProcess($cacheItem, $callback);
        $lock->release();

        return $cacheItem;
    }

    /**
     * @return LockHandler
     * @throws AlreadyRunningException
     */
    private function lock()
    {
        $lock = new LockHandler('mediacache_lock', $this->lockDir);
        if (!$lock->lock(false)) {
            throw new AlreadyRunningException('Another cache worker process running.');
        }

        return $lock;
    }

    /**
     * @param CacheItem $cacheItem
     * @param callable  $callback
     *
     * @return CacheItem
     */
    private function doProcess(CacheItem $cacheItem, callable $callback = null)
    {
        $site = $this->siteManager->getSiteById($cacheItem->getSiteId());
        $file = $site->findFile($cacheItem->getFileId(), $cacheItem->getFileVersion());

        $template = $this->templateManager->find($cacheItem->getTemplateKey());

        $worker = $this->workerResolver->resolve($template, $file);
        if (!$worker) {
            if ($callback) {
                call_user_func($callback, 'no_worker', null, $cacheItem);
            }

            return null;
        }

        $cacheItem = $worker->process($template, $file);

        if ($callback) {
            if (!$cacheItem) {
                call_user_func($callback, 'no_cacheitem', $worker, $cacheItem);
            } else {
                call_user_func($callback, $cacheItem->getCacheStatus(), $worker, $cacheItem);
            }
        }

        $this->properties->set('mediacache', 'last_run', date('Y-m-d H:i:s'));

        return $cacheItem;
    }
}