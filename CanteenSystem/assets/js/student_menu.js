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




    let cart =
        JSON.parse(
            localStorage.getItem("canteen_cart")
        ) || [];



    function saveCart() {

        localStorage.setItem(
            "canteen_cart",
            JSON.stringify(cart)
        );

    }


    if (hamburger) {

        hamburger.addEventListener(
            "click",
            function () {

                sidebar.classList.add("open");

                overlay.classList.add("show");

            }
        );

    }


    function closeSidebar() {

        sidebar.classList.remove("open");

        overlay.classList.remove("show");

    }


    if (closeMenu) {

        closeMenu.addEventListener(
            "click",
            closeSidebar
        );

    }


    if (overlay) {

        overlay.addEventListener(
            "click",
            closeSidebar
        );

    }



    const addButtons =
        document.querySelectorAll(".add-button");


    addButtons.forEach(function (button) {

        button.addEventListener(
            "click",
            function () {



                if (cart.length >= 10) {

                    alert(
                        "Your order stack is full. Maximum of 10 items."
                    );

                    return;

                }


                const card =
                    button.closest(".food-card");


                if (!card) {

                    return;

                }



                const itemId =
                    Number(
                        button.dataset.id
                    );


                const nameElement =
                    card.querySelector("h3");


                const descriptionElement =
                    card.querySelector("p");


                const priceElement =
                    card.querySelector(".food-price");


                const imageElement =
                    card.querySelector(".food-image img");


                const name =
                    nameElement
                        ? nameElement.textContent.trim()
                        : "";


                const description =
                    descriptionElement
                        ? descriptionElement.textContent.trim()
                        : "";


                const priceText =
                    priceElement
                        ? priceElement.textContent
                        : "0";


                const price =
                    parseFloat(
                        priceText
                            .replace("₱", "")
                            .replace(/,/g, "")
                            .trim()
                    );


                const image =
                    imageElement
                        ? imageElement.getAttribute("src")
                        : "";



                const stackNumber =
                    cart.length + 1;



                cart.push({

                    id: itemId,

                    name: name,

                    description: description,

                    price: price,

                    image: image,

                    quantity: 1,

                    stackNumber: stackNumber

                });



                saveCart();



                alert(
                    name +
                    " added to your order.\n\n" +
                    "Stack #" +
                    stackNumber
                );

            }
        );

    });




    if (logoutButton) {

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

    }

});