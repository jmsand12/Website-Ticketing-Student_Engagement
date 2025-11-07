// ===============================
// Sidebar toggle
// ===============================
const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleSidebar");
const logoSidebar = document.querySelector(".sidebar-logo");
const logoTopbar = document.querySelector(".logo-topbar img");

if (toggleBtn) {
  toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed");
    if (logoSidebar) logoSidebar.classList.toggle("hidden");
    if (logoTopbar) {
      logoTopbar.style.display = (logoTopbar.style.display === "none") ? "block" : "none";
    }
  });
}

// ===============================
// Dropdown Login/Logout toggle
// ===============================
document.addEventListener("DOMContentLoaded", () => {
  const dropbtn = document.querySelector(".dropbtn");
  const dropdownContent = document.querySelector(".dropdown-content");

  if (dropbtn && dropdownContent) {
    dropbtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownContent.classList.toggle("show");
    });

    document.addEventListener("click", (e) => {
      if (!dropdownContent.contains(e.target) && !dropbtn.contains(e.target)) {
        dropdownContent.classList.remove("show");
      }
    });
  }
});

// ===============================
// Autocomplete NIM Mahasiswa
// ===============================
document.addEventListener("DOMContentLoaded", () => {
  const nimInput = document.getElementById("nim");
  const namaInput = document.getElementById("nama");
  const programInput = document.getElementById("program");
  const classOfInput = document.getElementById("class_of");
  const nimResults = document.getElementById("nim-results");

  if (!nimInput) return;

  nimInput.addEventListener("keyup", function () {
    const nim = nimInput.value.trim();

    if (nim.length >= 2) {
      fetch(`../db/get_mahasiswa.php?nim=${nim}`)
        .then(res => res.json())
        .then(data => {
          if (data.length > 0) {
            let table = `
              <table>
                <tr><th>NIM</th><th>Nama</th><th>Jurusan</th><th>Angkatan</th></tr>
            `;
            data.forEach(m => {
              table += `
                <tr data-nim="${m.nim}" data-nama="${m.nama}"
                    data-jurusan="${m.jurusan}" data-angkatan="${m.angkatan}">
                  <td>${m.nim}</td>
                  <td>${m.nama}</td>
                  <td>${m.jurusan}</td>
                  <td>${m.angkatan}</td>
                </tr>`;
            });
            table += "</table>";
            nimResults.innerHTML = table;
            nimResults.style.display = "block";

            document.querySelectorAll("#nim-results tr[data-nim]").forEach(row => {
              row.addEventListener("click", function () {
                nimInput.value = this.dataset.nim;
                namaInput.value = this.dataset.nama;
                programInput.value = this.dataset.jurusan;
                classOfInput.value = this.dataset.angkatan;
                nimResults.innerHTML = "";
                nimResults.style.display = "none";
              });
            });
          } else {
            nimResults.innerHTML = "<p style='padding:6px'>Tidak ada hasil</p>";
            nimResults.style.display = "block";
          }
        })
        .catch(err => console.error(err));
    } else {
      nimResults.innerHTML = "";
      nimResults.style.display = "none";
    }
  });

  // Klik luar → tutup hasil pencarian
  document.addEventListener("click", (e) => {
    if (!nimInput.contains(e.target) && !nimResults.contains(e.target)) {
      nimResults.innerHTML = "";
      nimResults.style.display = "none";
    }
  });
});

// ===============================
// Log Type & Report Type Dynamic by Role
// ===============================
document.addEventListener("DOMContentLoaded", () => {
  const logRadios = document.querySelectorAll('input[name="log_type"]');
  const reportSelect = document.getElementById("report_type");
  const subTypeLabel = document.getElementById("subtype_label");
  const subTypeSelect = document.getElementById("sub_type");

  if (!reportSelect) return;

  // ==== Report list per role ====
  const reportData = {
    studentservice: {
      harian: [
        "Pengambilan TOEIC", "Pengambilan Legalisir", "Pemutihan Absensi Sakit dan Lomba",
        "KTM", "Status Mahasiswa", "Cicilan Jangka Panjang",
        "Ujian Susulan", "Visa", "Izin Belajar", "Lain-Lain"
      ],
      khusus: [
        "Saran dan Masukan", "Kendala KRS", "Kendala Status Mahasiswa",
        "Pemutihan/Ujian Susulan Kondisi Khusus", "Konsultasi Orangtua", "Lain-Lain"
      ]
    },
    studentsupport: [
      "Konseling Rujukan", "Konseling Mandiri", "Laporan Mahasiswa Berkebutuhan Khusus",
      "Request Psikotest", "Lain-Lain"
    ],
    studentdevelopment: [
      "Laporan Prestasi", "Administrasi Lomba", "Data Ormawa",
      "Data Panitia", "Data Tutoring", "Data Conference", "TA Publikasi"
    ]
  };

  // ==== Isi dropdown Report Type ====
  function fillReportOptions(options) {
    reportSelect.innerHTML = '<option value="">-- Select Report Type --</option>';
    options.forEach(opt => {
      const o = document.createElement("option");
      o.value = opt.toLowerCase().replace(/\s+/g, "_");
      o.textContent = opt;
      reportSelect.appendChild(o);
    });
  }

  // ==== Jika Student Service, tergantung Log Type ====
  if (role === "studentservice") {
    logRadios.forEach(radio => {
      radio.addEventListener("change", e => {
        fillReportOptions(reportData.studentservice[e.target.value]);
      });
    });
    const checked = document.querySelector('input[name="log_type"]:checked');
    if (checked) fillReportOptions(reportData.studentservice[checked.value]);
  }

  // ==== Jika Student Support / Development langsung isi ====
  if (role === "studentsupport" || role === "studentdevelopment") {
    fillReportOptions(reportData[role]);
  }

 // ==== Sub-Type Logic ====
const defaultSubTypeOptions = subTypeSelect.innerHTML;

reportSelect.addEventListener("change", () => {
  const selectedType = reportSelect.value;

  // Reset tampilan & validasi
  subTypeSelect.innerHTML = defaultSubTypeOptions;
  subTypeLabel.style.display = "none";
  subTypeSelect.style.display = "none";
  subTypeSelect.removeAttribute("required");
  subTypeSelect.value = "";

  // Jika report_type = status_mahasiswa → tampilkan & wajibkan
  if (selectedType === "status_mahasiswa") {
    subTypeLabel.style.display = "block";
    subTypeSelect.style.display = "block";
    subTypeSelect.setAttribute("required", "required");
  }

  // Jika student development → khusus untuk administrasi_lomba
  if (role === "studentdevelopment" && selectedType === "administrasi_lomba") {
    subTypeSelect.innerHTML = `
      <option value="">-- Pilih Sub Type --</option>
      <option value="pengajuan">Pengajuan</option>
      <option value="rekap_latihan">Rekap Latihan</option>
      <option value="penyelesaian">Penyelesaian</option>
    `;
    subTypeLabel.style.display = "block";
    subTypeSelect.style.display = "block";
    subTypeSelect.removeAttribute("required"); 
  }
});
});


// ===============================
// Modal Konfirmasi Sebelum Submit
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".report-form");
  const confirmModal = document.getElementById("confirmModal");
  const confirmYes = document.getElementById("confirmYes");
  const confirmNo = document.getElementById("confirmNo");

  if (!form || !confirmModal) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // hentikan pengiriman otomatis
    confirmModal.style.display = "block"; // tampilkan modal
  });

  confirmYes.addEventListener("click", function () {
    confirmModal.style.display = "none";
    form.submit(); // kirim form setelah konfirmasi
  });

  confirmNo.addEventListener("click", function () {
    confirmModal.style.display = "none";
  });

  // Klik di luar modal → tutup
  window.addEventListener("click", function (event) {
    if (event.target === confirmModal) {
      confirmModal.style.display = "none";
    }
  });
});


// ===============================
// Modal Status Submission
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("statusModal");
  const closeBtn = document.querySelector(".close");
  const closeModalBtn = document.getElementById("closeModalBtn");
  const modalTitle = document.getElementById("modalTitle");
  const modalMessage = document.getElementById("modalMessage");

  if (!modal) return;

  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get("status");
  const msg = urlParams.get("msg");

  if (status) {
    modal.style.display = "block";
    if (status === "success") {
      modalTitle.textContent = "Laporan Berhasil Dikirim";
      modalMessage.textContent = "Data laporan Anda sudah tersimpan.";
    } else {
      modalTitle.textContent = "Gagal Mengirim Laporan";
      modalMessage.textContent = msg || "Terjadi kesalahan saat menyimpan data.";
    }
  }


  // Tutup modal hanya ketika user klik tombol tutup
  if (closeBtn) closeBtn.onclick = hideModal;
  if (closeModalBtn) closeModalBtn.onclick = hideModal;

  function hideModal() {
    modal.style.display = "none";
    // Tidak hapus query string agar modal tidak otomatis muncul ulang setelah refresh
    window.history.replaceState({}, document.title, window.location.pathname);
  }
});
