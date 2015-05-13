<?php

try {
    $servername = "localhost"; //these credentials will have to be stored elsewhere; I've seen them hard-coded into the class definition of PDO
    $dbname = "testdb";
    $username = "root";
    $password = "";

    include 'Product.php';
    include 'ProductCategories.php';
    include 'Header.html';

    /* Extra small devices (phones, less than 768px)
      /* No media query since this is the default in Bootstrap

      /* Small devices (tablets, 768px and up)
      @media (min-width: @screen-sm-min) { ... }

      /* Medium devices (desktops, 992px and up)
      @media (min-width: @screen-md-min) { ... }

      /* Large devices (large desktops, 1200px and up)
      @media (min-width: @screen-lg-min) { ... }
     */

    $pdto = new ProductDTO ();
    if (isset($_POST['lookupid'])) {
        $ReceptorProdCatArray = array();
        $productid = filter_input(INPUT_POST, 'lookupid');
        $ps = new ProductService($servername, $dbname, $username, $password);
        $pdto = $ps->FetchProductById($productid, $pdto);        
        $pcs = new ProductCategoriesService($servername, $dbname, $username, $password);
        $pcs->FetchProductCategories($productid, $ReceptorProdCatArray);
        var_dump($ReceptorProdCatArray);
        
        if (null != $pdto->Name) {
            echo "<table class=\"table table-bordered\">"
            . "<tr><th>Product Name</th><th>Product Id</th><th>Product Make</th><th>Product Model</th><th>Product Price</th><th>Product Description</th><th>Product Categories</th></tr>"
            . "<tr><td> $pdto->Name</td><td> $pdto->Id</td><td> $pdto->Make</td><td> $pdto->Model</td><td> $pdto->Price </td><td> $pdto->Description</td><td> ";
            foreach ($ReceptorProdCatArray as $ProdCatListing) {
                echo "$ProdCatListing->CategoryId ";
            }
            echo "</td></tr></table>";
        }
        include 'CreateProductInputForm.html';
    } else if (isset($_POST['submit'])) {
        $ProdCatArray = [];
//populate the object from the posted data
        $pdto->Name = filter_input(INPUT_POST, 'productname');
        $pdto->Make = filter_input(INPUT_POST, 'productmake');
        $pdto->Model = filter_input(INPUT_POST, 'productmodel');
        $pdto->Price = filter_input(INPUT_POST, 'productprice');
        $pdto->Description = filter_input(INPUT_POST, 'productdescription');
        foreach ($_POST['categoryid'] as $categoryid) {
            $pcdto = new ProductCategoriesDTO();
            $pcdto->CategoryId = $categoryid;
            $ProdCatArray[] = $pcdto;
        }

//make a list of pages and, for each one, do a sequence diagram of page submission events

        $ps = new ProductService($servername, $dbname, $username, $password);
        $ps->Insert($pdto, $ProdCatArray);
        echo "<table class=\"table table-bordered\">"
        . "<tr><th>Product Name</th><th>Product Id</th><th>Product Make</th><th>Product Model</th><th>Product Price</th><th>Product Description</th><th>Product Categories</th></tr>"
        . "<tr><td> $pdto->Name</td><td> $pdto->Id</td><td> $pdto->Make</td><td> $pdto->Model</td><td> $pdto->Price </td><td> $pdto->Description</td><td> ";

        $CatCount = count($ProdCatArray);
        if (null != $CatCount) {
            $i = 0;
            do {
                echo $ProdCatArray[$i]->CategoryId . " ";
                $i++;
            } while ($i < $CatCount);
        }
        echo "</td></tr></table>";
        include "CreateProductInputForm.html";
    } else {
//Nothing was posted here
        include 'CreateProductInputForm.html';
    }
    include 'Footer.html';
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
}
?>