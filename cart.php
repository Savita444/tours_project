
<section class="py-5 flex-grow-1">
    <div class="container">
        <div class="row">
            <div class="col d-flex justify-content-end mb-2">
                <button class="btn btn-outline-dark btn-flat btn-sm" type="button" id="empty_cart">Remove Tour</button>
            </div>
        </div>
        <div class="card rounded-0">
            <div class="card-body">
                <h3><b>Booking Form</b></h3>
                <hr class="border-dark">
                <?php 
                    $hasItems = false; // Flag to check if cart has items
                    $qry = $conn->query("SELECT c.*,p.product_name,i.size,i.price,p.id as pid FROM `cart` c 
                        INNER JOIN `inventory` i ON i.id=c.inventory_id 
                        INNER JOIN products p ON p.id = i.product_id 
                        WHERE c.client_id = ".$_settings->userdata('id'));

                    while($row = $qry->fetch_assoc()):
                        $hasItems = true; // Set flag to true if there are items
                        $upload_path = base_app.'/uploads/product_'.$row['pid'];
                        $img = "";
                        if(is_dir($upload_path)){
                            $fileO = scandir($upload_path);
                            if(isset($fileO[2]))
                                $img = "uploads/product_".$row['pid']."/".$fileO[2];
                        }
                ?>
                    <div class="d-flex w-100 justify-content-between  mb-2 py-2 border-bottom cart-item">
                        <div class="d-flex align-items-center col-8">
                            <span class="mr-2"><a href="javascript:void(0)" class="btn btn-sm btn-outline-danger rem_item" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash"></i></a></span>
                            <img src="<?php echo validate_image($img) ?>" loading="lazy" class="cart-prod-img mr-2 mr-sm-2 border" alt="">
                            <div>
                                <p class="mb-1 mb-sm-1"><?php echo $row['product_name'] ?></p>
                                <!-- <p class="mb-1 mb-sm-1"><small><b>Size:</b> <?php //echo $row['size'] ?></small></p> -->
                                <p class="mb-1 mb-sm-1"><small><b>Price Per Person: </b>&#8377; <span class="price"><?php echo number_format($row['price']) ?></span></small></p>
                                <div>
                                <p class="mb-1 mb-sm-1"><b>Add Total Person Count</b></p>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-sm btn-outline-secondary min-qty" type="button" id="button-addon1"><i class="fa fa-minus"></i></button>
                                    </div>
                                    <input type="number" class="form-control form-control-sm qty text-center cart-qty" placeholder="" aria-label="Example text with button addon" value="<?php echo $row['quantity'] ?>" aria-describedby="button-addon1" data-id="<?php echo $row['id'] ?>" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-outline-secondary plus-qty" type="button" id="button-addon1"><i class="fa fa-plus"></i></button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col text-right align-items-center d-flex justify-content-end">
                            <h4>&#8377; <b class="total-amount"><?php echo number_format($row['price'] * $row['quantity']) ?></b></h4>
                        </div>
                            <!-- Add individual Book Tour button -->
                        <div class="col text-right align-items-center d-flex justify-content-end">
                            <a href="./?p=checkout&cart_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-flat btn-dark">Book Tour</a>
                        </div>
                    </div>
                <?php endwhile; ?>
                <!-- <?php //if ($hasItems): ?>
                    <div class="d-flex w-100 justify-content-between mb-2 py-2 border-bottom">
                        <div class="col-8 d-flex justify-content-end"><h4>Grand Total:</h4></div>
                        <div class="col d-flex justify-content-end">&#8377; <h4 id="grand-total">-</h4></div>
                    </div>
                <?php //endif; ?> -->
            </div>
        </div>
        <!-- <div class="d-flex w-100 justify-content-end">
            <a href="./?p=checkout" class="btn btn-sm btn-flat btn-dark">Checkout</a>
        </div> -->

        <!-- <?php //if ($hasItems): ?>
            <div class="d-flex w-100 justify-content-end">
                <a href="./?p=checkout" class="btn btn-sm btn-flat btn-dark">Book Tour</a>
            </div>
        <?php //endif; ?> -->
    </div>
</section>
<script>
    function calc_total(){
        var total  = 0

        $('.total-amount').each(function(){
            amount = $(this).text()
            amount = amount.replace(/\,/g,'')
            amount = parseFloat(amount)
            total += amount
        })
        $('#grand-total').text(parseFloat(total).toLocaleString('en-US'))
    }
    function qty_change($type,_this){
        var qty = _this.closest('.cart-item').find('.cart-qty').val()
        var price = _this.closest('.cart-item').find('.price').text()
        var cart_id = _this.closest('.cart-item').find('.cart-qty').attr('data-id')
        var new_total = 0
        start_loader();
        if($type == 'minus'){
            qty = parseInt(qty) - 1
        }else{
            qty = parseInt(qty) + 1
        }
        price = parseFloat(price)
        // console.log(qty,price)
        new_total = parseFloat(qty * price).toLocaleString('en-US')
        _this.closest('.cart-item').find('.cart-qty').val(qty)
        _this.closest('.cart-item').find('.total-amount').text(new_total)
        calc_total()

        $.ajax({
            url:'classes/Master.php?f=update_cart_qty',
            method:'POST',
            data:{id:cart_id, quantity: qty},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("an error occured", 'error');
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    end_loader()
                }else{
                    alert_toast("an error occured", 'error');
                    end_loader()
                }
            }

        })
    }
    function rem_item(id){
        $('.modal').modal('hide')
        var _this = $('.rem_item[data-id="'+id+'"]')
        var id = _this.attr('data-id')
        var item = _this.closest('.cart-item')
        start_loader();
        $.ajax({
            url:'classes/Master.php?f=delete_cart',
            method:'POST',
            data:{id:id},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("an error occured", 'error');
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    item.hide('slow',function(){ item.remove() })
                    calc_total()
                    end_loader()
                }else{
                    alert_toast("an error occured", 'error');
                    end_loader()
                }
            }

        })
    }
    function empty_cart(){
        start_loader();
        $.ajax({
            url:'classes/Master.php?f=empty_cart',
            method:'POST',
            data:{},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("an error occured", 'error');
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                   location.reload()
                }else{
                    alert_toast("an error occured", 'error');
                    end_loader()
                }
            }

        })
    }
    $(function(){
        calc_total()
        $('.min-qty').click(function(){
            qty_change('minus',$(this))
        })
        $('.plus-qty').click(function(){
            qty_change('plus',$(this))
        })
        $('#empty_cart').click(function(){
            // empty_cart()
            _conf("Are you sure to empty your cart list?",'empty_cart',[])
        })
        $('.rem_item').click(function(){
            _conf("Are you sure to remove the item in cart list?",'rem_item',[$(this).attr('data-id')])
        })
    })
</script>