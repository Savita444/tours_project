<!-- Header-->
<header class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Rural Tourism </h1>
            <p class="lead fw-normal text-white-50 mb-0">Enjoy The Trip With Us !</p>
        </div>
    </div>
</header>
<style>
    .slick-prev, .slick-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5) !important;
    color: white;
    border: none;
    padding: 10px 15px;
    font-size: 24px;
    cursor: pointer;
    z-index: 1000;
    border-radius: 50%;
}

.slick-prev {
    left: -50px; /* Adjust position as needed */
}

.slick-next {
    right: -50px; /* Adjust position as needed */
}

.slick-prev:hover, .slick-next:hover {
    background-color: rgba(0, 0, 0, 0.8);
}

@media (max-width: 768px) {
    .slick-prev {
        left: -30px; /* Adjust for smaller screens */
    }

    .slick-next {
        right: -30px; /* Adjust for smaller screens */
    }
}

</style>

<!-- Slick CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css"/>

<!-- jQuery (Required for Slick) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Slick JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>

<!-- Domestic Tours -->
<section>
    <div class="container px-4 px-lg-5 mt-5">
        <h2 class="text-center text-dark mb-4">Domestic Tours</h2>
        <div class="slider"> <!-- Added this div for Slick -->
            <?php 
                // Query for Domestic Tours (parent_id = 1)
                $domesticTours = $conn->query("
                    SELECT p.* 
                    FROM `products` p
                    JOIN `sub_categories` s ON p.sub_category_id = s.id
                    WHERE s.parent_id = 1 AND p.status = 1
                    ORDER BY RAND() LIMIT 8
                ");
                while($row = $domesticTours->fetch_assoc()):
                    $upload_path = base_app.'/uploads/product_'.$row['id'];
                    $img = "";
                    if(is_dir($upload_path)){
                        $fileO = scandir($upload_path);
                        if(isset($fileO[2]))
                            $img = "uploads/product_".$row['id']."/".$fileO[2];
                    }
                    $inventory = $conn->query("SELECT * FROM inventory WHERE product_id = ".$row['id']);
                    $inv = array();
                    while($ir = $inventory->fetch_assoc()){
                        $inv[$ir['size']] = number_format($ir['price']);
                    }
            ?>
            <div class="col">
                <div class="card h-100 product-item">
                    <!-- Product image-->
                    <img class="card-img-top w-100" src="<?php echo validate_image($img) ?>" alt="..." />
                    <!-- Product details-->
                    <div class="card-body p-4">
                        <div class="text-center">
                            <!-- Product name-->
                            <h5 class="fw-bolder"><?php echo $row['product_name'] ?></h5>
                            <!-- Product price-->
                            <?php foreach($inv as $k=> $v): ?>
                                <span><?php echo  ucfirst(strtolower($k)) ?>: <b>&#8377; <?php echo $v ?></b></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Product actions-->
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center">
                            <a class="btn btn-flat btn-primary" href=".?p=view_product&id=<?php echo md5($row['id']) ?>">View More</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- International Tours -->
<section>
    <div class="container px-4 px-lg-5 mt-4">
        <h2 class="text-center text-dark mb-4">International Tours</h2>
        <div class="row gx-4 gx-lg-5 row-cols-md-3 row-cols-xl-4 justify-content-center">
            <?php 
                // Query for International Tours (parent_id = 4)
                $internationalTours = $conn->query("
                    SELECT p.* 
                    FROM `products` p
                    JOIN `sub_categories` s ON p.sub_category_id = s.id
                    WHERE s.parent_id = 4 AND p.status = 1
                    ORDER BY RAND() LIMIT 8
                ");
                while($row = $internationalTours->fetch_assoc()):
                    $upload_path = base_app.'/uploads/product_'.$row['id'];
                    $img = "";
                    if(is_dir($upload_path)){
                        $fileO = scandir($upload_path);
                        if(isset($fileO[2]))
                            $img = "uploads/product_".$row['id']."/".$fileO[2];
                    }
                    $inventory = $conn->query("SELECT * FROM inventory WHERE product_id = ".$row['id']);
                    $inv = array();
                    while($ir = $inventory->fetch_assoc()){
                        $inv[$ir['size']] = number_format($ir['price']);
                    }
            ?>
            <div class="col mb-5">
                <div class="card h-100 product-item">
                    <!-- Product image-->
                    <img class="card-img-top w-100" src="<?php echo validate_image($img) ?>" alt="..." />
                    <!-- Product details-->
                    <div class="card-body p-4">
                        <div class="text-center">
                            <!-- Product name-->
                            <h5 class="fw-bolder"><?php echo $row['product_name'] ?></h5>
                            <!-- Product price-->
                            <?php foreach($inv as $k=> $v): ?>
                                <span><?php echo  ucfirst(strtolower($k)) ?>: <b>&#8377; <?php echo $v ?></b></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Product actions-->
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center">
                            <a class="btn btn-flat btn-primary" href=".?p=view_product&id=<?php echo md5($row['id']) ?>">View More</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>



<script>
    $(document).ready(function(){
        $('.slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 2000,
            dots: false,  /* Hide indicators */
            arrows: true, /* Enable previous & next arrows */
            prevArrow: '<button type="button" class="slick-prev">&#10094;</button>',
            nextArrow: '<button type="button" class="slick-next">&#10095;</button>',
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
    });
</script>