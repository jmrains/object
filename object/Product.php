<?php

//I can do things when I'm by myself, but when I'm with people I feel like I can't. I've grown more and more accustomed to doing business by myself and spending
//my time alone that it's getting hard to function around people. Don't let them get you so excited. Remember that when stepping out to do water energy, or stepping
//and turning the torso generally, the foot of the leg that doesn't step forward should pivot out on its heel, otherwise the knee takes the strain.
//I think the namespaces are collections of classes of a certain kind; OutNOutEntity contains all of the entity classes, OutNOutService contains all the service classes
//The design that this page uses (3-tier architecture?) depends on page submission events; could the existing classes be used in a no-submission design?
include 'Service.php';
include 'Entity.php';

/**
 * Description of Product
 *
 * @author Josh
 */
//fields
class ProductDTO {

//what calls the ProductDTO object into existence?
    public $Id;
    public $Name;
    public $Make;
    public $Model;
    public $Price;
    public $Description;

//public $Id; this gets plopped in after the Product has been inserted

    public function __construct($obj = null) {
        if (null != $obj) {
            $this->Name = $obj->Name;
            $this->Make = $obj->Make;
            $this->Model = $obj->Model;
            $this->Price = $obj->Price;
            $this->Description = $obj->Description;
            echo "ProductDTO constructor has run.";
        }
    }

}

//business rules
class ProductService extends Service {

    //public $DBH;
//These CRUDs will have parameters that include a $ProductDTO
    public function Insert(\ProductDTO $productdto, $ProdCatsArray) {
        echo "Inside PS->Insert()";
        $this->DBH->beginTransaction();
        $pe = new ProductEntity();
        $pe->Insert($productdto, $this->DBH);

        $pce = new ProductCategoriesEntity();
        try {
            foreach ($ProdCatsArray as $ProdCatDTO) {
                $ProdCatDTO->ProductId = $productdto->Id;
                $pce->Insert($ProdCatDTO, $this->DBH);
            }
            $this->DBH->commit();
        } catch (PDOException $pdoe) {
            echo "An error occurred during insertion: " . $pdoe->getMessage();
        }
    }

    public function FetchProductById($productid) {
        //$this->DBH->beginTransaction(); //is this necessary for retrieval?
        $pe = new ProductEntity();
        $FoundProduct = $pe->FetchProductById($productid, $this->DBH);
        var_dump($FoundProduct);
        //here's where I would check the object's fields before returning it, if I had a range targeted
        return $FoundProduct;
    }

    public function Update(\ProductDTO $productdto) {
        
    }

    public function Delete(\ProductDTO $productdto) {
        
    }

}

//database operations
class ProductEntity extends Entity {//make Service pass the connection to entity

    public function Insert(\ProductDTO $Pdto, PDO $DBH) {
        $AddProduct = "INSERT INTO Products (Name, Make, Model, Price, Description) VALUES (:name, :make, :model, :price, :description)";
//make a prepared statement from the query
        $STH = $DBH->prepare($AddProduct); //$STH is a prepared statement
//bind parameters to the statement handler
//the $NewProduct is the object created 
        $STH->bindParam(':name', $Pdto->Name);
        $STH->bindParam(':make', $Pdto->Make);
        $STH->bindParam(':model', $Pdto->Model);
        $STH->bindParam(':price', $Pdto->Price);
        $STH->bindParam(':description', $Pdto->Description);
//execute the query
        $STH->execute();
        $Pdto->Id = $DBH->lastInsertId();
    }

    public function FetchProductById($productid, PDO $DBH) {
        //we'll need to do something about ProductCategories, too
        $FindProductQuery = "SELECT * FROM Products WHERE Products.Id=$productid";
        $STH = $DBH->prepare($FindProductQuery);
        $STH->setFetchMode(PDO::FETCH_CLASS, "ProductDTO");
        $STH->execute();
        $FoundProduct = $STH->fetch(PDO::FETCH_CLASS);
        $STH->closeCursor();
        var_dump($FoundProduct);
        return $FoundProduct;
    }

    public function Update() {
        /* 1 - run a query to get the Id of the Product entry matching the parameters; try to lock it down with a check of the ProdCats table, too
         * 2 - run a 2nd query to UPDATE Products SET Field1=val1, Field2=val2 WHERE Id= the Id found in the previous query
         */
    }

    public function Delete() {
//need to call retrieve first
        $DeleteProduct = "DELETE * FROM Products ";
    }

}

?>