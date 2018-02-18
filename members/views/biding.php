
<title>Auction - ongoing</title>
<?php 
include '../partials/navigations.php';
use Auctions\fetch_functions;

$bidder=""; $max_bid=0; $next_bid="";

if(isset($_GET['id'])){
	$p_code = htmlspecialchars($_GET['id']);
	$row = fetch_functions\get_row('sell_posts','product_code',$p_code)[0];
	if(!$row){
		header("Location:auctions.php");
	}
	else{
		if(time()+5*60*60>$row->end_time){
			$status = "expired";
		}
		elseif(time()+5*60*60<$row->start_time){
			$status = "upcoming";
		}
		else{
			$status = "ongoing";
		}
		
	}

	$member_id = $_SESSION['active_user'];

	$stmt = $con->prepare("SELECT * FROM bids WHERE product_code = :product_code ORDER BY bid DESC");
	$stmt->execute(array('product_code' => $p_code));
	$bids = $stmt->fetchAll(\PDO::FETCH_OBJ);
	if(sizeof($bids)>0){
		$bidder = fetch_functions\get_row('members','id',$bids[0]->member_id)[0];
		$max_bid_query = $con->prepare("SELECT MAX(bid) AS maximum FROM bids WHERE product_code = :product_code");
		$max_bid_query->execute(array('product_code' => $p_code));
		$max_bid_fetch = $max_bid_query->fetch(\PDO::FETCH_OBJ);
		$max_bid = $max_bid_fetch->maximum;
	}

	if($max_bid == 0){
		$next_bid = $row->starting_price - 1;
	}
	else{
		$next_bid = $max_bid + $row->bid_interval - 1;
	}

}

else{
	header("Location:auctions.php");
}

?>

	
<div class="biding-wrapper">


	<h2>
		Auction Ongoing
		<img src="../../images/others/crown.png" alt="image">
	</h2>
	<?php $code = fetch_functions\fix_product_code($row->product_code);?>
	<h4>Product Code #<?php echo $code;?></h4>





	<div class="auction-title">

		<?php $images = explode("{}{{}}}", $row->image);?>

		<a target="_blank" href="../<?php echo $images[0];?>"><img src="../<?php echo $images[0];?>" alt="Image"></a>

		<?php if($status == "expired"){
			echo "<p class=\"status-msg\">Sorry, Time for bidding on this post has expired &nbsp;:(</p>";
		}
		
		elseif($status=="upcoming"){
			echo "<p class=\"status-msg\">Sorry, Time for bidding on this post has not started yet.<br><br>starting time : " . date("d-M-Y , h:i A",$row->start_time) . "<br>Please Visit after the auction starts to place your bid.</p>";
		}

		else{
			echo 
			"<p>place your bid here. next bid must be greater than $$next_bid</p>
			<input type=\"text\">
			<button id=\"bid-it\">BID IT</button>
			<input type=\"hidden\" value=\"$next_bid\">
			<input type=\"hidden\" value=\"$p_code\">
			<input type=\"hidden\" value=\"$member_id\">";

		} ?>
		

	</div> <!-- /auction-title -->


















	
	<div class="auction-info">
		
		<div class="sticky-note">
			
			<p>Start Date : <?php echo date("d-M-Y",$row->start_time);?></p>
			<p>Start Time : <?php echo date("h:i A",$row->start_time);?></p>
			<p>End Date :  <?php echo date("d-M-Y",$row->end_time);?></p>
			<p>End Date :  <?php echo date("h:i A",$row->end_time);?></p>
			<p>Bidding Starts At : $<?php echo $row->starting_price;?></p>
			<p>Bid Increase Rate : $<?php echo $row->bid_interval;?></p>
			<p>Total Bids : <?php echo sizeof($bids);?></p>
			<br>
			<?php $name =  fetch_functions\get_row('members','id',$row->seller_id)[0];?>
			<p>Seller Name : <?php echo $name->name;?></p>
			<p>Today :  <?php echo date("d-M-Y",$row->start_time);?></p>
			<p>3 days 23 hours remaining</p>
			<a target="_blank" href="product-detail.php?id=<?php echo $row->product_code;?>">View The Post</a>

		</div> <!-- /sticky-note -->







	

		<div class="highest-bid">
			
			<section>
				<h4>Highest Bid</h4>
				<h5>
					<?php if(empty($bidder)) echo "None";
					else echo "$" . $max_bid;?>
				</h5>
			</section>

			<section>
				<h4>Highest Bidder</h4>
				<h5>
					<?php if(empty($bidder)) echo "None";
					else {
						$arr = explode(" ",$bidder->name); 
						echo "<a target=\"blank\" href=\"view-profile.php?member=$bidder->id\">$arr[0]</a>";
					} ?>

				</h5>
			</section>

		</div> <!-- /highest-bid -->
 
	</div> <!-- /auction-info -->


















	<table class="table table-bordered biding-table" id="append-table">
		<thead>
			<tr>
				<th>Member</th>
				<th>Bid</th>
				<th>Date</th>
				<th>Time</th>
			</tr>
		</thead>

		<tbody>
			<?php if(sizeof($bids)==0){
				echo 
				"<tr><td  style=\"text-align:left; color:#666;\" colspan=\"4\">No Bid Placed</td><tr>";
			}
			else{
				foreach ($bids as $bid) { 
				$name = fetch_functions\get_row('members','id',$bid->member_id)[0];
				?>
				<tr>
					<td><a target="_blank" href="view-profile.php?member=<?php echo $bid->member_id;?>"><?php echo $name->name;?></a></td>
					<td>$<?php echo $bid->bid;?></td>
					<td><?php echo date("d - M - Y",$bid->placed_on);?></td>
					<td><?php echo date("h:i A",$bid->placed_on);?></td>
				</tr>
				<?php } 
			} ?>
		</tbody>
	</table>



</div> <!-- /biding-wrapper -->










<script>
	
	$('#bid-it').on('click',function(){
		var $this = $(this),
			val = $this.prev().val(),
			nextBid = $this.next().val(),
			productCode = $this.next().next().val(),
			memberId = $this.next().next().next().val();
		
		if(val!=""){
			console.log(val);
			val = parseInt(val);
			nextBid = parseInt(nextBid);
			if(val<=nextBid){
				alert('Sorry, Your bid must be greater than $' + nextBid);
			}
			else{
				$.ajax({
					url : '../../functions/process-forms.php',
					method : 'get',
					data : {bid : val, pCode : productCode, mId : memberId},
					success : function(context){
						window.location.href=window.location.href; 
					}
				})
			}
		}
	});

</script>



<?php include '../partials/footer.php';?>