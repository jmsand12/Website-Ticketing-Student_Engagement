// Ambil elemen
const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleSidebar");
const logo = document.querySelector(".sidebar-logo");
const logoTopbarImg = document.querySelector(".logo-topbar img");

// Sidebar toggle
toggleBtn.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
  logo.classList.toggle("hidden");
  logoTopbarImg.classList.toggle("hidden");
});

// Dropdown Login/Logout toggle
document.addEventListener("DOMContentLoaded", () => {
  const dropbtn = document.querySelector(".dropbtn");
  const dropdownContent = document.querySelector(".dropdown-content");

  if (dropbtn && dropdownContent) {
    dropbtn.addEventListener("click", (e) => {
      e.stopPropagation(); // cegah event bubble ke document
      dropdownContent.classList.toggle("show");
    });

    // Tutup dropdown kalau klik di luar
    document.addEventListener("click", (e) => {
      if (!dropdownContent.contains(e.target) && !dropbtn.contains(e.target)) {
        dropdownContent.classList.remove("show");
      }
    });
  }
});
