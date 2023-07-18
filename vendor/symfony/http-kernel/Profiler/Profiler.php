<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpKernel\Profiler;

use EasyCI202307\Psr\Log\LoggerInterface;
use EasyCI202307\Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\HttpFoundation\Response;
use EasyCI202307\Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use EasyCI202307\Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use EasyCI202307\Symfony\Contracts\Service\ResetInterface;
/**
 * Profiler.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Profiler implements ResetInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\Profiler\ProfilerStorageInterface
     */
    private $storage;
    /**
     * @var DataCollectorInterface[]
     */
    private $collectors = [];
    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    private $logger;
    /**
     * @var bool
     */
    private $initiallyEnabled = \true;
    /**
     * @var bool
     */
    private $enabled = \true;
    public function __construct(ProfilerStorageInterface $storage, LoggerInterface $logger = null, bool $enable = \true)
    {
        $this->storage = $storage;
        $this->logger = $logger;
        $this->initiallyEnabled = $this->enabled = $enable;
    }
    /**
     * Disables the profiler.
     */
    public function disable()
    {
        $this->enabled = \false;
    }
    /**
     * Enables the profiler.
     */
    public function enable()
    {
        $this->enabled = \true;
    }
    public function isEnabled() : bool
    {
        return $this->enabled;
    }
    /**
     * Loads the Profile for the given Response.
     */
    public function loadProfileFromResponse(Response $response) : ?Profile
    {
        if (!($token = $response->headers->get('X-Debug-Token'))) {
            return null;
        }
        return $this->loadProfile($token);
    }
    /**
     * Loads the Profile for the given token.
     */
    public function loadProfile(string $token) : ?Profile
    {
        return $this->storage->read($token);
    }
    /**
     * Saves a Profile.
     */
    public function saveProfile(Profile $profile) : bool
    {
        // late collect
        foreach ($profile->getCollectors() as $collector) {
            if ($collector instanceof LateDataCollectorInterface) {
                $collector->lateCollect();
            }
        }
        if (!($ret = $this->storage->write($profile)) && null !== $this->logger) {
            $this->logger->warning('Unable to store the profiler information.', ['configured_storage' => \get_class($this->storage)]);
        }
        return $ret;
    }
    /**
     * Purges all data from the storage.
     */
    public function purge()
    {
        $this->storage->purge();
    }
    /**
     * Finds profiler tokens for the given criteria.
     *
     * @param string|null $limit The maximum number of tokens to return
     * @param string|null $start The start date to search from
     * @param string|null $end   The end date to search to
     *
     * @see https://php.net/datetime.formats for the supported date/time formats
     */
    public function find(?string $ip, ?string $url, ?string $limit, ?string $method, ?string $start, ?string $end, string $statusCode = null) : array
    {
        return $this->storage->find($ip, $url, $limit, $method, $this->getTimestamp($start), $this->getTimestamp($end), $statusCode);
    }
    /**
     * Collects data for the given Response.
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null) : ?Profile
    {
        if (\false === $this->enabled) {
            return null;
        }
        $profile = new Profile(\substr(\hash('sha256', \uniqid(\mt_rand(), \true)), 0, 6));
        $profile->setTime(\time());
        $profile->setUrl($request->getUri());
        $profile->setMethod($request->getMethod());
        $profile->setStatusCode($response->getStatusCode());
        try {
            $profile->setIp($request->getClientIp());
        } catch (ConflictingHeadersException $exception2) {
            $profile->setIp('Unknown');
        }
        if ($prevToken = $response->headers->get('X-Debug-Token')) {
            $response->headers->set('X-Previous-Debug-Token', $prevToken);
        }
        $response->headers->set('X-Debug-Token', $profile->getToken());
        foreach ($this->collectors as $collector) {
            $collector->collect($request, $response, $exception);
            // we need to clone for sub-requests
            $profile->addCollector(clone $collector);
        }
        return $profile;
    }
    public function reset()
    {
        foreach ($this->collectors as $collector) {
            $collector->reset();
        }
        $this->enabled = $this->initiallyEnabled;
    }
    /**
     * Gets the Collectors associated with this profiler.
     */
    public function all() : array
    {
        return $this->collectors;
    }
    /**
     * Sets the Collectors associated with this profiler.
     *
     * @param DataCollectorInterface[] $collectors An array of collectors
     */
    public function set(array $collectors = [])
    {
        $this->collectors = [];
        foreach ($collectors as $collector) {
            $this->add($collector);
        }
    }
    /**
     * Adds a Collector.
     */
    public function add(DataCollectorInterface $collector)
    {
        $this->collectors[$collector->getName()] = $collector;
    }
    /**
     * Returns true if a Collector for the given name exists.
     *
     * @param string $name A collector name
     */
    public function has(string $name) : bool
    {
        return isset($this->collectors[$name]);
    }
    /**
     * Gets a Collector by name.
     *
     * @param string $name A collector name
     *
     * @throws \InvalidArgumentException if the collector does not exist
     */
    public function get(string $name) : DataCollectorInterface
    {
        if (!isset($this->collectors[$name])) {
            throw new \InvalidArgumentException(\sprintf('Collector "%s" does not exist.', $name));
        }
        return $this->collectors[$name];
    }
    private function getTimestamp(?string $value) : ?int
    {
        if (null === $value || '' === $value) {
            return null;
        }
        try {
            $value = new \DateTime(\is_numeric($value) ? '@' . $value : $value);
        } catch (\Exception $exception) {
            return null;
        }
        return $value->getTimestamp();
    }
}
