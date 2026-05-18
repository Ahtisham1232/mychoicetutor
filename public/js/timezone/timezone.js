document.addEventListener("DOMContentLoaded", function () {
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    console.log("User timezone:", timezone);

    document.getElementById("timezone").value = timezone;
});