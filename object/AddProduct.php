<?php
if (isset($_POST['submit'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    if (isset($_POST['categoryid'])) {
        echo "Hola! The checkboxes got posted.<br/>";
        $productname = filter_input(INPUT_POST, 'productname');
        echo $productname . "<br/>";
        $productmake = filter_input(INPUT_POST, 'productmake');
        echo $productmake . "<br/>";
        $productmodel = filter_input(INPUT_POST, 'productmodel');
        echo $productmodel . "<br/>";
        $productprice = filter_input(INPUT_POST, 'productprice');
        echo $productprice . "<br/>";
        $productdescription = filter_input(INPUT_POST, 'productdescription');
        echo $productdescription . "<br/>";
        try {
            $DbConn = new PDO("mysql:host=$servername;dbname=testdb", $username, $password);
            $DbConn->setAttribute(PDO::ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);

            $sth = $DbConn->prepare("INSERT INTO Products(Name, Make, Model, Price, Description) "
                    . "VALUES(:productname, :productmake, :productmodel, :productprice, :productdescription)");                    
            $sth->bindValue(':productname', filter_input(INPUT_POST, 'productname'), PDO::PARAM_STR);
            $sth->bindValue(':productmake', filter_input(INPUT_POST, 'productmake'), PDO::PARAM_STR);
            $sth->bindValue(':productmodel', filter_input(INPUT_POST, 'productmodel'), PDO::PARAM_STR);
            $sth->bindValue(':productprice', filter_input(INPUT_POST, 'productprice'), PDO::PARAM_STR);
            $sth->bindValue(':productdescription', filter_input(INPUT_POST, 'productdescription'), PDO::PARAM_STR);
            $result = $sth->execute();
            $sth = $DbConn->prepare("SELECT LAST_INSERT_ID()");
            $productid = $DbConn->lastInsertId();//calling the $DbConn PDO object's lastInsertId() method gives the desired output
            //now we can insert the categories assoc'd with this product as Product-Category associations in the ProductCategories table
            echo $productid;
            $result = $sth->execute();
        } catch (PDOException $e) {
            echo 'Connection failed:' . $e->getMessage();
        }
// Counting number of checked checkboxes.
        $checked_count = count($_POST['categoryid']);
        echo "You have selected following " . $checked_count . " option(s): <br/>";
// Loop to store and display values of individual checked checkbox.
        foreach ($_POST['categoryid'] as $selected) {
            echo "<p>" . $selected . "</p>";
            
            $sthelse = $DbConn->prepare("INSERT INTO ProductCategories (ProductId, CategoryId) "
                    . "VALUES(:productid, :categoryid)");
            //How can this be rewritten so that invalid sources aren't bound and statement preparation halts?
            $sthelse->bindValue(':productid', $productid);            
            $sthelse->bindValue(':categoryid', $selected);
            $result2 = $sthelse->execute();
            //the first pass through this loop succeeds, because for Products 13,14,15 there are entries with corresponding CategoryId of 2.
            //the second attempt to add an entry fails each time, giving an exception for a duplicate entry for the ProductId
            //15-4-2015: the problem noted above was solved by altering the ProdCats table so that ProductId wasn't the table's primary key;
            //          instead, each entry into the ProdCats table is given its own unique int primary key, unrelated to the ProductId
        }
        //Don't get into the try-block without making sure that all of the inputs are filled out and valid
    }
}
include 'AddProduct.html';
?>