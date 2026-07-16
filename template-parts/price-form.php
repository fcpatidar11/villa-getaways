<?php 
$price = ( isset($_REQUEST['price']) && $_REQUEST['price'] ) ? $_REQUEST['price'] : "";
?>

<div id="filter-by-price">
    <form action="" method="get" id="filter-by-price">
        <select class="form-control" name="price" id="filterPriceRecommended">
            <option value="" >Choose Option</option>
            <option <?php echo $price == "recommended" ? "selected": ""; ?> value="recommended">Recommended</option>
            <option <?php echo $price == "low" ? "selected": ""; ?> value="low">Low to High</option>
            <option <?php echo $price == "high" ? "selected": ""; ?> value="high">High to Low</option>
        </select>
    </form>
</div>