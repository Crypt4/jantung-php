<?php

namespace Crypt4\Jantung\Data;

use Crypt4\Jantung\Metric\Contract;
use Crypt4\Jantung\Metric\Metric;
use Ramsey\Uuid\Uuid;

class Entry
{
    /**
     * The entry's UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * The entry's type.
     *
     * @var string
     */
    public $type;

    /**
     * The entry's family hash.
     *
     * @var string|null
     */
    public $hashFamily;

    /**
     * The currently request metric.
     *
     * @var \Crypt4\Jantung\Metric\Metric
     */
    public $metric;

    /**
     * The entry's content.
     *
     * @var array
     */
    public $content = [];

    /**
     * The entry's tags.
     *
     * @var array
     */
    public $tags = [];

    /**
     * The DateTime that indicates when the entry was recorded.
     *
     * @var \DateTimeInterface
     */
    public $recorded_at;

    /**
     * Create a new incoming entry instance.
     *
     * @param  string|null  $uuid
     * @return void
     */
    public function __construct($type, array $content, $uuid = null)
    {
        $this->uuid = $uuid ?: (string) Uuid::uuid4()->toString();

        $this->type = $type;

        $this->recorded_at = new \DateTimeImmutable();

        $this->content = $content;

        $this->metric = new Metric;
    }

    /**
     * Create a new entry instance.
     *
     * @param  mixed  ...$arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    /**
     * Assign the entry a given type.
     *
     * @return $this
     */
    public function type(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Assign the entry a family hash.
     *
     * @param  null|string  $hashFamily
     * @return $this
     */
    public function setHashFamily($hashFamily)
    {
        $this->hashFamily = $hashFamily;

        return $this;
    }

    /**
     * Merge tags into the entry's existing tags.
     *
     * @return $this
     */
    public function tags(array $tags)
    {
        $this->tags = array_unique(array_merge($this->tags, $tags));

        return $this;
    }

    /**
     * Determine if the incoming entry has a monitored tag.
     *
     * @return bool
     */
    public function hasMonitoredTag()
    {
        return ! empty($this->tags);
    }

    /**
     * Determine if the incoming entry is an exception.
     *
     * @return bool
     */
    public function isException()
    {
        return $this->type === Type::EXCEPTION;
    }

    /**
     * Get the family look-up hash for the incoming entry.
     *
     * @return string|null
     */
    public function getHashFamily()
    {
        return $this->hashFamily;
    }

    public function getType()
    {
        return $this->type;
    }

    public function addMetric(Contract $contract)
    {
        $this->metric->add($contract);

        return $this;
    }

    /**
     * Get an array representation of the entry for storage.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'uuid' => $this->uuid,
            'hash_family' => $this->getHashFamily(),
            'type' => $this->getType(),
            'content' => $this->content,
            'meta' => $this->metric->toArray(),
            'created_at' => $this->recorded_at->format('Y-m-d H:i:s'),
        ];
    }
}
