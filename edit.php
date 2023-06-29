<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$id = $_GET['id'];

if(!$id){
header('Location: index.php');
exit;
}

$statement = $pdo->prepare('SELECT * FROM products WHERE id= :id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);
// header('Location: index.php');


$errors = [];

$title = $product['title'];
$description = $product['description'];
$price = $product['price'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['desc'];
    $price = $_POST['price'];
   

    if (!$title) {
        $errors[] = 'Please enter product title';
    }
    if (!$price) {
        $errors[] = "Enter Product price";
    }

    if(!is_dir('images')){
        mkdir('images');
    }

    if (empty($errors)) {

        $image = $_FILES['image'] ?? null;
        $imagePath = $product['image'];

        if($image && $image['tmp_name']){

            if($product['image']){
                unlink($product['image']);
            }
            $imagePath = 'images/'.randomString(8).'/'.$image['name'];
            mkdir(dirname($imagePath));
         
            move_uploaded_file($image['tmp_name'], $imagePath);
        }
        // $pdo->prepare("INSERT INTO products (title, image, description, price, create_date) VALUES ('$title', '$image', '$description', $price, '$date')") avoid this style
        $statement = $pdo->prepare("UPDATE products SET title = :title, image=:image, description=:description, price=:price WHERE id = :id");
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':id', $id);
        $statement->execute();
        header('Location: index.php');
    }
}

function randomString($n){

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for($i=0; $i < $n; $i++){
        $index = rand(0, strlen($characters) - 1);
        $str .= $characters[$index];
    }

    return $str;

}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Product</title>
    <link rel="stylesheet" href="app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <h1>Update Product</h1>
    <a href="index.php" class="btn btn-warning" style="font-weight: 700;"> <i class="bi bi-arrow-bar-left"></i> GO Back</a>
    <?php if ($errors) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div>
                    <?php echo $error ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <form action="" method="post" enctype="multipart/form-data">

    <?php if($product['image']): ?>
        <img src="<?php echo $product['image'] ?>"/>
    <?php endif ?>

        <div class="mb-3">
            <label for="formFile" class="form-label">Product Image</label>
            <input class="form-control" type="file" id="formFile" name="image">
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Product Title</label>
            <input type="text" class="form-control" id="exampleFormControlInput1"  value="<?php echo $title ?>" name="title">
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Product Price</label>
            <input type="text" class="form-control" id="exampleFormControlInput1" inputmode="numeric" pattern="[0-9]*" value="<?php echo $price ?>" name="price">
        </div>
        <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">Product description</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="desc"><?php echo $description ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Item <i class="bi bi-plus-circle"></i></button>
    </form>
</body>

</html>