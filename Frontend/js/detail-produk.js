
document.addEventListener("DOMContentLoaded", function () {

    // DATA PRODUK SIRUWA
    const daftarProduk = {
        "rendang": {
            nama: "Rendang Minang",
            harga: 85000,
            gambar: "images/Rendang.jpeg",
            deskripsi: "Rendang khas Minangkabau dengan bumbu rempah pilihan dan cita rasa autentik."
        },

        "kue-sapik": {
            nama: "Kue Sapik",
            harga: 25000,
            gambar: "images/kuesapik.jpeg",
            deskripsi: "Kue tradisional Minangkabau dengan tekstur renyah dan rasa manis yang khas."
        },

        "karak-kaliang": {
            nama: "Karak Kaliang",
            harga: 35000,
            gambar: "images/karakkaliang.jpeg",
            deskripsi: "Camilan khas Sumatera Barat yang gurih dan renyah."
        },

        "kerupuk-jangek": {
            nama: "Kerupuk Jangek",
            harga: 55000,
            gambar: "images/kerupukjangek.jpeg",
            deskripsi: "Kerupuk kulit khas Minangkabau dengan tekstur renyah."
        },

        "pisang-salai": {
            nama: "Pisang Salai",
            harga: 35000,
            gambar: "images/Pisangsalai.jpeg",
            deskripsi: "Olahan pisang tradisional dengan cita rasa manis dan aroma khas."
        }
    };

    // AMBIL SLUG DARI URL
    const parameter = new URLSearchParams(window.location.search);
    const slug = parameter.get("produk");
    const produk = daftarProduk[slug];

    // AMBIL ELEMEN HTML
    const namaProduk = document.getElementById("nama-produk");
    const hargaProduk = document.getElementById("harga-produk");
    const gambarProduk = document.getElementById("gambar-produk");
    const deskripsiProduk = document.getElementById("deskripsi-produk");

    const inputJumlah = document.getElementById("jumlah");
    const tombolKurang = document.getElementById("kurang");
    const tombolTambah = document.getElementById("tambah");
    const tombolKeranjang = document.getElementById("tambahKeranjang");

    // PERIKSA PRODUK
    if (!produk) {
        alert("Produk tidak ditemukan!");
        window.location.href = "Beranda.php#produk";
        return;
    }

    // TAMPILKAN PRODUK
    namaProduk.textContent = produk.nama;

    hargaProduk.textContent =
        "Rp" + produk.harga.toLocaleString("id-ID");

    deskripsiProduk.textContent = produk.deskripsi;

    gambarProduk.src = produk.gambar;
    gambarProduk.alt = produk.nama;

    gambarProduk.onerror = function () {
        console.error("Gambar tidak ditemukan:", produk.gambar);
        gambarProduk.alt = "Gambar produk tidak tersedia";
    };

    // KONTROL JUMLAH
    tombolKurang.addEventListener("click", function () {
        let jumlah = parseInt(inputJumlah.value) || 1;

        if (jumlah > 1) {
            inputJumlah.value = jumlah - 1;
        }
    });

    tombolTambah.addEventListener("click", function () {
        let jumlah = parseInt(inputJumlah.value) || 1;
        inputJumlah.value = jumlah + 1;
    });

    inputJumlah.addEventListener("change", function () {
        let jumlah = parseInt(inputJumlah.value);

        if (isNaN(jumlah) || jumlah < 1) {
            inputJumlah.value = 1;
        }
    });

    // TAMBAH KE KERANJANG DATABASE
    tombolKeranjang.addEventListener("click", function () {

        const jumlah = parseInt(inputJumlah.value);

        if (!Number.isInteger(jumlah) || jumlah < 1) {
            alert("Jumlah produk tidak valid.");
            inputJumlah.value = 1;
            return;
        }

        // BUAT FORM POST
        const form = document.createElement("form");

        form.method = "POST";
        form.action = "../Backend/cart/tambah_keranjang.php";

        // KIRIM SLUG PRODUK
        const inputSlug = document.createElement("input");

        inputSlug.type = "hidden";
        inputSlug.name = "slug";
        inputSlug.value = slug;

        // KIRIM JUMLAH PRODUK
        const inputQuantity = document.createElement("input");

        inputQuantity.type = "hidden";
        inputQuantity.name = "quantity";
        inputQuantity.value = jumlah;

        form.appendChild(inputSlug);
        form.appendChild(inputQuantity);

        document.body.appendChild(form);

        // KIRIM KE BACKEND
        form.submit();

    });

});
