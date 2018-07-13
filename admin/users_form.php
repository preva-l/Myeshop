<?php

    require_once('inc/header.php');

    if($_POST)
    {
        foreach($_POST as $key => $value) 
        {
            $_POST[$key] = addslashes($value);
        }

        debug($_POST);
        debug($_FILES);

        if(!empty($_FILES['picture']['name'])) // I'm checking if I got a result for the 1st picture
        {
            // I give a random name for my picture.
            $picture_name = $_POST['pseudo'] . '-' . rand(1,999) .  '_' . $_FILES['picture']['name'];

            $picture_name = str_replace(' ', '-', $picture_name);

            // we register the path of my file
            $picture_path = ROOT_TREE . 'uploads/img/' . $picture_name;

            $max_size = 2000000;

            if($_FILES['picture']['size'] > $max_size || empty($_FILES['picture']['size']))
            {
                $msg_error .= "<div class='alert alert-danger'>Please select a 2Mo file maximum !</div>";
            }

            $type_picture = ['image/jpeg', 'image/png', 'image/gif'];
            
            if(!in_array($_FILES['picture']['type'], $type_picture) || empty($_FILES['picture']['type']))
            {
                $msg_error .= "<div class='alert alert-danger'>Please select a JPEG/JPG, a PNG or a GIF file.</div>";
            }

        }
        elseif (isset($_POST['actual_picture'])) // if I update a product, I target the new input created with my $update_product
        {
            $picture_name = $_POST['actual_picture'];
        }
        else
        {
            $picture_name = 'default.jpg';
        }

        // OTHER CHECK POSSIBLE HERE

        if(empty($msg_error))
        {

            if(!empty($_POST['user'])) // we register the update
            {
                $result = $pdo->prepare("UPDATE user SET id_user=:id_user, pseudo=:pseudo, pwd=:pwd, firstname=:firstname, lastname=:lastname, email=:email, gender=:gender, city=:city, zip_code=:zip:_code, address=:address, privilege=:privilege, picture=:picture WHERE user = :user");

                $result->bindValue(':id_user', $_POST['id_user'], PDO::PARAM_INT);
            }


            $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
            $result->bindValue(':pwd', $_POST['pwd'], PDO::PARAM_STR);
            $result->bindValue(':firstname', $_POST['firstname'], PDO::PARAM_STR);
            $result->bindValue(':lastname', $_POST['lastname'], PDO::PARAM_STR);
            $result->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $result->bindValue(':gender', $_POST['gender'], PDO::PARAM_STR);
            $result->bindValue(':city', $_POST['city'], PDO::PARAM_STR);
            $result->bindValue(':zip_code', $_POST['zip_code'], PDO::PARAM_STR);
            $result->bindValue(':address', $_POST['address'], PDO::PARAM_STR);
            $result->bindValue(':privilege', $_POST['privilege'], PDO::PARAM_INT);

            $result->bindValue(':picture', $picture_name, PDO::PARAM_STR);

            if($result->execute()) // if the request was inserted ine the DTB
            {
                if(!empty($_FILES['user_picture']['name']))
                {
                    copy($_FILES['user_picture']['tmp_name'], $picture_path); 
                }

                if(!empty($_POST['id_user']))
                {
                    header('location:users_list.php?m=update');
                }
            }

        }

    }

    if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']))
    {
        $req = "SELECT * FROM user WHERE id_user = :id_user";

        $result = $pdo->prepare($req);
        $result->bindValue(':id_user', $_GET['id'], PDO::PARAM_INT);
        $result->execute();

        if($result->rowCount() == 1)
        {
            $update_user = $result->fetch();
        }
    }

    $pseudo = (isset($update_user)) ? $update_user['pseudo'] : '';
    $pwd = (isset($update_user)) ? $update_user['pwd'] : '';
    $firstname = (isset($update_user)) ? $update_user['firstname'] : '';
    $lastname = (isset($update_user)) ? $update_user['lastname'] : '';
    $email = (isset($update_user)) ? $update_user['email'] : '';
    $gender = (isset($update_user)) ? $update_user['gender'] : '';
    $city = (isset($update_user)) ? $update_user['city'] : '';
    $zip_code = (isset($update_user)) ? $update_user['zip_code'] : '';
    $address = (isset($update_user)) ? $update_user['address'] : '';
    $privilege = (isset($update_user)) ? $update_user['privilege'] : '';

    $user = (isset($update_user)) ? $update_user['id_user'] : '';

    $action = (isset($update_user)) ? "Update" : 'Add';

?>

    <h1 class="h2"><?= $action ?> a user</h1>

    <form action="" method="post">
            <small class="form-text text-muted">Here you can update users information</small>
            <?= $msg_error ?>
            <input type='hidden' name="id_user" value="<?= $user ?>">
            <div class="form-group">
                <input type="text" class="form-control" name="pseudo" placeholder="Choose a pseudo..." value="<?= $pseudo ?>" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Choose a password..." required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="firstname" placeholder="Your firstname..." value="<?= $firstname ?>">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="lastname" placeholder="Your lastname..." value="<?= $lastname ?>">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Your email..." value="<?= $email ?>">
            </div>
            <div class="form-group">
                <select name="gender" class="form-control">
                    <option value="m" <?php if($gender == 'm'){echo 'selected';} ?>>Men</option>
                    <option value="f" <?php if($gender == 'f'){echo 'selected';} ?>>Women</option>
                    <option value="o" <?php if($gender == 'o'){echo 'selected';} ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="address" placeholder="Address..." value="<?= $address ?>">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="zc" placeholder="Zip code..." value="<?= $zip_code ?>">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="city" placeholder="City..." value="<?= $city ?>">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="privilege" placeholder="Privilege..." value="<?= $privilege ?>">
            </div>
            <input type="submit" value="Send" class="btn btn-success btn-lg btn-block">
        </form>


<?php
    require_once('inc/footer.php');
?>