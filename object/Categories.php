<?php

/**
 * Description of Categories
 *
 * @author Josh
 */
class CategoriesDTO {

    public $CategoryId;
    public $CategoryName;

}

class CategoriesService extends Service {

    public function Insert(CategoriesDTO $cdto) {
        
    }

    public function FetchCategories() {
        echo "Inside CS->FetchCategories(). ";
        $CategoriesDTOArray = array(); //fetch Categories into this array, then put them into DTOs
        $ce = new CategoriesEntity();
        $ResultSet = $ce->FetchCategories($this->DBH);
        foreach ($ResultSet as $CategoryObject) {
            $pcdto = new CategoriesDTO();
            $pcdto->CategoryId = $CategoryObject->CategoryId;
            $pcdto->CategoryName = $CategoryObject->CategoryName;
            $CategoriesDTOArray[] = $pcdto;
        }
        return $CategoriesDTOArray;
    }

}

class CategoriesEntity {

    public function FetchCategories(PDO $DBH) {
        echo "Inside CE->FetchCategories().";
        $FetchCategoriesQuery = "SELECT Categories.CategoryId, Categories.CategoryName FROM Categories";
        $STH = $DBH->prepare($FetchCategoriesQuery); //$STH is PDOStatement class object
        $STH->execute(); //upon execution, $STH represents the result set of the executed query
        $ResultSet = $STH->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "CategoriesDTO"); //this fetches the result set of the last query as an array of CategoriesDTO class objects        
        
        return $ResultSet;
    }

}
