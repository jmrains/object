<?php

$servername = "localhost";
$dbname = "testdb";
$username = "root";
$password = "";

include 'Product.php';
include 'ProductCategories.php';
include 'Categories.php';

if (isset($_POST['categoryid'])) {//entering this block means that we have to show Products in a Category
    
    $pcdto = new ProductCategoriesDTO();
    $pcdto->CategoryId = filter_input(INPUT_POST, 'categoryid');
    $pcs = new ProductCategoriesService();
    $pcs->FetchProductsByCategory($pcdto);
} else {//this block means that we have to show Categories
    
    $cs = new CategoriesService($servername, $dbname, $username, $password);
    $CategoriesDTOArray = array();
    $Categories = $cs->FetchCategories();
    echo "Back on the page!";
    //var_dump($ResultSet);//this prints information pulled from the db
    echo "<html><header></header><body>";
    foreach ($Categories as $Catitem) {
        echo "<h2>$Catitem->CategoryName</h2>";
        echo "<h2>$Catitem->CategoryId</h2>";
    }
    echo "</body></html>";
    echo "Foreach on the page has exited.";
}
?>