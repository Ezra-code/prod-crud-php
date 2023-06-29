<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//$_POST super global used to get the data being sent to the database
//$_SERVER is a super global variable used to check request methods IF ITS SUPPOSED TO BE A POST THEN IT WILL RETURN THE VALUES

$errors = [];


$title = '';
$description = '';
$price = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['desc'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s');

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
        $imagePath = '';
    //check if the image exists if it exists then add it to the database
    //to avoid image folder being created we check if image contains tmp_name the name allocated to a chosen file by php
        if($image && $image['tmp_name']){
            $imagePath = 'images/'.randomString(8).'/'.$image['name'];
            mkdir(dirname($imagePath));
         
            move_uploaded_file($image['tmp_name'], $imagePath);
        }
        // $pdo->prepare("INSERT INTO products (title, image, description, price, create_date) VALUES ('$title', '$image', '$description', $price, '$date')") avoid this style
        $statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date) VALUES (:title, :image, :description, :price, :date)");
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':date', $date);
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
// echo '<pre>';
// var_dump($products);
// echo'</pre>';

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Product</title>
    <link rel="stylesheet" href="app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <h1>Create Product</h1>
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