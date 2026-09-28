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

    const addMenuButton =
        document.getElementById("addMenuButton");




    hamburger.addEventListener("click", function () {

        sidebar.classList.add("open");
        overlay.classList.add("show");
        document.body.classList.add("menu-open");

    });


    function closeSidebar() {

        sidebar.classList.remove("open");
        overlay.classList.remove("show");
        document.body.classList.remove("menu-open");

    }


    closeMenu.addEventListener(
        "click",
        closeSidebar
    );


    overlay.addEventListener(
        "click",
        closeSidebar
    );




    addMenuButton.addEventListener(
        "click",
        function () {

            const name =
                window.prompt("Food name:");

            if (name === null) {
                return;
            }


            const trimmedName =
                name.trim();


            if (trimmedName === "") {

                alert("Food name is required.");

                return;

            }


            const description =
                window.prompt("Description:");


            if (description === null) {
                return;
            }


            const priceInput =
                window.prompt("Price:");


            if (priceInput === null) {
                return;
            }


            const price =
                Number(priceInput);


            if (
                !Number.isFinite(price) ||
                price <= 0
            ) {

                alert(
                    "Please enter a valid price."
                );

                return;

            }


            const category =
                window.prompt(
                    "Category:"
                );


            if (category === null) {
                return;
            }


            const image =
                window.prompt(
                    "Image URL (optional):"
                );


            if (image === null) {
                return;
            }


            addFood({
                name:
                    trimmedName,

                description:
                    description.trim(),

                price:
                    price,

                category:
                    category.trim(),

                image:
                    image.trim()
            });

        }
    );




    async function addFood(food) {

        addMenuButton.disabled =
            true;

        addMenuButton.textContent =
            "Adding...";


        try {

            const response =
                await fetch(
                    "../api/admin_menu.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        credentials:
                            "same-origin",

                        body:
                            JSON.stringify({

                                action:
                                    "add",

                                name:
                                    food.name,

                                description:
                                    food.description,

                                price:
                                    food.price,

                                category:
                                    food.category,

                                image:
                                    food.image

                            })
                    }
                );


            const data =
                await response.json();


            if (!data.success) {

                alert(
                    data.message ||
                    "Unable to add food."
                );

                return;

            }


            alert(
                data.message ||
                "Food added successfully."
            );


            window.location.reload();


        } catch (error) {

            console.error(
                "Add food error:",
                error
            );


            alert(
                "Unable to connect to the server."
            );


        } finally {

            addMenuButton.disabled =
                false;

            addMenuButton.textContent =
                "+ Add Food";

        }

    }



    document.querySelectorAll(
        ".edit-button"
    ).forEach(function (button) {

        button.addEventListener(
            "click",
            function () {

                editFood(
                    Number(
                        button.dataset.id
                    )
                );

            }
        );

    });


    async function editFood(id) {

        const card =
            document.querySelector(
                `.edit-button[data-id="${id}"]`
            )?.closest(
                ".food-card"
            );


        if (!card) {
            return;
        }


        const nameElement =
            card.querySelector("h3");

        const descriptionElement =
            card.querySelector("p");

        const priceElement =
            card.querySelector(".food-price");

        const categoryElement =
            card.querySelector(".food-category");


        const currentName =
            nameElement
                ? nameElement.textContent.trim()
                : "";


        const currentDescription =
            descriptionElement
                ? descriptionElement.textContent.trim()
                : "";


        const currentPrice =
            priceElement
                ? priceElement.textContent
                    .replace("₱", "")
                    .replace(/,/g, "")
                    .trim()
                : "0";


        const currentCategory =
            categoryElement
                ? categoryElement.textContent.trim()
                : "";


        const name =
            window.prompt(
                "Food name:",
                currentName
            );


        if (name === null) {
            return;
        }


        if (name.trim() === "") {

            alert(
                "Food name is required."
            );

            return;

        }


        const description =
            window.prompt(
                "Description:",
                currentDescription
            );


        if (description === null) {
            return;
        }


        const priceInput =
            window.prompt(
                "Price:",
                currentPrice
            );


        if (priceInput === null) {
            return;
        }


        const price =
            Number(priceInput);


        if (
            !Number.isFinite(price) ||
            price <= 0
        ) {

            alert(
                "Please enter a valid price."
            );

            return;

        }


        const category =
            window.prompt(
                "Category:",
                currentCategory
            );


        if (category === null) {
            return;
        }


        const image =
            window.prompt(
                "Image URL (optional):"
            );


        if (image === null) {
            return;
        }


        button.disabled =
            true;


        button.textContent =
            "Saving...";


        try {

            const response =
                await fetch(
                    "../api/admin_menu.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        credentials:
                            "same-origin",

                        body:
                            JSON.stringify({

                                action:
                                    "edit",

                                id:
                                    id,

                                name:
                                    name.trim(),

                                description:
                                    description.trim(),

                                price:
                                    price,

                                category:
                                    category.trim(),

                                image:
                                    image.trim()

                            })
                    }
                );


            const data =
                await response.json();


            if (!data.success) {

                alert(
                    data.message ||
                    "Unable to update food."
                );

                return;

            }


            alert(
                data.message ||
                "Food updated successfully."
            );


            window.location.reload();


        } catch (error) {

            console.error(
                "Edit food error:",
                error
            );


            alert(
                "Unable to connect to the server."
            );


        } finally {

            button.disabled =
                false;

            button.textContent =
                "Edit";

        }

    }




    document.querySelectorAll(
        ".toggle-button"
    ).forEach(function (button) {

        button.addEventListener(
            "click",
            function () {

                const id =
                    Number(
                        button.dataset.id
                    );

                const currentAvailability =
                    Number(
                        button.dataset.available
                    );


                const newAvailability =
                    currentAvailability === 1
                        ? 0
                        : 1;


                toggleFood(
                    button,
                    id,
                    newAvailability
                );

            }
        );

    });



    async function toggleFood(
        button,
        id,
        isAvailable
    ) {

        button.disabled =
            true;

        button.textContent =
            "Updating...";


        try {

            const response =
                await fetch(
                    "../api/admin_menu.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        credentials:
                            "same-origin",

                        body:
                            JSON.stringify({

                                action:
                                    "toggle",

                                id:
                                    id,

                                is_available:
                                    isAvailable

                            })
                    }
                );


            const data =
                await response.json();


            if (!data.success) {

                alert(
                    data.message ||
                    "Unable to update availability."
                );

                return;

            }


            window.location.reload();


        } catch (error) {

            console.error(
                "Toggle food error:",
                error
            );


            alert(
                "Unable to connect to the server."
            );


        } finally {

            button.disabled =
                false;

        }

    }



    logoutButton.addEventListener(
        "click",
        async function () {

            logoutButton.disabled =
                true;


            try {

                const response =
                    await fetch(
                        "../api/logout.php",
                        {
                            method: "POST",
                            credentials:
                                "same-origin"
                        }
                    );


                const data =
                    await response.json();


                if (data.success) {

                    window.location.href =
                        "../login.html";

                    return;

                }


                logoutButton.disabled =
                    false;


                alert(
                    data.message ||
                    "Unable to logout."
                );


            } catch (error) {

                console.error(
                    "Logout error:",
                    error
                );


                logoutButton.disabled =
                    false;


                alert(
                    "Unable to connect to the server."
                );

            }

        }
    );

});