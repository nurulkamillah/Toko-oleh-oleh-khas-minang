
const daftarKeranjang = document.getElementById("daftarKeranjang");

function ambilKeranjang() {

    return JSON.parse(
        localStorage.getItem("siruwaKeranjang")
    ) || [];

}

function simpanKeranjang(data) {

    localStorage.setItem(
        "siruwaKeranjang",
        JSON.stringify(data)
    );

}

function formatRupiah(angka) {

    return "Rp" + angka.toLocaleString("id-ID");

}

function tampilkanKeranjang() {

    const keranjang = ambilKeranjang();

    daftarKeranjang.innerHTML = "";

    let totalItem = 0;
    let subtotal = 0;

    if (keranjang.length === 0) {

        daftarKeranjang.innerHTML = `
            <div class="empty-cart">
                <h3>Keranjang Masih Kosong</h3>
                <p>Silakan pilih produk favoritmu terlebih dahulu.</p>
                <a href="Beranda.html#produk" class="primary-btn">
                    Mulai Belanja
                </a>
            </div>
        `;

    }

    keranjang.forEach(function (item, index) {

        const jumlah = Number(item.jumlah);
        const harga = Number(item.harga);

        const totalHarga = harga * jumlah;

        totalItem += jumlah;
        subtotal += totalHarga;

        const elemen = document.createElement("div");

        elemen.className = "cart-item";

        const gambar = document.createElement("img");
        gambar.src = item.gambar;
        gambar.alt = item.nama;

        const info = document.createElement("div");
        info.className = "cart-info";

        const nama = document.createElement("h3");
        nama.textContent = item.nama;

        const hargaProduk = document.createElement("p");
        hargaProduk.className = "cart-price";
        hargaProduk.textContent = formatRupiah(harga);

        const quantity = document.createElement("div");
        quantity.className = "quantity";

        const kurang = document.createElement("button");
        kurang.type = "button";
        kurang.textContent = "-";
        kurang.setAttribute("aria-label", "Kurangi jumlah");

        const jumlahText = document.createElement("span");
        jumlahText.textContent = jumlah;

        const tambah = document.createElement("button");
        tambah.type = "button";
        tambah.textContent = "+";
        tambah.setAttribute("aria-label", "Tambah jumlah");

        const total = document.createElement("p");
        total.className = "cart-price";
        total.textContent = "Total: " + formatRupiah(totalHarga);

        const hapus = document.createElement("button");
        hapus.className = "remove-btn";
        hapus.type = "button";
        hapus.textContent = "Hapus";

        kurang.addEventListener("click", function () {

            ubahJumlah(index, -1);

        });

        tambah.addEventListener("click", function () {

            ubahJumlah(index, 1);

        });

        hapus.addEventListener("click", function () {

            hapusProduk(index);

        });

        quantity.append(kurang, jumlahText, tambah);

        info.append(
            nama,
            hargaProduk,
            quantity,
            total
        );

        elemen.append(
            gambar,
            info,
            hapus
        );

        daftarKeranjang.appendChild(elemen);

    });

    document.getElementById("totalItem").textContent =
        totalItem + " item";

    document.getElementById("subtotal").textContent =
        formatRupiah(subtotal);

    document.getElementById("totalPembayaran").textContent =
        formatRupiah(subtotal);

    document.getElementById("checkoutBtn").disabled =
        keranjang.length === 0;

}

function ubahJumlah(index, perubahan) {

    const keranjang = ambilKeranjang();

    if (!keranjang[index]) return;

    keranjang[index].jumlah += perubahan;

    if (keranjang[index].jumlah <= 0) {

        keranjang.splice(index, 1);

    }

    simpanKeranjang(keranjang);

    tampilkanKeranjang();

}

function hapusProduk(index) {

    const keranjang = ambilKeranjang();

    keranjang.splice(index, 1);

    simpanKeranjang(keranjang);

    tampilkanKeranjang();

}

document.getElementById("checkoutBtn").addEventListener("click", function () {

    const keranjang = ambilKeranjang();

    if (keranjang.length === 0) {

        alert("Keranjang masih kosong.");
        return;

    }

    window.location.href = "detail-pembayaran.html";

});

// Tampilkan keranjang saat halaman dibuka

tampilkanKeranjang();
