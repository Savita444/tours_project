<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<?php 
// Get the cart_id from the URL
$cart_id = isset($_GET['cart_id']) ? $_GET['cart_id'] : 0; // Default to 0 if not set

$total = 0;
$quantity = 0; // Track quantity
$travellers = [];

if ($cart_id > 0) {
    // Query to fetch the specific cart item
    $qry = $conn->query("SELECT c.*, p.product_name, i.size, i.price, c.quantity, p.id as pid 
                         FROM `cart` c 
                         INNER JOIN `inventory` i ON i.id = c.inventory_id 
                         INNER JOIN products p ON p.id = i.product_id 
                         WHERE c.client_id = ".$_settings->userdata('id')." 
                         AND c.id = ".$cart_id);
                         
    while ($row = $qry->fetch_assoc()) {
        $total = $row['price'] * $row['quantity'];
        $quantity = $row['quantity']; // Get quantity from cart
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body">
                <h3 class="text-center"><b>Book Tour</b></h3>
                <hr class="border-dark">
                <form action="" id="place_order">
                    <input type="hidden" name="amount" value="<?php echo $total ?>">
                    <input type="hidden" name="payment_method" value="cod">
                    <input type="hidden" name="paid" value="0">
                    
                    <div class="row row-col-1 justify-content-center">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="control-label">Address</label>
                                <textarea name="delivery_address" class="form-control" rows="3" style="resize:none"><?php echo $_settings->userdata('default_delivery_address') ?></textarea>
                            </div>
                            
                            <!-- Static Email & Mobile Fields -->
                            <div class="form-group">
                                <label>Email ID</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Mobile Number</label>
                                <input type="text" name="mobile_number" class="form-control" required>
                            </div>
                            
                            <!-- Dynamic Traveller Fields -->
                            <h4>Traveller Details</h4>
                            <div id="traveller_fields">
                                <?php for ($i = 1; $i <= $quantity; $i++) { ?>
                                    <div class="traveller-section border p-2 mb-2">
                                        <h5>Traveller <?php echo $i; ?></h5>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="traveller_name[]" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select name="gender[]" class="form-control" required>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Date of Birth</label>
                                            <input type="date" name="date_of_birth[]" class="form-control" required>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div>
                                <h4><b>Total:</b> <?php echo number_format($total) ?></h4>
                            </div>
                            <hr>
                            
                            <div class="col my-3">
                                <h4 class="text-muted">Payment Method</h4>
                                <div class="d-flex w-100 justify-content-between">
                                    <button class="btn btn-flat btn-dark">Cash </button>
                                    <span id="paypal-button"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
paypal.Button.render({
    env: 'sandbox',
	client: {
        sandbox: 'ASb1ZbVxG5ZFzCWLdYLi_d1-k5rmSjvBZhxP2etCxBKXaJHxPba13JJD_D3dTNriRbAv3Kp_72cgDvaZ',
    },
    commit: true,
    style: { color: 'blue', size: 'small' },
    payment: function(data, actions) {
        return actions.payment.create({
            payment: {
                transactions: [{
                    amount: { total: '<?php echo $total; ?>', currency: 'INR' }
                }]
            }
        });
    },
    onAuthorize: function(data, actions) {
        return actions.payment.execute().then(function(payment) {
            swal('Thank you!', 'Paypal purchase successful.', 'success');
            payment_online();
        });
    }
}, '#paypal-button');

function payment_online(){
    $('[name="payment_method"]').val("Online Payment");
    $('[name="paid"]').val(1);
    $('#place_order').submit();
}

$(function(){
    $('#place_order').submit(function(e){
        e.preventDefault();
        start_loader();
        $.ajax({
            url: 'classes/Master.php?f=place_order',
            method: 'POST',
            data: $(this).serialize(),
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred", "error");
                end_loader();
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    alert_toast("Order Successfully placed.", "success");
                    setTimeout(() => location.replace('./'), 2000);
                } else {
                    console.log(resp);
                    alert_toast("An error occurred", "error");
                    end_loader();
                }
            }
        });
    });
});
</script>
