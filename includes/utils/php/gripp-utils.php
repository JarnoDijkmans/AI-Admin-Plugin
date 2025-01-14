<?php
  /**
 * Connect the productId from the database to the gripp product id (hardcoded).
 *
 * @param int $productId The productId to check.
 * @return int|null Returns the Gripp product ID if found, or null if not mapped.
 */
function connect_productIds($productId) {
    $product_map = [
        1 => 101, // id: Blogposts
        2 => 1140, // id: Producten
        5 => 103, // id: Afbeeldingen
        6 => 1135 // id: landingspaginas
    ];

    // Return the corresponding Gripp ID or null if not found
    return isset($product_map[$productId]) ? $product_map[$productId] : null;
}
?>