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

                } else {

                    alert(
                        "Unable to logout."
                    );

                }

            } catch (error) {

                console.error(error);

                alert(
                    "Unable to connect to the server."
                );

            }

        }
    );

});