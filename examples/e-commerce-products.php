<?php

/**
 * Example: Scraping E-commerce Product Listings
 *
 * Demonstrates:
 * - Complex transformers (trim|float for prices)
 * - Multiple field types (text, attributes, numbers)
 * - Pagination with limit and offset
 * - Data type conversions
 */

use Scrawler\Bootstrap;
use Scrawler\Scrawler;

require_once dirname(__DIR__) . "/vendor/autoload.php";

// Handle PHP 8.4 deprecation warnings from vendor libraries
Bootstrap::init();

$scrawler = new Scrawler();

// Sample HTML (you would fetch from a real e-commerce site)
$html = '
<div class="products">
    <div class="product" data-id="123">
        <h3 class="title">  Wireless Headphones  </h3>
        <span class="price">  $129.99  </span>
        <div class="rating">4.5</div>
        <img src="/images/headphones.jpg" alt="Product Image">
        <a href="/products/wireless-headphones">View Details</a>
    </div>
    <div class="product" data-id="456">
        <h3 class="title">  Smart Watch  </h3>
        <span class="price">  $299.00  </span>
        <div class="rating">4.8</div>
        <img src="/images/watch.jpg" alt="Product Image">
        <a href="/products/smart-watch">View Details</a>
    </div>
    <div class="product" data-id="789">
        <h3 class="title">  Bluetooth Speaker  </h3>
        <span class="price">  $79.50  </span>
        <div class="rating">4.2</div>
        <img src="/images/speaker.jpg" alt="Product Image">
        <a href="/products/bluetooth-speaker">View Details</a>
    </div>
</div>
';

$schema = [
    "products" => [
        ".product" => [
            "id" => ["@data-id", "int"], // Extract attribute, convert to int
            "name" => [".title", "trim|ucwords"], // Clean and capitalize
            "price" => [".price", "trim|strip_tags|float"], // Remove $, convert to float
            "rating" => [".rating", "trim|float"], // Convert to float
            "image" => "img@src", // Simple attribute extraction
            "url" => "a@href", // Link to product page
        ],
        "offset" => 0, // Skip first 0 items
        "limit" => 10, // Take only 10 items
    ],
    "total_products" => "count(.product)", // Count total products (if needed)
];

$result = $scrawler->scrape($html, $schema, true);

header("Content-Type: application/json");
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

/* Output:
{
    "products": [
        {
            "id": 123,
            "name": "Wireless Headphones",
            "price": 129.99,
            "rating": 4.5,
            "image": "/images/headphones.jpg",
            "url": "/products/wireless-headphones"
        },
        {
            "id": 456,
            "name": "Smart Watch",
            "price": 299,
            "rating": 4.8,
            "image": "/images/watch.jpg",
            "url": "/products/smart-watch"
        },
        {
            "id": 789,
            "name": "Bluetooth Speaker",
            "price": 79.5,
            "rating": 4.2,
            "image": "/images/speaker.jpg",
            "url": "/products/bluetooth-speaker"
        }
    ]
}
*/
