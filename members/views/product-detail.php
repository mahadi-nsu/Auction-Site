
<title>Product Detail</title>
<?php 
include '../partials/navigations.php';
use Auctions\fetch_functions;

if(isset($_GET['id'])){
	$product_code = htmlspecialchars($_GET['id']);
	$row = fetch_functions\get_row('sell_posts','product_code',$product_code)[0];
	if(sizeof($row)<1){
		header("Location:auctions.php");
	}
}
else{
	header("Location:posts.php");
}


// MODIFY VARIABLES
$code = fetch_functions\fix_product_code($row->product_code);

$time = time()+5*60*60;
if($time >= $row->start_time && $time <= $row->end_time){
	$status = "ongoing";
}

elseif($time > $row->end_time){
	$status = "Expired";
}

elseif($time < $row->start_time){
	$status = "Upcoming";
}

?>


<div class="product-details">
	
	<h2><?php echo $row->title;?> <span><i class="fa fa-bookmark"></i> <?php echo $status;?></span></h2>
	<h4>Product Code # <?php echo $code;?></h4>




	<div class="banner">
		<?php $images = explode("{}{{}}}", $row->image);?>
		<img id="banner-image" src="../<?php echo $images[0];?>" alt="Banner Picture">
		<div class="small-images">
			<?php 
			foreach ($images as $image) {
				echo "<img src=\"../$image\" alt=\"Thumbnail\">";
			}
			?>
		</div>
	</div>




	<p class="product-description"><?php echo $row->description;?></p>




	<table class="left-table">
		<tr>
			<th>Starting Date</th>
			<td><?php echo date("d-M-Y",$row->start_time) . " at " . date("h:i A",$row->start_time);?></td>
		</tr>
		<tr>
			<th>End Date</th>
			<td><?php echo date("d-M-Y",$row->end_time) . " at " . date("h:i A",$row->end_time);?></td>
		</tr>
		<tr>
			<th>Duration</th>
			<?php $difference = $row->end_time - $row->start_time;
			$day = floor($difference/(24*60*60));
			$difference = $difference - ($day*24*60*60);
			$hour = floor($difference/(60*60));
			$difference = $difference - ($hour*60);
			?>
			<td>Approximately <?php echo $day.' Day(s) '.$hour.' Hour(s) ';?></td>
		</tr>
	</table>




	<table class="right-table">
		<tr>
			<th>Bidding Starts From</th>
			<td>$<?php echo $row->starting_price;?>.00</td>
		</tr>
		<tr>
			<th>Increase Rate</th>
			<td>$<?php echo $row->bid_interval;?>.00</td>
		</tr>
		<tr>
			<th>Category</th>
			<td>Docs</td>
		</tr>
	</table>




	<p class="posted-by">
		<?php $name = fetch_functions\get_row('members','id',$row->seller_id)[0];?>
		Posted by <a href="view-profile.php?member=<?php echo $name->id;?>"><?php echo $name->name;?></a> on <em><?php echo $row->post_date;?></em>
		<br><br>
	</p>



</div> <!-- /product-details -->




<?php include '../partials/footer.php';?>



















<script>
	
	var smallImage = $('.small-images img');
	smallImage.first().addClass('reduce-brightness');

	smallImage.on('click',function(){
		var $this = $(this),
			path = $this.attr('src');

		$this.addClass('reduce-brightness').siblings('img').removeClass('reduce-brightness');
		$('#banner-image').attr('src',path);
	});
	
</script>