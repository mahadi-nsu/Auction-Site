<?php namespace Auctions\process_forms;

require_once 'functions.php';
use Auctions\fetch_functions;
use Auctions\process_forms;



// TRIGGERS
if(isset($_POST['register-user']))
	register_user();

if(isset($_POST['login-user']))
	login_user();

if(isset($_POST['edit-member-password']))
	edit_member_password();

if(isset($_POST['update-profile']))
	update_profile();

if(isset($_POST['sell-post']))
	sell_post();

if(isset($_POST['edit-post']))
	edit_post();

if(isset($_GET['memberId']) && isset($_GET['productCode']))
	add_to_favorite();

if(isset($_GET['memberId']) && isset($_GET['productId']))
	remove_from_favorite();

if(isset($_GET['bid']) && isset($_GET['pCode']) && isset($_GET['mId']))
	set_bid();

if(isset($_POST['add-category']))
	add_category();

if(isset($_POST['approve']))
	approve_post();

if(isset($_POST['reject']))
	reject_post();









function register_user(){

	global $con;
	$mail = htmlspecialchars($_POST['mail']);
	$password = htmlspecialchars($_POST['password']);
	$confirm_password = htmlspecialchars($_POST['confirm-password']);

	$check_user = fetch_functions\get_row('accounts','mail',$mail)[0];
	if($check_user){
		header("Location:../visitors/views/authorize.php?error_mode=useralreadyexist");
		die();
	}


	if(empty($mail)){
		header("Location:../visitors/views/authorize.php?error_mode=emptyemailexception");
	}
	else{
		if(!filter_var($mail,FILTER_VALIDATE_EMAIL)){
			header("Location:../visitors/views/authorize.php?error_mode=invalidemailexception");
		}
		else{
			
			if(empty($password) || empty($confirm_password)){
				header("Location:../visitors/views/authorize.php?error_mode=emptypasswordexception");
			}

			if(strlen($password)<6){
				header("Location:../visitors/views/authorize.php?error_mode=toosmallexception");
				die();					
			}

			$containsLetter = preg_match('/[a-zA-Z]/',$password);
			$containsDigit = preg_match('/\d/', $password);
			$containsSpecial = preg_match('/[^a-zA-Z\d]/', $password);
			if($containsLetter && $containsDigit && $containsSpecial){
				if($password === $confirm_password){
					$stmt = $con->prepare("INSERT INTO accounts (mail,password) VALUES (:mail,:password)");
					$execute = $stmt->execute(array('mail'=>$mail,'password'=>hash('sha256', $password)));
					if($execute){
						$rows = fetch_functions\get_rows('accounts');
						$size =  sizeof($rows); 
						$set_id = $rows[$size-1]->id;

						$stmt = $con->prepare("INSERT INTO members (id,mail) VALUES (:id,:mail)");
						$execute = $stmt->execute(array('id'=>$set_id,'mail'=>$mail));

						session_start();
						$_SESSION['active_user'] = $set_id;
						header("Location:../members/views/update-profile.php");
					}
					else{
						echo "<script>Something Went Wrong. Profile Could Not Be Created</script>";
					}
				}
				else{
					header("Location:../visitors/views/authorize.php?error_mode=passwordmismatchexception");
				}
			}
			else{
				header("Location:../visitors/views/authorize.php?error_mode=invalidpasswordformatexception");
			}
		}
	}
}









function login_user(){

	global $con;
	$mail = htmlspecialchars($_POST['mail']);
	$password = htmlspecialchars($_POST['password']);
	$user = fetch_functions\get_row('accounts','mail',$mail)[0];
	if($user){
		$user = $user->mail;
		$stmt = $con->prepare("SELECT * FROM accounts WHERE mail = :mail");
		$stmt->execute(array('mail' => $user));
		$authenticated = $stmt->fetch(\PDO::FETCH_OBJ);
		if($authenticated->password == hash('sha256', $password)){
			session_start();
			$_SESSION['active_user'] = $authenticated->id;
			header("Location:../members/views/profile.php");
		}
		else{
			header("Location:../visitors/views/authorize.php?error_mode=wrongpasswordexception");
		}
	}
	else{
		header("Location:../visitors/views/authorize.php?error_mode=userdontexistexception");
	}
}









function edit_member_password(){

	global $con;
	$mail = htmlspecialchars($_POST['mail']);
	$current_password = htmlspecialchars($_POST['current-password']);
	$username = fetch_functions\get_row('accounts','mail',$mail)[0];
	$new_pass = htmlspecialchars($_POST['new-password']);
	$confirm_pass = htmlspecialchars($_POST['confirm-password']);

	if(strlen($new_pass)<6){
		header("Location:../members/views/settings.php?error_mode=toosmallexception");	
		die();
	}

	if($username){
		$user = $username->mail;
		$stmt = $con->prepare("SELECT * FROM accounts WHERE mail = :mail");
		$stmt->execute(array('mail' => $user));
		$fetch_pass = $stmt->fetch(\PDO::FETCH_OBJ);
		$retrieve_pass = $fetch_pass->password;
		$current_password = hash('sha256', $current_password);
		if($retrieve_pass == $current_password){

			if($new_pass == $confirm_pass){
				$containsLetter = preg_match('/[a-zA-Z]/',$new_pass);
				$containsDigit = preg_match('/\d/', $new_pass);
				$containsSpecial = preg_match('/[^a-zA-Z\d]/', $new_pass);
				if($containsLetter && $containsDigit && $containsSpecial){
					$set_pass = hash('sha256', $new_pass);
					$stmt = $con->prepare("UPDATE accounts SET password = :password WHERE id = :id");
					$executed = $stmt->execute(array('password' => $set_pass, 'id' => $username->id));
					if($executed){
						header("Location:../members/views/settings.php?error_mode=noerror");		
					}
					else{
						header("Location:../members/views/settings.php?error_mode=unknown");	
					}	
				}
				else{
					header("Location:../members/views/settings.php?error_mode=invalidpasswordformatexception");
				}
				
			}
			else{
				header("Location:../members/views/settings.php?error_mode=passwordmismatchexception");
			}
		}
		else{
			header("Location:../members/views/settings.php?error_mode=wrongpasswordexception");
		}
	}
	else{
		header("Location:../members/views/settings.php?error_mode=userdontexistexception");
	}
}









function update_profile(){

	global $con;
	
	// HIDDEN VALUES	
	$id = htmlspecialchars($_POST['id']);
	$image = htmlspecialchars($_POST['image']);

	// OTHER VALUES
	$name = htmlspecialchars($_POST['name']);
	$birthday = htmlspecialchars($_POST['birthday']);
	$gender = htmlspecialchars($_POST['gender']);
	$about = htmlspecialchars($_POST['about']);
	$phone = htmlspecialchars($_POST['phone']);
	$mail = htmlspecialchars($_POST['mail']);
	$interests = htmlspecialchars($_POST['interests']);
	$living_area = htmlspecialchars($_POST['living-area']);
	$area_preference = htmlspecialchars($_POST['area-preference']);
	$image = process_forms\file_check($image,'file','images/dp','IMAGE');

	$stmt = $con->prepare("UPDATE members SET name = :name, image = :image, birthday = :birthday, gender = :gender, about = :about, phone = :phone, mail = :mail, interests = :interests, living_area = :living_area, area_preference = :area_preference WHERE id = :id");

	$execute = $stmt->execute(array('name'=>$name, 'image'=>$image, 'birthday'=>$birthday, 'gender'=>$gender, 'about'=>$about, 'phone'=>$phone, 'mail'=>$mail, 'interests'=>$interests, 'living_area'=>$living_area, 'area_preference'=>$area_preference, 'id'=>$id));

	if($execute){
		header("Location:../members/views/profile.php");
	}

}









function sell_post(){

	global $con;
	
	// HIDDEN VALUES	
	$seller_id = htmlspecialchars($_POST['seller-id']);
	$img1 = "";$img2 = "";$img3 = "";$img4 = "";

	// OTHER VALUES
	$title = htmlspecialchars($_POST['title']);
	$description = htmlspecialchars($_POST['description']);
	$start_time = htmlspecialchars($_POST['start-date'])." ".htmlspecialchars($_POST['starts-at']);
	$end_time = htmlspecialchars($_POST['end-date'])." ".htmlspecialchars($_POST['ends-at']);
	$starting_price = htmlspecialchars($_POST['start-price']);
	$interval = htmlspecialchars($_POST['interval']);

	$start_time = strtotime($start_time);
	$end_time = strtotime($end_time);
	$post_date = date("d-M-Y , h:i A",time() + 5*60*60);

	$img1 = process_forms\file_check($img1,'img1','images/products','IMAGE');
	$img2 = process_forms\file_check($img2,'img2','images/products','IMAGE');
	$img3 = process_forms\file_check($img3,'img3','images/products','IMAGE');
	$img4 = process_forms\file_check($img4,'img4','images/products','IMAGE');
	$image = $img1 . "{}{{}}}" . $img2 . "{}{{}}}" . $img3 . "{}{{}}}" . $img4;

	$stmt = $con->prepare("INSERT INTO sell_posts (title,description,post_date,start_time,end_time,starting_price,bid_interval,image,seller_id,approval,posted_on,updated_on) VALUES (:title,:description,:post_date,:start_time,:end_time,:starting_price,:bid_interval,:image,:seller_id,:approval,:posted_on,:updated_on)");

	$executed = $stmt->execute(array(
		'title' => $title,
		'description' => $description,
		'post_date' => $post_date,
		'start_time' => $start_time,
		'end_time' => $end_time,
		'starting_price' => $starting_price,
		'bid_interval' => $interval,
		'image' => $image,
		'seller_id' => $seller_id,
		'approval' => 0,
		'posted_on' => $post_date,
		'updated_on' => $post_date
	));
	
	if($executed){
		header("Location:../members/views/posts.php");
	}	

}









function edit_post(){

	global $con;
	
	// HIDDEN VALUES	
	$seller_id = htmlspecialchars($_POST['seller-id']);
	$id = htmlspecialchars($_POST['id']);
	$img1 = htmlspecialchars($_POST['img1']);
	$img2 = htmlspecialchars($_POST['img2']);
	$img3 = htmlspecialchars($_POST['img3']);
	$img4 = htmlspecialchars($_POST['img4']);

	// OTHER VALUES
	$title = htmlspecialchars($_POST['title']);
	$description = htmlspecialchars($_POST['description']);
	$start_time = htmlspecialchars($_POST['start-date'])." ".htmlspecialchars($_POST['starts-at']);
	$end_time = htmlspecialchars($_POST['end-date'])." ".htmlspecialchars($_POST['ends-at']);
	$starting_price = htmlspecialchars($_POST['start-price']);
	$interval = htmlspecialchars($_POST['interval']);

	$start_time = strtotime($start_time);
	$end_time = strtotime($end_time);
	$post_date = date("d-M-Y , h:i A",time() + 5*60*60);

	$img1 = process_forms\file_check($img1,'img1','images/products','IMAGE');
	$img2 = process_forms\file_check($img2,'img2','images/products','IMAGE');
	$img3 = process_forms\file_check($img3,'img3','images/products','IMAGE');
	$img4 = process_forms\file_check($img4,'img4','images/products','IMAGE');
	$image = $img1 . "{}{{}}}" . $img2 . "{}{{}}}" . $img3 . "{}{{}}}" . $img4;

	$updated_on = date("d M, Y",time()+5*60*60) . " at " . date("h:i A",time()+5*60*60);
	
	$stmt = $con->prepare("UPDATE sell_posts SET title = :title, description = :description, post_date = :post_date, start_time = :start_time, end_time = :end_time, starting_price = :starting_price, bid_interval = :bid_interval, image = :image, updated_on = :updated_on WHERE product_code = :id");

	$executed = $stmt->execute(array(
		'title' => $title,
		'description' => $description,
		'post_date' => $post_date,
		'start_time' => $start_time,
		'end_time' => $end_time,
		'starting_price' => $starting_price,
		'bid_interval' => $interval,
		'image' => $image,
		'updated_on' => $updated_on,
		'id' => $id
	));
	
	if($executed){
		header("Location:../members/views/posts.php");
	}	

}









function add_to_favorite(){

	global $con;
	$member_id = htmlspecialchars($_GET['memberId']);
	$product_code = htmlspecialchars($_GET['productCode']);

	$stmt = $con->prepare("SELECT favorites FROM members WHERE id = :id");
	$executed = $stmt->execute(array('id' => $member_id));
	$row = $stmt->fetch(\PDO::FETCH_OBJ);
	$favorites = $row->favorites . $product_code . ',';
	$stmt = $con->prepare("UPDATE members SET favorites = :favorites WHERE id = :id");
	$executed = $stmt->execute(array('favorites' => $favorites, 'id' => $member_id));
	if($executed){
		echo "The Product Has Been Added To Your Favorite List";
	}
}









function remove_from_favorite(){

	global $con;
	$member_id = htmlspecialchars($_GET['memberId']);
	$product_code = htmlspecialchars($_GET['productId']);

	$stmt = $con->prepare("SELECT favorites FROM members WHERE id = :id");
	$executed = $stmt->execute(array('id' => $member_id));
	$row = $stmt->fetch(\PDO::FETCH_OBJ);
	$favorites = explode(',',$row->favorites);
	$size = sizeof($favorites);
	if($size>0){
		$str = "";
		for ($i=0; $i<$size-1; $i++) {
			if($favorites[$i] != $product_code){
				$str = $str . $favorites[$i] . ',';
			}
		}

		$stmt = $con->prepare("UPDATE members SET favorites = :favorites WHERE id = :id");
		$executed = $stmt->execute(array('favorites' => $str, 'id' => $member_id));
		if($executed){
			echo "The Product Has Been Removed From Your Favorite List";
		}
	}
}









function set_bid(){
	
	global $con;
	$p_code = htmlspecialchars($_GET['pCode']);
	$bid = htmlspecialchars($_GET['bid']);
	$member_id = htmlspecialchars($_GET['mId']);
	$placed_on = time() + 5*60*60;

	$stmt = $con->prepare("INSERT INTO bids (product_code,member_id,bid,placed_on) VALUES (:product_code,:member_id,:bid,:placed_on)");
	$executed = $stmt->execute(array(
		'product_code' => $p_code,
		'member_id' => $member_id,
		'bid' => $bid,
		'placed_on' => $placed_on
	));

	if($executed){
		echo "$executed";
	}
}









// FILE CHECKING
function file_check($file,$name,$dir,$fileof){

	$uploadOk = 1;
	$temp = $file;

	$temp_dir = "../$dir/";
	$target_file = $temp_dir . uniqid() . basename($_FILES[$name]['name']);
	if ($_FILES[$name]["size"] > 25000000) {
	    echo "Sorry, $fileof file is too large.";
	    $uploadOk = 0;
	}
 
	if ($uploadOk == 0) {
	    echo  "Sorry, your file was not uploaded.";
	}
	else {
	    if (move_uploaded_file($_FILES[$name]["tmp_name"], $target_file)) {
	        $temp = $target_file;
	        unlink($file);
		}
	}
	

	return $temp;
}









function multiple_image($name,$dir){
	
	$extensions = array('jpeg','jpg','png','gif');
	$temp_dir = "../$dir/";
	$moved = array();
	
	foreach ($_FILES[$name]['tmp_name'] as $image) {
		$file = $temp_dir . uniqid() . basename($_FILES[$name]['name']);
		$ext = pathinfo($file,PATHINFO_EXTENSION);
		if(in_array($ext, $extensions)){
			move_uploaded_file($_FILES['files']['tmp_name'], $file);
			array_push($moved, $file);
		}
		else{
			echo "One of the files is not an image";
			return false;
		}
	}

	return $moved;
}









function add_category(){

	global $con;
	$id = htmlspecialchars($_POST['id']);
	$category = htmlspecialchars($_POST['category']);
	$stmt = $con->prepare("INSERT INTO categories (category) VALUES (:category)");
	$executed = $stmt->execute(array(
		'category' => $category
	));
	if($executed){
		header("Location:../admin/views/product-detail.php?id=$id");
	}

}









function approve_post(){

	global $con;
	$p_code = htmlspecialchars($_POST['product-code']);
	$category = htmlspecialchars($_POST['set-category']);
	$stmt = $con->prepare("UPDATE sell_posts SET approval=1, category = :category WHERE product_code = :product_code");
	$executed = $stmt->execute(array(
		'category' => $category,
		'product_code' => $p_code
	));
	if($executed){
		header("Location:../admin/views/admin-approval.php");
	}
}









function reject_post(){

	global $con;
	$p_code = htmlspecialchars($_POST['product-code']);
	$stmt = $con->prepare("UPDATE sell_posts SET approval=-1 WHERE product_code = :product_code");
	$executed = $stmt->execute(array(
		'product_code' => $p_code
	));
	if($executed){
		header("Location:../admin/views/admin-approval.php");
	}
}