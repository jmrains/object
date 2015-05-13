<?php

class ProductCategoriesDTO {

    public $ProdCatAssocId;
    public $ProductId; //this is the auto-incremented Id assigned during the Product's INSERTion; it is also the $NewRowId captured inside ProductService::Insert()
    public $CategoryId;

    //public $ProdCatsArray; //ProdCatsDTOs get instantiated (for now) in the ProductService::Insert method, so this needs to be able to be populated after Product insertion
    //a call to the ProductCategories Entity doesn't have to originate from a ProductCategoryDTO; it can be called from within the ProductService
    //additionally, while one product can be associated with many pategories, each association of a Product with a Category is a separate entry in the ProdCats table
    //a ProdCatsDTO object is modelled on one entry in the ProdCats table, but differs in that the object also has a ProdCatsArray member that stores all of the products
    //at once; this 

    /*  $featured = array('key1' => 'value1', 'key2' => 'value2', etc.);
     *      foreach($featured as $value){
     *      echo $value['name'];
     * }
     *
     * In the PHP code: $featured is the associative array being looped through, and as $key => $value means that each time the loop runs 
     * and selects a key-value pair from the array, it stores the key in the local $key variable to use inside the loop block and the value 
     * in the local $value variable. So for our example array above, the foreach loop would reach the first key-value pair, and if you specified as $key => $value, 
     * it would store 'key1' in the $key variable and 'value1' in the $value variable.
     */

    /*
      public function __construct($NewProductRowId = null, $ProdCatsArray = null) {
      if (null != $NewProductRowId) {
      $this->ProductId = $NewProductRowId;
      //if we do have a $NewProductRowId, then we don't have any ProdCatAssocIds (the unique Id for each entry in the ProdCats table);
      //a ProdCatAssocId is assigned when the entry is inserted, so it is only available for existing ProdCats records.
      //the existence of a NewProductRowId means that we also have to make entries in the ProdCats table FROM $ProdCatsArray
      foreach ($ProdCatsArray as $value) {
      $this->ProdCatsArray = $value;
      }
      }//if this condition was not met, then we assign to $this->ProdCatsArray for getting it back to the presentation layer
      else {
      /* ask the db how many categories are associated with the product in question; this will require a PCS object
     * make a new array of the same size
     * populate it with all the ProdCatAssocIds corresponding to a certain ProductId
     * loop through the array and copy each value into $this->ProdCatsArray
     */
    /*
      $this->ProductId = filter_input(INPUT_GET, 'productid');
      $pcs = new ProductCategoriesService();
      $pcs->FetchProductData(&$this);
      $pcs->GetProdCatsRowCount(&$this); //the reference lets the PCS/PCE know where to put the data

      $dbProdCatsArray = array();
      $this->ProdCatsArray = array();
      }
      } */
}

class ProductCategoriesService extends Service {

    public function Insert(ProductCategoriesDTO $pcdto) {
        $pce = new ProductCategoriesEntity();
        $pce->Insert($pcdto, $this->DBH);
    }

    public function FetchProductsByCategory(ProductCategoriesDTO $pcdto) {
        $pce = new ProductCategoriesEntity();
        $Products = $pce->FetchProductsByCategory($pcdto);
    }

    public function FetchCategories() {
        $pce = new ProductCategoriesEntity();
    }

    public function GetProdCatsRowCount(ProductCategoriesDTO &$pcdto) {
        $pce = new ProductCategoriesEntity();
        // $ProdCatsRowCount = $pce->GetProdCatsRowCount(&$pcdto);
    }

    public function FetchProductCategories($productid, $ProductCategoriesDTOArray) {
        $pce = new ProductCategoriesEntity();
        $ResultSet = $pce->FetchProductCategories($productid, $this->DBH);
        foreach ($ResultSet as $ProductCategoriesObject) {
            $temp_pcdto = new ProductCategoriesDTO();
            $temp_pcdto->CategoryId = $ProductCategoriesObject->CategoryId;
            $ProductCategoriesDTOArray[] = $temp_pcdto;
        }
    }

}

class ProductCategoriesEntity extends Entity {

    public function Insert(ProductCategoriesDTO $pcdto, PDO $DBH) {
        //echo "Inside PCE->Insert().";
        $AddProdCat = "INSERT INTO ProductCategories (ProductId, CategoryId) VALUES (:productid, :categoryid)";
        $STH = $DBH->prepare($AddProdCat);
        $STH->bindParam(':productid', $pcdto->ProductId);
        $STH->bindParam(':categoryid', $pcdto->CategoryId);
        //execute the query
        $STH->execute();
    }

    public function FetchProductsByCategory(ProductCategoriesDTO $pcdto, PDO $DBH) {
        $ProductsInCategoryQuery = "SELECT ProductId FROM ProductCategories WHERE CategoryId=" . $pcdto->CategoryId;
        $STH = $DBH->prepare($ProductsInCategoryQuery);
        $ProductsInCategory = $STH->execute();
    }

    public function FetchCategories(PDO $DBH) {
        $FetchCategoriesQuery = "SELECT * FROM Categories";
        $STH = $DBH->prepare($FetchCategoriesQuery);
        $AllCategories = $STH->execute();
    }

    //this function will get the categories associated with a product
    public function GetProdCatsRowCount(ProductCategoriesDTO &$pcdto, PDO $DBH) {
        //make up a query with named placeholders
        $ProdCatAssocQuery = "SELECT CategoryId FROM ProductCategories WHERE ProductCategories.ProductId=$pcdto->ProductId";
        //make a prepared statement from the query
        $STH = $DBH->prepare($ProdCatAssocQuery);
        //bind parameters to the statement handler
        //execute the query, capturing the result, which will come back as an array?
        //return result
    }

    public function FetchProductCategories($productid, PDO $DBH) {
        $ProductQuery = "SELECT ProductCategories.CategoryId FROM ProductCategories WHERE ProductCategories.ProductId=$productid";
        $STH = $DBH->prepare($ProductQuery);
        $ResultSet = $STH->fetchAll(PDO::FETCH_COLUMN, 0);
        var_dump($ResultSet);
        return $ResultSet;
    }

}

?>