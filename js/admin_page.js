document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleSidebar");
    const logo = document.getElementById("sidebarLogo");
    const backBtn = document.getElementById("backBtn");

    // Toggle sidebar
    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        logo.classList.toggle("hidden");
    });

    // Navigasi sidebar
    const navMap = {
        dashboardBtn: "index.php",
        reportRequestBtn: "report_request.php",
        reportStatusBtn: "status_report.php"
    };

    Object.keys(navMap).forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener("click", () => {
                window.location.href = navMap[id];
            });
        }
    });

    // Back button
    backBtn.addEventListener("click", () => {
        window.location.href = "index.php";
    });

    // Division card click
    document.querySelectorAll('.division-card').forEach(card => {
        if (!card.classList.contains('disabled')) {
            card.addEventListener('click', () => {
                window.location.href = card.dataset.href;
            });
        }
    });
});
