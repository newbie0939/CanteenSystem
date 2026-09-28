document.addEventListener("DOMContentLoaded", function () {

    const sidebar =
        document.getElementById("sidebar");

    const hamburger =
        document.getElementById("hamburger");

    const closeMenu =
        document.getElementById("closeMenu");

    const overlay =
        document.getElementById("sidebarOverlay");

    const logoutButton =
        document.getElementById("logoutButton");

    const cartItems =
        document.getElementById("cartItems");

    const emptyCart =
        document.getElementById("emptyCart");

    const orderSummary =
        document.getElementById("orderSummary");

    const summaryItems =
        document.getElementById("summaryItems");

    const summarySubtotal =
        document.getElementById("summarySubtotal");

    const summaryTotal =
        document.getElementById("summaryTotal");

    const checkoutButton =
        document.getElementById("checkoutButton");



    let cart =
        JSON.parse(
            localStorage.getItem(
                "canteen_cart"
            )
        ) || [];

    cart.forEach(function (item, index) {

        if (!item.stackNumber) {

            item.stackNumber = index + 1;

        }

    });

    localStorage.setItem(
        "canteen_cart",
        JSON.stringify(cart)
    );


    hamburger.addEventListener(
        "click",
        function () {

            sidebar.classList.add("open");

            overlay.classList.add("show");

        }
    );


    function closeSidebar() {

        sidebar.classList.remove("open");

        overlay.classList.remove("show");

    }


    closeMenu.addEventListener(
        "click",
        closeSidebar
    );


    overlay.addEventListener(
        "click",
        closeSidebar
    );



    function saveCart() {

        localStorage.setItem(
            "canteen_cart",
            JSON.stringify(cart)
        );

    }




    function formatPrice(price) {

        return "₱" +
            Number(price).toLocaleString(
                "en-PH",
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );

    }




    function escapeHtml(value) {

        const div =
            document.createElement("div");

        div.textContent = value;

        return div.innerHTML;

    }




    function renderCart() {

        cartItems.innerHTML = "";


        if (cart.length === 0) {

            emptyCart.style.display =
                "block";

            orderSummary.style.display =
                "none";

            return;

        }


        emptyCart.style.display =
            "none";

        orderSummary.style.display =
            "block";


        let totalQuantity = 0;

        let totalPrice = 0;



        const stackDisplay =
            [...cart].reverse();


        stackDisplay.forEach(function (item) {

            const originalIndex =
                cart.indexOf(item);


            const quantity =
                Number(item.quantity || 1);


            const price =
                Number(item.price);


            const subtotal =
                price * quantity;


            totalQuantity +=
                quantity;


            totalPrice +=
                subtotal;


            const itemElement =
                document.createElement("div");


            itemElement.className =
                "cart-item";


            itemElement.innerHTML = `

                <div class="stack-number">
                    #${Number(item.stackNumber)}
                </div>


                <div class="cart-item-image">

                    ${
                        item.image
                            ? `
                                <img
                                    src="${escapeHtml(item.image)}"
                                    alt="${escapeHtml(item.name)}"
                                    style="
                                        width: 80px;
                                        height: 80px;
                                        object-fit: cover;
                                        display: block;
                                    "
                                >
                            `
                            : `
                                <span>
                                    🍽️
                                </span>
                            `
                    }

                </div>


                <div class="cart-item-info">

                    <h3>
                        ${escapeHtml(item.name)}
                    </h3>

                    <div class="cart-item-price">
                        ${formatPrice(price)}
                        each
                    </div>

                    <div class="cart-item-subtotal">
                        ${formatPrice(subtotal)}
                    </div>

                </div>


                <div class="quantity-control">

                    <button
                        type="button"
                        class="quantity-button decrease-button"
                        data-index="${originalIndex}"
                    >
                        −
                    </button>


                    <span class="quantity-value">
                        ${quantity}
                    </span>


                    <button
                        type="button"
                        class="quantity-button increase-button"
                        data-index="${originalIndex}"
                    >
                        +
                    </button>

                </div>

            `;


            cartItems.appendChild(
                itemElement
            );

        });


        summaryItems.textContent =
            totalQuantity;


        summarySubtotal.textContent =
            formatPrice(totalPrice);


        summaryTotal.textContent =
            formatPrice(totalPrice);


        checkoutButton.disabled =
            false;

    }




    cartItems.addEventListener(
        "click",
        function (event) {

            const target =
                event.target;


            const index =
                Number(
                    target.dataset.index
                );


            if (
                Number.isNaN(index) ||
                !cart[index]
            ) {

                return;

            }




            if (
                target.classList.contains(
                    "increase-button"
                )
            ) {

                cart[index].quantity =
                    Number(
                        cart[index].quantity || 1
                    ) + 1;


                saveCart();

                renderCart();

                return;

            }



            if (
                target.classList.contains(
                    "decrease-button"
                )
            ) {

                if (
                    Number(cart[index].quantity) > 1
                ) {

                    cart[index].quantity -= 1;

                    saveCart();

                    renderCart();

                } else {

                    alert(
                        "Quantity cannot be less than 1. Use Undo Last Item to remove the latest item."
                    );

                }

            }

        }
    );



    const undoLastButton =
        document.getElementById(
            "undoLastButton"
        );


    if (undoLastButton) {

        undoLastButton.addEventListener(
            "click",
            function () {

                if (cart.length === 0) {

                    return;

                }



                const removedItem =
                    cart.pop();


                saveCart();

                renderCart();


                alert(
                    removedItem.name +
                    " (#" +
                    removedItem.stackNumber +
                    ") was removed from the top of the stack."
                );

            }
        );

    }


    checkoutButton.addEventListener(
        "click",
        async function () {

            if (cart.length === 0) {

                return;

            }


            const originalText =
                checkoutButton.textContent;


            checkoutButton.disabled =
                true;

            checkoutButton.textContent =
                "Placing Order...";


            try {

                const response =
                    await fetch(
                        "../api/place_order.php",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/json"
                            },

                            body: JSON.stringify({
                                cart: cart
                            })
                        }
                    );


                const data =
                    await response.json();


                if (!data.success) {

                    alert(
                        data.message ||
                        "Unable to place order."
                    );


                    checkoutButton.disabled =
                        false;

                    checkoutButton.textContent =
                        originalText;

                    return;

                }



                localStorage.removeItem(
                    "canteen_cart"
                );




                alert(
                    "Order placed successfully!\n\n" +
                    "Order Number: " +
                    data.order_number +
                    "\n" +
                    "Queue Position: " +
                    data.queue_position +
                    "\n" +
                    "Total: ₱" +
                    data.total_amount
                );


                window.location.href =
                    "orders.php";


            } catch (error) {

                console.error(error);


                alert(
                    "Unable to connect to the server."
                );


                checkoutButton.disabled =
                    false;

                checkoutButton.textContent =
                    originalText;

            }

        }
    );



    logoutButton.addEventListener(
        "click",
        async function () {

            try {

                const response =
                    await fetch(
                        "../api/logout.php",
                        {
                            method: "POST"
                        }
                    );


                const data =
                    await response.json();


                if (data.success) {

                    localStorage.removeItem(
                        "canteen_cart"
                    );


                    window.location.href =
                        "../login.html";

                }

            } catch (error) {

                console.error(error);

                alert(
                    "Unable to connect to the server."
                );

            }

        }
    );


    renderCart();

});