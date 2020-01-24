<?php

declare(strict_types=1);

namespace App\Model;

/**
 * A model that can be tagged.
 */
abstract class Taggable
{
    /**
     * Tags for flagging certain objects.
     *
     * @var string[]
     */
    protected $tags = [];

    /**
     * Returns an array of tags.
     *
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Returns whether this object has the given tag.
     */
    public function hasTag(string $tag): bool
    {
        return \in_array($tag, $this->tags, true);
    }

    public function addTag(string $tag): void
    {
        if (!$this->hasTag($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * Removes multiple tags.
     *
     * @param string[] $tags An array of tags
     */
    public function removeTags(array $tags): void
    {
        $this->tags = array_values(array_diff($this->tags, $tags));
    }

    /**
     * Removes a tag.
     */
    public function removeTag(string $tag): void
    {
        $key = array_search($tag, $this->tags, true);
        if (false !== $key) {
            unset($this->tags[$key]);

            // This step is needed, as the above would remove a key in the
            // order of the array and serializer would then think them key=>value
            // pairs instead of just a flat array
            $this->tags = array_values($this->tags);
        }
    }

    /**
     * Adds multiple tags.
     *
     * @param string[]|null $tags An array of tags
     */
    public function addTags(?array $tags): void
    {
        foreach ($tags ?? [] as $tag) {
            $this->addTag($tag);
        }
    }

    /**
     * Overwrite the tags with a new list.
     *
     * @param string[]|null $tags
     */
    public function setTags(?array $tags): void
    {
        if (null === $tags) {
            $this->tags = [];

            return;
        }

        $this->tags = array_values(array_unique($tags));
    }
}
