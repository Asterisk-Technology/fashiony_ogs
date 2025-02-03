<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id = :id");
$statement->execute(['id' => 1]);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_product_category = htmlspecialchars($row['banner_product_category'], ENT_QUOTES, 'UTF-8');
}
?>

<?php
if (!isset($_REQUEST['id']) || !isset($_REQUEST['type'])) {
    header('location: index.php');
    exit;
} 

$type = $_REQUEST['type'];
$id = filter_var($_REQUEST['id'], FILTER_VALIDATE_INT); // Use filter_var to validate 'id' as an integer

if ($id === false) {
    header('location: index.php');
    exit;
}

$allowed_types = ['top-category', 'mid-category', 'end-category'];
if (!in_array($type, $allowed_types, true)) {
    header('location: index.php');
    exit;
} 

$top = $top1 = $mid = $mid1 = $mid2 = $end = $end1 = $end2 = [];

$statement = $pdo->prepare("SELECT tcat_id, tcat_name FROM tbl_top_category");
$statement->execute();
$topCategories = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($topCategories as $row) {
    $top[] = $row['tcat_id'];
    $top1[] = htmlspecialchars($row['tcat_name'], ENT_QUOTES, 'UTF-8');
}

$statement = $pdo->prepare("SELECT mcat_id, mcat_name, tcat_id FROM tbl_mid_category");
$statement->execute();
$midCategories = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($midCategories as $row) {
    $mid[] = $row['mcat_id'];
    $mid1[] = htmlspecialchars($row['mcat_name'], ENT_QUOTES, 'UTF-8');
    $mid2[] = $row['tcat_id'];
}

$statement = $pdo->prepare("SELECT ecat_id, ecat_name, mcat_id FROM tbl_end_category");
$statement->execute();
$endCategories = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($endCategories as $row) {
    $end[] = $row['ecat_id'];
    $end1[] = htmlspecialchars($row['ecat_name'], ENT_QUOTES, 'UTF-8');
    $end2[] = $row['mcat_id'];
}

$final_ecat_ids = [];

if ($type === 'top-category') {
    if (!in_array($id, $top, true)) {
        header('location: index.php');
        exit;
    }
    
    $title = $top1[array_search($id, $top, true)];
    $arr1 = $arr2 = [];
    foreach ($mid as $key => $m) {
        if ($mid2[$key] == $id) {
            $arr1[] = $m;
        }
    }
    foreach ($arr1 as $arr1_id) {
        foreach ($end as $key => $e) {
            if ($end2[$key] == $arr1_id) {
                $arr2[] = $e;
            }
        }
    }
    $final_ecat_ids = $arr2;
} 

if ($type === 'mid-category') {
    if (!in_array($id, $mid, true)) {
        header('location: index.php');
        exit;
    }
    
    $title = $mid1[array_search($id, $mid, true)];
    $arr2 = [];
    foreach ($end as $key => $e) {
        if ($end2[$key] == $id) {
            $arr2[] = $e;
        }
    }
    $final_ecat_ids = $arr2;
} 

if ($type === 'end-category') {
    if (!in_array($id, $end, true)) {
        header('location: index.php');
        exit;
    }
    
    $title = $end1[array_search($id, $end, true)];
    $final_ecat_ids = [$id];
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_product_category; ?>)">
    <div class="inner">
        <h1><?php echo LANG_VALUE_50; ?> <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php require_once('sidebar-category.php'); ?>
            </div>
            <div class="col-md-9">
                <h3><?php echo LANG_VALUE_51; ?> "<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"</h3>
                <div class="product product-cat">
                    <div class="row">
                        <?php
                        $prod_count = 0;
                        
                        $placeholders = rtrim(str_repeat('?,', count($final_ecat_ids)), ',');
                        $statement = $pdo->prepare("SELECT ecat_id FROM tbl_product WHERE ecat_id IN ($placeholders)");
                        $statement->execute($final_ecat_ids);
                        $prod_table_ecat_ids = $statement->fetchAll(PDO::FETCH_COLUMN);

                        if (empty($prod_table_ecat_ids)) {
                            echo '<div class="pl_15">'.LANG_VALUE_153.'</div>';
                        } else {
                            $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE ecat_id IN ($placeholders) AND p_is_active = 1");
                            $statement->execute($final_ecat_ids);
                            $products = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($products as $row) {
                                ?>
                                <div class="col-md-4 item item-product-cat">
                                    <div class="inner">
                                        <div class="thumb">
                                            <div class="photo" style="background-image:url(assets/uploads/<?php echo htmlspecialchars($row['p_featured_photo'], ENT_QUOTES, 'UTF-8'); ?>);"></div>
                                            <div class="overlay"></div>
                                        </div>
                                        <div class="text">
                                            <h3><a href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo htmlspecialchars($row['p_name'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                                            <h4><?php echo LANG_VALUE_1; ?><?php echo $row['p_current_price']; ?></h4>
                                            <?php if ($row['p_qty'] == 0): ?>
                                                <div class="out-of-stock">
                                                    <div class="inner">Out Of Stock</div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
