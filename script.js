$(document).ready(function() {
    $(document).on("click", ".fav", function() {
        const heart = $(this);
        const ksiazkaId = heart.data("ksiazka");

        $.post("changeFav.php", { idKsiazki: ksiazkaId })
            .done(function(data) {
                if (data.trim() === "sukces") {
                    const srcFilled = "obrazki/IMG_3888.jpg";
                    const srcEmpty = "obrazki/IMG_3889.jpg";
                    const current = heart.attr("src") || "";
                    const newSrc = current.endsWith("IMG_3889.jpg") ? srcFilled : srcEmpty;
                    heart.attr("src", newSrc);
                } else {
                    alert("Wystąpił błąd przy aktualizacji ulubionych.");
                }
            })
            .fail(function() {
                alert("Błąd komunikacji z serwerem.");
            });
    });
});
