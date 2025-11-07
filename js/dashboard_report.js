// dashboard_report.js (revisi: chart berdasarkan semua data)
document.addEventListener("DOMContentLoaded", () => {
  let selectedId = null;
  let currentEditRow = null;
  let ticketChart = null;
  let trendChart = null;
  let reportTypeChart = null;
  let currentInterval = "monthly";

  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.getElementById("toggleSidebar");
  const logo = document.querySelector(".sidebar-logo");
  const logoTopbarImg = document.querySelector(".logo-topbar img");
  if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {
      sidebar.classList.toggle("collapsed");
      logo?.classList.toggle("hidden");
      logoTopbarImg?.classList.toggle("hidden");
    });
  }

  // ======================
  // Dropdown menu
  // ======================
  const dropbtn = document.querySelector(".dropbtn");
  const dropdownContent = document.querySelector(".dropdown-content");
  if (dropbtn && dropdownContent) {
    dropbtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownContent.classList.toggle("show");
    });
    document.addEventListener("click", (e) => {
      if (
        dropdownContent.classList.contains("show") &&
        !dropdownContent.contains(e.target) &&
        !dropbtn.contains(e.target)
      ) {
        dropdownContent.classList.remove("show");
      }
    });
  }

  // ======================
  // Modal Handling
  // ======================
  function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add("show");
  }
  function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove("show");
  }
  window.closeModal = closeModal;
  window.addEventListener("click", (e) => {
    document.querySelectorAll(".modal.show").forEach((modal) => {
      if (e.target === modal) modal.classList.remove("show");
    });
  });

  const DIVISION_OPTIONS = [
    { value: "service", label: "Service" },
    { value: "support", label: "Support" },
    { value: "development", label: "Development" },
  ];

  const laporanTable = document.getElementById("laporanTable");
  const entriesSelect = document.getElementById("entriesSelect");
  const searchInput = document.getElementById("searchInput");
  const chartInterval = document.getElementById("chartInterval");

  // ======================
  // Entries (Jumlah Baris per Halaman)
  // ======================
  let currentPage = 1; // pastikan ada di atas fungsi renderTableWithPagination

  // Pastikan dropdown punya value numerik (10, 25, 50, dst)
  if (entriesSelect) {
    entriesSelect.addEventListener("change", () => {
      currentPage = 1;
      renderTableWithPagination();
    });

    // Set default value kalau belum ada
    if (!entriesSelect.value || isNaN(parseInt(entriesSelect.value))) {
      entriesSelect.value = "10";
    }
  }


  // ======================
  // Edit & Delete Buttons
  // ======================
  function attachRowButtons() {
    document.querySelectorAll(".edit-btn").forEach((btn) => {
      if (btn.dataset.attached) return;
      btn.dataset.attached = "1";
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const row = btn.closest("tr");
        if (!row) return;
        currentEditRow = row;
        selectedId = row.dataset.id;
        document.getElementById("editId").value = selectedId;
        document.getElementById("editStatus").value = row.dataset.status || "pending";
        document.getElementById("editDivision").value =
          row.dataset.division && row.dataset.division !== "-" ? row.dataset.division : "";
        document.getElementById("editNotes").value =
          row.dataset.notes && row.dataset.notes !== "-" ? row.dataset.notes : "";
        openModal("editModal");
      });
    });

    document.querySelectorAll(".delete-btn").forEach((btn) => {
      if (btn.dataset.attached) return;
      btn.dataset.attached = "1";
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        selectedId = btn.closest("tr").dataset.id;
        openModal("deleteModal");
      });
    });
  }
  attachRowButtons();

  const deleteCancelBtn = document.getElementById("cancelDeleteBtn");
  if (deleteCancelBtn) deleteCancelBtn.addEventListener("click", () => closeModal("deleteModal"));
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
  if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener("click", () => {
      fetch("../db/delete_report.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${selectedId}`,
      })
        .then((res) => res.text())
        .then((data) => {
          if (data.trim() === "success") {
            document.querySelector(`tr[data-id='${selectedId}']`)?.remove();
            closeModal("deleteModal");
            renderTableWithPagination();
            updateChartData(); 
          } else alert("Gagal menghapus data");
        })
        .catch(() => alert("Terjadi error koneksi"));
    });
  }


// ================================
// Edit Form Submit
// ================================
let isUpdating = false;

editForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  if (isUpdating) return;
  isUpdating = true;

  const formData = new FormData(editForm);
  const reportId = formData.get("report_id") || formData.get("id");
  const status = formData.get("status");
  const division = formData.get("division");
  const notes = formData.get("notes");


  try {
    const response = await fetch("../db/update_report.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      if (currentEditRow) {
        const oldDivision = currentEditRow.dataset.division;
        const newDivision = division;

        // ✅ Update dataset & tampilan row agar sinkron
        currentEditRow.dataset.status = status;
        currentEditRow.dataset.division = newDivision;

        // Update tampilan kolom status
        const statusCell = currentEditRow.querySelector(".status");
        if (statusCell) statusCell.textContent = status;

        // Update tampilan kolom division
        const divLabel =
          DIVISION_OPTIONS.find((o) => o.value === newDivision)?.label || "-";
        if (currentEditRow.cells[5]) currentEditRow.cells[5].innerText = divLabel;

        // Update teks tombol edit (opsional)
        console.log(`✅ Updated row #${reportId}: status=${status}, division=${newDivision}`);

        // Jika divisi berubah, lakukan sembunyikan sesuai role
        const currentRole = document.body.dataset.role;
        if (currentRole && currentRole !== newDivision && newDivision !== "") {
          currentEditRow.style.display = "none";
        }

        alert(data.message || "Laporan berhasil diperbarui.");
      }

      closeModal("editModal");
      renderTableWithPagination();
      updateChartData();
    }
    else {
          alert(data.message || "Gagal memperbarui data. Pastikan format input benar.");
        }
      } catch (err) {
        console.error("❌ Fetch error:", err);
        alert("Terjadi kesalahan koneksi dengan server.");
      } finally {
        isUpdating = false;
     }
 });



// ======================
// Detail Modal
// ======================
function attachRowDetailListeners() {
  if (!laporanTable) return;
  laporanTable.querySelectorAll("tbody tr").forEach((row) => {
    if (row.dataset.detailAttached) return;
    row.dataset.detailAttached = "1";
    row.addEventListener("click", (e) => {
      if (
        e.target.classList.contains("edit-btn") ||
        e.target.classList.contains("delete-btn")
      )
        return;

        selectedId = row.dataset.id;
        document.getElementById("detail-timestamp").textContent = row.dataset.timestamp || "-";
        document.getElementById("detail-nim").textContent = row.dataset.nim || "-";
        document.getElementById("detail-nama").textContent = row.dataset.nama || "-";
        document.getElementById("detail-report").textContent = row.dataset.report || "-";
        document.getElementById("detail-division").textContent = row.dataset.division || "-";
        document.getElementById("detail-status").textContent = row.dataset.status || "-";
        document.getElementById("detail-deskripsi").textContent = row.dataset.deskripsi || "-";

        fetch(`../db/get_report_notes.php?id=${selectedId}`)
          .then((res) => res.json())
          .then((notesData) => {
            const list = document.getElementById("detail-notes-list");
            list.innerHTML = "";
            if (!notesData || notesData.length === 0) {
              list.innerHTML = "<li>-</li>";
            } else {
              notesData.forEach((note) => {
                const li = document.createElement("li");
                li.innerHTML = `<strong>${new Date(note.created_at).toLocaleString()}</strong>: ${note.note}`;
                list.appendChild(li);
              });
            }
          })
          .catch(() => console.error("Gagal memuat catatan"));
        openModal("detailModal");
      });
    });
  }
  attachRowDetailListeners();
  const closeDetailBtn = document.getElementById("closeDetailBtn");
  if (closeDetailBtn) closeDetailBtn.addEventListener("click", () => closeModal("detailModal"));



  // ======================
  // Sort Timestamp
  // ======================
  const table = document.getElementById("laporanTable");
  const header = document.getElementById("timestampHeader");
  const icon = document.getElementById("sortIcon");
  let sortAscending = false;
  if (header) {
    header.addEventListener("click", () => {
      const tbody = table.querySelector("tbody");
      const rows = Array.from(tbody.querySelectorAll("tr"));
      sortAscending = !sortAscending;
      icon.className = sortAscending ? "fas fa-sort-up" : "fas fa-sort-down";
      rows.sort((a, b) => {
        const dateA = new Date(a.dataset.timestamp);
        const dateB = new Date(b.dataset.timestamp);
        return sortAscending ? dateA - dateB : dateB - dateA;
      });
      tbody.innerHTML = "";
      rows.forEach((row) => tbody.appendChild(row));
      attachRowButtons();
      attachRowDetailListeners();
      renderTableWithPagination();
      updateChartData(); // [REVISED] refresh chart (berdasarkan seluruh data)
    });
  }

  // ======================
  // Chart Report
  // ======================
  const ticketChartCtx = document.getElementById("ticketChart")?.getContext?.("2d");
  const trendChartCtx = document.getElementById("trendChart")?.getContext?.("2d");
  const reportTypeChartCtx = document.getElementById("reportTypeChart")?.getContext?.("2d");

  if (ticketChartCtx) {
    ticketChart = new Chart(ticketChartCtx, {
      type: "doughnut",
      data: {
        labels: ["Pending", "Assign", "Resolved"],
        datasets: [{ data: [0, 0, 0], backgroundColor: ["#f39c12", "#3498db", "#2ecc71"] }],
      },
      options: { responsive: true, plugins: { legend: { position: "bottom" } } },
    });
  }

  if (trendChartCtx) {
    trendChart = new Chart(trendChartCtx, {
      type: "line",
      data: { labels: [], datasets: [{ label: "Jumlah Laporan", data: [], borderWidth: 2, tension: 0.2 }] },
      options: { responsive: true, scales: { y: { beginAtZero: true, precision: 0 } } },
    });
  }

  if (reportTypeChartCtx) {
    reportTypeChart = new Chart(reportTypeChartCtx, {
      type: "bar",
      data: { labels: [], datasets: [{ label: "Jumlah Laporan per Jenis", data: [], backgroundColor: [] }] },
      options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } },
    });
  }

  function generateColors(n) {
    const palette = ["#4e79a7","#f28e2b","#e15759","#76b7b2","#59a14f","#edc949","#af7aa1","#ff9da7","#9c755f","#bab0ab"];
    return Array.from({ length: n }, (_, i) => palette[i % palette.length]);
  }

  // ======================
  // Update Chart Data 
  // ======================
  function updateChartData() {
    const rows = laporanTable ? Array.from(laporanTable.querySelectorAll("tbody tr")) : [];

    let pending = 0, assign = 0, resolved = 0;
    const trendMap = new Map();
    const reportCounts = {};

    rows.forEach((r) => {
      const s = (r.dataset.status || "").toLowerCase();
      const rawTimestamp = r.dataset.timestamp || "";
      let date = new Date(rawTimestamp);
      if (isNaN(date.getTime())) date = new Date(rawTimestamp.split(" ")[0]);

      if (s === "pending") pending++;
      else if (s === "assign") assign++;
      else if (s === "resolved" || s === "done") resolved++;

      if (!isNaN(date.getTime())) {
        const y = date.getFullYear();
        const m = date.getMonth() + 1;
        const d = date.getDate();
        let key = "";
        if (currentInterval === "daily") key = `${y}-${String(m).padStart(2,"0")}-${String(d).padStart(2,"0")}`;
        else if (currentInterval === "weekly") key = `${y}-${String(m).padStart(2,"0")} (Minggu ${Math.ceil(d/7)})`;
        else key = `${y}-${String(m).padStart(2,"0")}`;
        trendMap.set(key, (trendMap.get(key) || 0) + 1);
      }

      const rpt = (r.dataset.report || "Tidak Diketahui").trim();
      reportCounts[rpt] = (reportCounts[rpt] || 0) + 1;
    });

    if (ticketChart) {
      ticketChart.data.datasets[0].data = [pending, assign, resolved];
      ticketChart.update();
    }

    if (trendChart) {
      const arr = Array.from(trendMap.entries());
      arr.sort((a, b) => new Date(a[0]) - new Date(b[0]));
      trendChart.data.labels = arr.map((x) => x[0]);
      trendChart.data.datasets[0].data = arr.map((x) => x[1]);
      trendChart.update();
    }

    if (reportTypeChart) {
      const entries = Object.entries(reportCounts).sort((a, b) => b[1] - a[1]);
      const labels = entries.map((x) => x[0]);
      const data = entries.map((x) => x[1]);
      const colors = generateColors(labels.length);
      reportTypeChart.data.labels = labels;
      reportTypeChart.data.datasets[0].data = data;
      reportTypeChart.data.datasets[0].backgroundColor = colors;
      reportTypeChart.update();
    }
  }

  updateChartData();

  if (chartInterval) {
    chartInterval.addEventListener("change", () => {
      currentInterval = chartInterval.value;
      updateChartData();
    });
  }

  
  // ======================
  // Pagination & Filter
  // ======================
  const paginationContainer = document.getElementById("paginationControls");
  
  function renderTableWithPagination() {
  if (!laporanTable) return;
  const allRows = Array.from(laporanTable.querySelectorAll("tbody tr"));
  const visibleRows = allRows.filter((r) => r.dataset.hidden !== "true");

  const perPage = parseInt(entriesSelect?.value) || 10;
  const totalPages = Math.ceil(visibleRows.length / perPage) || 1;
  currentPage = Math.min(currentPage, totalPages);

  // Sembunyikan semua baris dulu
  allRows.forEach((r) => (r.style.display = "none"));

  // Tampilkan baris sesuai halaman aktif
  const start = (currentPage - 1) * perPage;
  const end = start + perPage;
  visibleRows.forEach((r, i) => {
    r.style.display = i >= start && i < end ? "" : "none";
  });

 
  // Pagination Buttons
  paginationContainer.innerHTML = "";

  if (totalPages > 1) {
    const wrapper = document.createElement("div");
    wrapper.className = "pagination-wrapper";

    // Tombol sebelumnya
    const prev = document.createElement("button");
    prev.textContent = "‹";
    prev.className = "pagination-btn";
    prev.disabled = currentPage === 1;
    prev.onclick = () => {
      currentPage--;
      renderTableWithPagination();
    };
    wrapper.appendChild(prev);

    // Nomor halaman
    const maxVisible = 5; // batas tampilan halaman agar tidak panjang
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    if (endPage - startPage < maxVisible - 1) {
      startPage = Math.max(1, endPage - maxVisible + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
      const btn = document.createElement("button");
      btn.textContent = i;
      btn.className = "pagination-number";
      if (i === currentPage) btn.classList.add("active");
      btn.onclick = () => {
        currentPage = i;
        renderTableWithPagination();
      };
      wrapper.appendChild(btn);
    }

    // Tombol berikutnya
    const next = document.createElement("button");
    next.textContent = "›";
    next.className = "pagination-btn";
    next.disabled = currentPage === totalPages;
    next.onclick = () => {
      currentPage++;
      renderTableWithPagination();
    };
    wrapper.appendChild(next);

    paginationContainer.appendChild(wrapper);
  }
}

  // ======================
  // Search Filter
  // ======================
  if (searchInput && laporanTable) {
    let searchTimeout = null;
    searchInput.addEventListener("input", () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        applyFilter(searchInput.value.trim());
      }, 180);
    });
  }

  // Inisialisasi Filter Keyword
  function applyFilter(keyword) {
    if (!laporanTable) return;
    const q = keyword.toLowerCase();
    laporanTable.querySelectorAll("tbody tr").forEach((r) => {
      const match = r.textContent.toLowerCase().includes(q);
      r.dataset.hidden = match ? "false" : "true";
    });
    currentPage = 1;
    renderTableWithPagination();
    updateChartData();
  }

  if (entriesSelect) entriesSelect.addEventListener("change", () => renderTableWithPagination());
  renderTableWithPagination();


// ======================
// Export Excel (All Data)
// ======================
const exportBtn = document.getElementById("exportExcelBtn");
if (exportBtn) {
  exportBtn.addEventListener("click", async () => {
    // Ambil SEMUA baris data (bukan hanya yang tampil)
    const rows = Array.from(laporanTable.querySelectorAll("tbody tr"));
    if (rows.length === 0) {
      alert("Tidak ada data untuk diekspor!");
      return;
    }

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Laporan Mahasiswa");

    // Header
    worksheet.addRow([
      "No", "Tanggal", "NIM", "Nama", "Jenis Laporan",
      "Divisi", "Status", "Deskripsi", "Catatan"
    ]);
    const headerRow = worksheet.getRow(1);
    headerRow.font = { bold: true, color: { argb: "FFFFFFFF" } };
    headerRow.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FF4F81BD" } };
    headerRow.alignment = { vertical: "middle", horizontal: "center" };
    headerRow.height = 20;

    // Isi data
    let no = 1;
    for (const tr of rows) {
      const id = tr.dataset.id;
      const timestamp = tr.dataset.timestamp || "-";
      const nim = tr.dataset.nim || "-";
      const nama = tr.dataset.nama || "-";
      const report = tr.dataset.report || "-";
      const division = tr.dataset.division || "-";
      const status = tr.dataset.status || "-";
      const deskripsi = tr.dataset.deskripsi || "-";

      let notesText = "-";
    try {
      const resp = await fetch(`../db/get_report_notes.php?id=${id}`);
      if (resp.ok) {
        const notes = await resp.json();
        if (Array.isArray(notes) && notes.length > 0) {
          notesText = notes.map(
            (n) => `${new Date(n.created_at).toLocaleString("id-ID")} - ${n.note}`
          ).join("\n");
        }
      }
    } catch (err) {
      console.warn(`Gagal load notes untuk laporan ID ${id}:`, err);
  }

      const row = worksheet.addRow([
        no++, timestamp, nim, nama, report, division, status, deskripsi, notesText,
      ]);

      row.eachCell((cell, idx) => {
        cell.alignment = {
          vertical: "top",
          horizontal: idx <= 2 ? "center" : "left",
          wrapText: true,
        };
        cell.border = {
          top: { style: "thin" },
          left: { style: "thin" },
          bottom: { style: "thin" },
          right: { style: "thin" },
        };
      });
    }

    worksheet.columns.forEach((col, i) => {
      col.width = [5, 20, 15, 20, 25, 15, 15, 40, 50][i];
    });

    worksheet.views = [{ state: "frozen", ySplit: 1, activeCell: "A2" }];
    worksheet.autoFilter = { from: "A1", to: "I1" };

    try {
      const buffer = await workbook.xlsx.writeBuffer();
      saveAs(
        new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" }),
        `Laporan_Student_Engagement_${new Date().toISOString().slice(0, 10)}.xlsx`
      );
    } catch (err) {
      console.error("Gagal ekspor Excel:", err);
      alert("Terjadi kesalahan saat menyimpan file Excel.");
    }
  });
}
});