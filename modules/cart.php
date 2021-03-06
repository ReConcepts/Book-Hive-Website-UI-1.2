
<?php
require_once WPATH . "modules/classes/Books.php";
require_once WPATH . "modules/classes/Users.php";
require_once WPATH . "modules/classes/System_Administration.php";
require_once WPATH . "modules/classes/Transactions.php";
$transactions = new Transactions();
$system_administration = new System_Administration();
$users = new Users();
$books = new Books();

if (isset($_SERVER['HTTP_REFERER'])) {
    $previous_url = $_SERVER['HTTP_REFERER'];
}
if (!empty($_GET["cart_action"])) {
    switch ($_GET["cart_action"]) {
        case "remove":
            if (!empty($_SESSION["cart_item"])) {
                foreach ($_SESSION["cart_item"] as $k => $v) {
                    if ($_GET["code"] == $v['id'])
                        unset($_SESSION["cart_item"][$k]);
                    if (empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            unset($_SESSION["item_total"]);
            break;
    }
    App::redirectTo("{$previous_url}");
}

if (!empty($_POST) AND $_POST['action'] == "update_cart") {

    if (!empty($_SESSION["cart_item"])) {
        foreach ($_SESSION["cart_item"] as $k => $v) {
            if ($_POST["code"] == $v['id'])
                unset($_SESSION["cart_item"][$k]);
            if (empty($_SESSION["cart_item"]))
                unset($_SESSION["cart_item"]);
        }
    }

    $productByCode = $books->fetchBookDetails($_POST["code"]);
    $itemArray = array($productByCode["id"] => array('id' => $productByCode["id"], 'title' => $productByCode["title"], 'price' => $productByCode["price"], 'quantity' => ($_POST["quantity"] + 1)));

    if (!empty($_SESSION["cart_item"])) {
        if (in_array($productByCode["id"], array_keys($_SESSION["cart_item"]))) {
            foreach ($_SESSION["cart_item"] as $k => $v) {
                if ($productByCode["id"] == $k) {
                    if (empty($_SESSION["cart_item"][$v]["quantity"])) {
                        $_SESSION["cart_item"][$k][$v]["quantity"] = 0;
                    }
                    $_SESSION["cart_item"][$k][$v]["quantity"] += ($_POST["quantity"] + 1);
                }
            }
        } else {
            $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
        }
    } else {
        $_SESSION["cart_item"] = $itemArray;
    }
//    App::redirectTo($_POST["previous_url"]);
}

require_once "core/template/header.php";

if (isset($_SESSION["cart_number_of_items"]) AND $_SESSION["cart_number_of_items"] == 0) {
    ?>
    <div class="alert alert-block alert-error fade in">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>No items added to cart. <a href="?home"> Click here </a> to add items to your shopping cart before proceeding.</strong>
    </div>
    <?php
}
?>

<div class="container">
    <div class="cart-content-page">
        <h2 class="title-shop-page">my cart</h2>
        <!--        <form method="post">-->
        <div class="table-responsive">


            <?php
            if (isset($_SESSION["cart_item"])) {
                $item_total = 0;
                ?>	

                <table cellspacing="0" class="shop_table cart table">
                    <thead>
                        <tr>
                            <th class="product-remove">&nbsp;</th>
                            <th class="product-thumbnail">&nbsp;</th>
                            <th class="product-name">Product Name</th>
                            <th class="product-price">Unit Price</th>
                            <th class="product-quantity">Quantity</th>
                            <th class="product-subtotal">Sub-Total (KES)</th>
                        </tr>
                    </thead>

                    <tbody>                        
                        <?php
                        foreach ($_SESSION["cart_item"] as $item) {
                            $sub_item_total = ($item["price"] * $item["quantity"]);
                            $book_details = $books->fetchBookDetails($item["id"]);

                            if ($book_details['level_id'] == 1) {
                                $location = 'modules/images/books/ecd/';
                            } else if ($book_details['level_id'] == 2) {
                                $location = 'modules/images/books/primary/';
                            } else if ($book_details['level_id'] == 3) {
                                $location = 'modules/images/books/secondary/';
                            } else if ($book_details['level_id'] == 4) {
                                $location = 'modules/images/books/adult/';
                            }
                            ?>

                            <tr class="cart_item">
                                <td class="product-remove">
                                    <a class="remove" href="?cart&cart_action=remove&code=<?php echo $item["id"]; ?>" ><i class="fa fa-times"></i>Remove Item</a>
                                </td>
                                <td class="product-thumbnail">
                                    <img src="<?php echo $location . $book_details['cover_photo']; ?>" width="120" alt="<?php echo $book_details['title'] . " COVER PHOTO"; ?>"/>
                                </td>
                                <td class="product-name">
                                    <a href="#"><?php echo $book_details['title']; ?></a>					
                                </td>
                                <td class="product-price">
                                    <span class="amount"><?php echo "KES " . $book_details['price']; ?></span>					
                                </td>
                        <form method="post">
                            <input type="hidden" name="action" value="update_cart"/>
                            <input type="hidden" name="code" value="<?php echo $item['id']; ?>"/>
                            <input type="hidden" name="previous_url" value="<?php echo $previous_url; ?>"/>
                            <td class="product-quantity">

<!--                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="button" class="btn btn-secondary btn-danger" onclick="decrementValue()" value="-" />
                                    <input type="text" class="btn btn-secondary" name="quantity" value="<?php // echo $item["quantity"]; ?>" maxlength="4" max="1000" size="4" id="number" readonly="" />
                                    <input type="button" class="btn btn-secondary btn-info" onclick="incrementValue()" value="+" />
                                </div>-->

                                <div class="detail-qty border radius">
                                    <!--<a href="#" class="qty-down"><i class="fa fa-caret-down" aria-hidden="true"></i></a>-->
                                    <input type="number" name="quantity" placeholder="Qty" max-width="16" value="<?php echo $item["quantity"]; ?>">
                                    <!--<span class="qty-val"><?php // echo $item["quantity"];  ?></span>-->
                                    <!--<a href="#" class="qty-up"><i class="fa fa-caret-up" aria-hidden="true"></i></a>-->
                                </div>		
                                <input type="submit" value="Update Quantity" name="update_cart" class="button">
                            </td>
                        </form>

                        <td class="product-subtotal">
                            <span class="amount"><?php echo "KES " . $sub_item_total; ?></span>					
                        </td>
                        </tr>
                        <?php
                        $item_total += ($item["price"] * $item["quantity"]);
                    }
                    ?>
                    </tbody>
                </table>
            <?php } ?>

        </div>
        <!--</form>-->

        <div class="cart-collaterals">
            <div class="cart_totals ">
                <h2>Cart Totals</h2>
                <div class="table-responsive">
                    <table cellspacing="0" class="table">
                        <tbody>
                            <tr class="cart-subtotal">
                                <th>Subtotal</th>
                                <td><strong class="amount"><?php echo "KES " . $item_total; ?></strong></td>
                            </tr>
                            <tr class="shipping">
                                <th>Delivery Method</th>
                                <td>
                                    <ul id="shipping_method">
                                        <li>
                                            <input type="radio" class="shipping_method" checked="checked" value="by_buyer" id="shipping_method_0_local_pickup" data-index="0" name="shipping_method[0]">
                                            <label for="shipping_method_0_local_pickup">Pickup by Buyer(Free)</label>
                                        </li>
                                        <li>
                                            <input type="radio" class="shipping_method" value="by_seller" id="shipping_method_0_free_shipping" data-index="0" name="shipping_method[0]">
                                            <label for="shipping_method_0_free_shipping">Delivery by Seller(Charged)</label>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="order-total">
                                <th>Total</th>
                                <td><strong><span class="amount"><?php echo "KES " . $item_total; ?></span></strong> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="wc-proceed-to-checkout">
                    <a class="checkout-button button alt wc-forward" href="?cart&cart_action=empty">Empty Cart</a>
                </div>
                <div class="wc-proceed-to-checkout">
                    <a class=" go-button" href="?home">Continue Shopping</a>
                    <a style="float: right;" class=" go-button" href="?checkout">Proceed to Checkout</a>
                </div>
                <div class="wc-proceed-to-checkout">

                </div>
            </div>
        </div>
    </div>
</div>	
<!-- End Content Page -->