window.onload = () => {
     const links = document.querySelectorAll("[data-delete]");

    links.forEach(link => {
        link.addEventListener("click", async function (e) {
            e.preventDefault();

            if (confirm("Are you sure you want to delete this image?")) {
                try {
                    const response = await fetch(this.getAttribute("href"), {
                        method: "DELETE",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ "_token": this.dataset.token })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.parentElement.remove();
                        } else {
                            alert("There was an error deleting the image");
                        }
                    } else {
                        alert("Failed to delete the image. Please try again.");
                    }
                } catch (error) {
                    console.error("An error occurred:", error);
                    alert("There was an error deleting the image. Please try again.");
                }
            }
        });
    });
}
