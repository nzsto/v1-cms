<?php
	require('clicaptcha.class.php');
	
  $clicaptcha = new clicaptcha();
  $imagePathArr = array('image/bg1.jpg', 'image/bg2.jpg', 'image/bg3.jpg', 'image/bg4.jpg', 'image/bg5.jpg');
  if ($_GET['type'] == 'small') {
    $imagePath = $imagePathArr[rand(0, count($imagePathArr) - 1)];
    $_SESSION['imagePath'] = $imagePath;
    $_SESSION['img_text'] = $clicaptcha->randChars();
  }
  $clicaptcha->imagePath = $_SESSION['imagePath'];
  $clicaptcha->text = $_SESSION['img_text'];
	if($_POST['do'] == 'check'){
		echo $clicaptcha->check($_POST['info'], false) ? 1 : 0;
	}else{
    if ($_GET['type'] == 'small') {
      $clicaptcha->creat();
    }else{
      $clicaptcha->GetSmallImg();
    }
	}
?>