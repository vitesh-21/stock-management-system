<?php
include("db.php");

$products = mysqli_query($conn,"SELECT * FROM products WHERE quantity > 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS Checkout System</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>

body{
background:#000;
font-family:monospace;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
color:#00ff66;
}
.terminal-card{
width:420px;
border:2px solid #00ff66;
padding:20px;
background:#000;
box-shadow:0 0 20px #00ff66;
}
header h2{
text-align:center;
margin-bottom:15px;
border-bottom:1px dashed #00ff66;
padding-bottom:10px;
}
.input-group{
margin:10px 0;
display:flex;
justify-content:space-between;
align-items:center;
}
label{
width:40%;
}

select,input{
width:55%;
background:#000;
border:1px solid #00ff66;
color:#00ff66;
padding:6px;
}

.auto-field{
border:1px solid #00ff66;
padding:6px 10px;
}

.add-btn{
margin-top:10px;
width:100%;
background:#000;
border:1px solid #00ff66;
color:#00ff66;
padding:10px;
cursor:pointer;
}

.add-btn:hover{
background:#00ff66;
color:#000;
}

.cart-section{
margin-top:15px;
border-top:1px dashed #00ff66;
padding-top:10px;
}

#cartList{
margin-top:10px;
}

.total-section{
margin-top:15px;
font-size:18px;
}

.confirm-btn{
margin-top:10px;
width:100%;
background:#00ff66;
border:none;
padding:12px;
font-weight:bold;
cursor:pointer;
}

.confirm-btn:hover{
background:#00cc55;
}

/* Added for M-Pesa Phone Field visibility */
#phoneGroup { display: none; }

</style>

</head>

<body>

<div class="terminal-card">

<header>
<h2><i class="fas fa-cash-register"></i> SALES / CHECKOUT</h2>
</header>

<main>

<div class="input-group">
<label>Product:</label>

<select id="productSelect" onchange="updatePrice()">

<option value="" disabled selected>Select Product</option>

<?php while($row = mysqli_fetch_assoc($products)){ ?>

<option 
value="<?php echo $row['id']; ?>" 
data-price="<?php echo $row['price']; ?>"
data-stock="<?php echo $row['quantity']; ?>">

<?php echo $row['name']; ?> (Stock: <?php echo $row['quantity']; ?>)

</option>

<?php } ?>

</select>

</div>

<div class="input-group">
<label>Quantity:</label>
<input type="number" id="qtyInput" min="1" value="1">
</div>

<div class="input-group">
<label>Payment Method:</label>

<select id="paymentMethod" onchange="togglePhoneInput()">
<option value="Cash">Cash</option>
<option value="M-Pesa">M-Pesa</option>
<option value="Credit Card">Credit Card</option>
</select>

</div>

<div class="input-group" id="phoneGroup">
<label>Phone (254...):</label>
<input type="text" id="mpesaPhone" placeholder="2547XXXXXXXX">
</div>

<div class="input-group">
<label>Price:</label>
<span id="priceDisplay" class="auto-field"> [ Auto ] </span>
</div>

<button class="add-btn" onclick="addToCart()">
<i class="fas fa-cart-plus"></i> + Add Item
</button>

<div class="cart-section">
<p><strong><i class="fas fa-shopping-basket"></i> Cart Items</strong></p>
<div id="cartList"></div>
</div>

<div id="receipt" style="display:none; margin-top:20px; border-top:1px dashed #00ff66; padding-top:10px;">

<h3 style="text-align:center;">SMART SHOP POS</h3>

<div id="receiptItems"></div>

<p>--------------------------------</p>

<p>Total: Ksh <span id="receiptTotal"></span></p>

<p>Payment: <span id="receiptPayment"></span></p>

<p id="receiptMpesa" style="display:none;">M-Pesa Code: <span id="receiptCode"></span></p>

<p>--------------------------------</p>

<p style="text-align:center;">THANK YOU FOR SHOPPING</p>

</div>
</main>

<footer>

<div class="total-section">
Total: Ksh <span id="grandTotal">0</span>
</div>

<button class="confirm-btn" onclick="confirmSale()">
<i class="fas fa-check-circle"></i> [ Confirm Sale & Print ]
</button>

</footer>

</div>

<script>

let cart = [];
let total = 0;

// Function to show/hide phone input based on selection
function togglePhoneInput() {
    let method = document.getElementById("paymentMethod").value;
    document.getElementById("phoneGroup").style.display = (method === "M-Pesa") ? "flex" : "none";
}

function updatePrice(){

let product = document.getElementById("productSelect");

let price = product.options[product.selectedIndex].getAttribute("data-price");

document.getElementById("priceDisplay").innerText = "Ksh " + price;

}

function addToCart(){

let product = document.getElementById("productSelect");
let qty = document.getElementById("qtyInput").value;

if(product.value === ""){
alert("Select a product first");
return;
}

let name = product.options[product.selectedIndex].text;
let price = product.options[product.selectedIndex].getAttribute("data-price");
let productId = product.value; 

let itemTotal = price * qty;

cart.push({
id: productId, 
name:name,
qty:qty,
price:price,
total:itemTotal
});

total += itemTotal;

updateCart();

}

function updateCart(){

let cartDiv = document.getElementById("cartList");

cartDiv.innerHTML = "";

cart.forEach(item => {

cartDiv.innerHTML += `
<div>
${item.name} x ${item.qty} = Ksh ${item.total}
</div>
`;

});

document.getElementById("grandTotal").innerText = total;

}

/* CONFIRM SALE */

// ... existing updateCart() function above ...

/* CONFIRM SALE - Replace the old one with this */
function confirmSale(){
    if(cart.length == 0){
        alert("Cart is empty");
        return;
    }

    let payment = document.getElementById("paymentMethod").value;
    let totalText = document.getElementById("grandTotal").innerText;
    let totalValue = totalText.replace("Ksh ", "").trim();
    let mpesaCode = "";

    if(payment === "M-Pesa") {
        mpesaCode = prompt("Enter M-Pesa Transaction Code (e.g., RCL4H7X9Z):");
        
        if(mpesaCode == null || mpesaCode == "") {
            alert("Transaction cancelled. M-Pesa code is required.");
            return;
        }
    }

    fetch("process_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            cart: cart,            
            payment_method: payment, 
            mpesa_code: mpesaCode, 
            total: totalValue      
        })
    })
   .then(response => response.text())
.then(data => {

alert("✅ TRANSACTION SUCCESSFUL!\n" + data);

createReceipt(payment, mpesaCode);

printReceipt();

location.reload();

})

// ... existing createReceipt() function below ...



/* CREATE RECEIPT */

function createReceipt(payment, mpesaCode){

let receiptItems = document.getElementById("receiptItems");

receiptItems.innerHTML = "";

cart.forEach(item => {

receiptItems.innerHTML += `
<p>${item.name} x${item.qty} - Ksh ${item.total}</p>
`;

});

document.getElementById("receiptTotal").innerText = total;

document.getElementById("receiptPayment").innerText = payment;

if(payment === "M-Pesa"){
document.getElementById("receiptMpesa").style.display = "block";
document.getElementById("receiptCode").innerText = mpesaCode;
}

document.getElementById("receipt").style.display = "block";

}

/* PRINT RECEIPT */

function printReceipt(){

let receiptContent = document.getElementById("receipt").innerHTML;

let date = new Date().toLocaleString();

let newWindow = window.open("", "", "width=300,height=600");

newWindow.document.write(`
<html>
<head>
<title>Receipt</title>
<style>
body{
font-family:monospace;
text-align:center;
}
</style>
</head>
<body>

${receiptContent}

<p>Date: ${date}</p>

</body>
</html>
`);

newWindow.document.close();
newWindow.print();

}

newWindow.document.close();
newWindow.print();

}

</script>
</body>
</html>