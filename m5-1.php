<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板(MySQL)</title>
</head>
<body>

    <?php
    //書き込み

    $dsn = 'mysql:dbname=データベース名;host=localhost'; //データベース名
    $user = 'ユーザー名'; //ユーザー名
    $password = 'ホスト名';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    $sql = "CREATE TABLE IF NOT EXISTS keijiban"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date CHAR(20),"
    . "pass CHAR(10)"
    .");";
    $stmt = $pdo->query($sql);

    

    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && isset($_POST["submit"])){    //名前とコメントフォームが空でないとき
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $pass = $_POST["pass"];
        $date = date("Y/m/d/ H:i:s");
        
         
        if($_POST["hnumber"] == 0){  //編集番号・パスワード・編集ボタンが空のとき
        
            if($name != "名前" && $comment != "コメント" && $pass != "パスワード"){  //フォームの中身が初期状態ではないとき
                
                $sql = "ALTER TABLE keijiban AUTO_INCREMENT = 1";
                $stmt = $pdo->query($sql);
                
                $sql = 'SELECT * FROM keijiban WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    if($row["pass"] == $_POST["epass"]){
                    $name = $row['name'];
                    $comment = $row['comment'];
                    $hnumber = $row["id"];
                    }
                }
                            
                $sql = "INSERT INTO keijiban (name, comment, date, pass) VALUES(:name, :comment, :date, :pass)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindValue(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->execute();
                $name = "名前";
                $comment = "コメント";
                $hnumber = 0;
            }
            
        }else{
            $id = $_POST["hnumber"];
            $ename = $_POST["name"];
            $ecomment = $_POST["comment"];
            $date = date("Y/m/d/ H:i:s");
            $sql = 'UPDATE keijiban SET name=:name,comment=:comment,date=:date WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $ename, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $ecomment, PDO::PARAM_STR);
            $stmt->bindValue(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }



//削除
    if(!empty($_POST["dnumber"]) && !empty($_POST["dpass"]) && isset($_POST["delate"])){  //削除番号があるとき
        
        $id = $_POST["dnumber"];
        $sql = 'SELECT * FROM keijiban WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($row["pass"] == $_POST["dpass"]){
                $sql = 'delete from keijiban where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
       
    }
    
    if(!empty($_POST["enumber"]) && !empty($_POST["epass"]) && isset($_POST["edit"])){
        $id = $_POST["enumber"];
        $sql = 'SELECT * FROM keijiban WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($row["pass"] == $_POST["epass"]){
                $name = $row['name'];
                $comment = $row['comment'];
                $hnumber = $row["id"];
                
            }else{
                $name = "名前";
                $comment = "コメント";
                $hnumber = 0;
            }
        }        
    }else{
         $name = "名前";
         $comment = "コメント";
         $hnumber = 0;
    }

    ?>


    <form action="" method="post">
        <input type="text" name="name" value="<?php echo $name; ?>">
        <input type="text" name="comment" value="<?php echo $comment; ?>">
        <input type="text" name="pass" value="パスワード">
        <input type="submit" name="submit">
        <p>投稿の際には10文字以内のパスワードが必要です。</p>
        <input type="number" name="dnumber" placeholder="削除番号">
        <input type="submit" name="delate" value="削除">
        <input type="text" name="dpass" placeholder="パスワード"><br>
        <input type="number" name="enumber" placeholder="編集対象番号">
        <input type="submit" name="edit" value="編集">
        <input type="text" name="epass" placeholder="パスワード">
        <input type="hidden" name="hnumber" value="<?php echo $hnumber; ?>">
    </form>




    <?php
    //表示機能
    $sql = 'SELECT * FROM keijiban';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach($results as $row){
        echo $row['id'].",";
        echo $row['name'].",";
        echo $row['comment'].",";
        echo $row['date'];
    echo "<hr>";
    }
    ?>
    
</body>
</html>
    
    
