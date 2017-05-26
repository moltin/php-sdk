<?php

require_once('./init.php');

try {

    $categoryIds = [];
    $unique = sha1(rand(0,5000) . microtime());

    $cleanup = true;

    // create a category
    $categoryCreate = $moltin->categories->create([
        'type' => 'category',
        'name' => 'My First Category',
        'slug' => 'my-first-category-' . $unique
    ]);

    if ($categoryCreate->getStatusCode() === 201) {

        $categoryId = $categoryCreate->data()->id;
        $categoryIds[] = $categoryId;
        echo "Category ($categoryId) created (" . $categoryCreate->getExecutionTime() . "s)\n";

    }

    // create a second category
    $categoryCreate = $moltin->categories->create([
        'type' => 'category',
        'name' => 'My Second Category',
        'slug' => 'my-second-category-' . $unique
    ]);

    if ($categoryCreate->getStatusCode() === 201) {

        $categoryId = $categoryCreate->data()->id;
        $categoryIds[] = $categoryId;
        echo "Category ($categoryId) created (" . $categoryCreate->getExecutionTime() . "s)\n";

    }

    // create a third category
    $categoryCreate = $moltin->categories->create([
        'type' => 'category',
        'name' => 'My Third Category',
        'slug' => 'my-third-category-' . $unique
    ]);

    if ($categoryCreate->getStatusCode() === 201) {

        $categoryId = $categoryCreate->data()->id;
        $categoryIds[] = $categoryId;
        echo "Category ($categoryId) created (" . $categoryCreate->getExecutionTime() . "s)\n";

    }

    function printTree($data) {
        echo "Tree: \t" . print_r($data, true);
    }

    printTree($moltin->categories->get('tree')->data());

    echo "make second a child of first:\n";
    $moltin->categories->createRelationships($categoryIds[0], 'children', [$categoryIds[1]]);
    printTree($moltin->categories->tree()->data());

    echo "move second to be a child of third:\n";
    $moltin->categories->createRelationships($categoryIds[1], 'parent', $categoryIds[2]);
    printTree($moltin->categories->tree()->data());

    echo "make second to be a child of first again:\n";
    $moltin->categories->createRelationships($categoryIds[1], 'parent', $categoryIds[0]);
    printTree($moltin->categories->tree()->data());

    echo "make third the parent of first:\n";
    $moltin->categories->createRelationships($categoryIds[2], 'children', [$categoryIds[0]]);
    printTree($moltin->categories->tree()->data());

    echo "remove children of third:\n";
    $moltin->categories->updateRelationships($categoryIds[2], 'children', []);
    printTree($moltin->categories->tree()->data());

    // clean up
    if ($cleanup) {

        // remove the categories
        foreach($categoryIds as $categoryId) {
            $deleteCategory = $moltin->categories->delete($categoryId);
            if ($deleteCategory->getStatusCode() === 200) {
                echo "Category ($categoryId) deleted (" . $deleteCategory->getExecutionTime() . "s)\n";
            }
        }
    }

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
