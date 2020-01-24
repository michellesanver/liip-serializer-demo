<?php

declare(strict_types=1);

namespace App\Model;

use JMS\Serializer\Annotation as Serializer;
use function Safe\json_encode;

/**
 * Represents the Brand information.
 *
 * @Serializer\AccessorOrder("custom", custom={
 *     "id", "name", "slug", "title", "abstract", "description",
 *     "tags", "tagsInV3", "getTagsInV3",
 *     "links", "linksInV3", "getLinksInV3",
 *     "keywords", "keywordsInV3", "getKeywordsInV3",
 *     "facetValue", "getFacetValue"
 * })
 *
 */
class Product extends Taggable
{
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"api"})
     */
    public $id;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"api"})
     */
    public $name;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Since("2")
     * @Serializer\Groups({"product-details"})
     */
    public $region;

    /**
     * Tags for flagging certain products. The tags until version 2 also in list view.
     *
     * @var string[]
     * @Serializer\Until("2")
     * @Serializer\Type("array<string>")
     * @Serializer\Accessor(getter="getTags", setter="setTags")
     * @Serializer\Groups({"api"})
     */
    protected $tags = [];

    /**
     * Tags for flagging certain products.
     *
     * @Serializer\Since("3")
     * @Serializer\Type("array<string>")
     * @Serializer\Groups({"product-details"})
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("tags")
     *
     * @return string[]|null
     */
    public function getTagsInV3(): ?array
    {
        return $this->tags ?: null;
    }

    /**
     * The value for faceting, containing all facet information
     * that we need to enhance the facet for the response.
     *
     * This is an associative array encoded as JSON,
     * with the following keys: 'code' and 'name'.
     *
     * @Serializer\Type("string")
     * @Serializer\Groups({"product-details"})
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("facet_value")
     */
    public function getFacetValue(): string
    {
        return json_encode([
            'code' => $this->id,
            'name' => $this->name,
        ], \JSON_UNESCAPED_SLASHES | \JSON_PRESERVE_ZERO_FRACTION);
    }
}
