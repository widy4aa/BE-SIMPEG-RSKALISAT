# BACKEND DEVELOPER — [NAMA KAMU]

> Proyek: **SIMPEG RS Kalisat** — REST API Sistem Informasi Manajemen Kepegawaian
> Stack: **Laravel 13 (PHP 8.3) · MySQL 8.0 · JWT (custom HS256) · Nginx Reverse Proxy · Cloudflare Tunnel (Zero Trust) · Podman**

---

## 5.1. Pemrograman Backend (KMU1018)

**5.1.1.** Pada tahap pengembangan back-end, saya bertanggung jawab dalam membangun dan mengelola logika bisnis aplikasi menggunakan **Laravel 13** sebagai framework utama dengan bahasa **PHP 8.3**. Seluruh kode sumber dikelola melalui repositori **GitHub** untuk mendukung kolaborasi tim dan pengelolaan versi aplikasi. Selain itu, saya menerapkan penggunaan *environment variables* melalui file `.env` untuk menyimpan konfigurasi aplikasi seperti kredensial basis data MySQL, *secret key* JWT (`JWT_SECRET`), *time-to-live* token (`JWT_TTL`), serta pengaturan koneksi server. Pendekatan ini membantu menjaga keamanan data sensitif dan memudahkan pengelolaan konfigurasi pada berbagai lingkungan pengembangan maupun produksi. 📄 *env.png*

**5.1.2.** Dalam proses pengembangan basis data, saya mengimplementasikan struktur tabel berdasarkan **Entity Relationship Diagram (ERD)** yang telah dirancang sesuai kebutuhan sistem kepegawaian. Basis data menggunakan **MySQL 8.0** sebagai DBMS, sedangkan **Eloquent ORM** bawaan Laravel digunakan untuk mengelola skema, migrasi, dan interaksi data dari aplikasi. Relasi antar tabel seperti *one-to-many* (misalnya satu pegawai memiliki banyak riwayat karir, diklat, dan data keluarga) dirancang untuk mendukung kebutuhan bisnis aplikasi. Proses **migration** Laravel digunakan untuk memastikan perubahan struktur basis data dapat diterapkan secara terkontrol, konsisten, dan dapat dilacak (*version-controlled*) pada setiap lingkungan pengembangan. 📄 *migration.png*

**5.1.3.** Pada pengembangan fitur back-end, saya menerapkan arsitektur berlapis (*layered architecture*) yang memisahkan komponen **Controller**, **Service**, dan **Repository** untuk menjaga keteraturan struktur kode dan mempermudah proses pemeliharaan aplikasi. *Controller* berfungsi sebagai penerima *request* dan pengatur alur endpoint, *Service layer* menangani logika bisnis aplikasi, sedangkan *Repository* bertanggung jawab terhadap akses data menggunakan Eloquent ORM. Selain itu, **middleware** digunakan untuk menangani proses autentikasi JWT, sementara validasi *request* dikelola melalui **Form Request** Laravel agar setiap data yang masuk terverifikasi sebelum diproses. Pendekatan ini membantu meningkatkan keterbacaan kode serta memudahkan pengembangan fitur baru di masa mendatang. 📄 *layer.png*

**5.1.4.** Untuk mengamankan akses ke layanan API, saya mengimplementasikan mekanisme autentikasi dan otorisasi menggunakan **JSON Web Token (JWT)** yang dibangun secara mandiri (*custom implementation*) tanpa bergantung pada pustaka pihak ketiga. Token dihasilkan menggunakan algoritma **HS256** melalui `hash_hmac`, dengan proses *encoding* header dan *payload* memakai *base64url* serta penandatanganan (*signature*) yang diverifikasi pada setiap permintaan. Setelah pengguna berhasil melakukan proses *login*, sistem menghasilkan token berisi *claims* identitas pengguna (`iss`, `iat`, `nbf`, `exp`) yang digunakan sebagai kredensial pada setiap permintaan ke endpoint yang dilindungi. Validasi token dilakukan melalui *middleware* Laravel yang memeriksa keabsahan *signature* serta masa berlaku token (`exp`/`nbf`) sebelum *request* diteruskan ke proses bisnis aplikasi. 📄 *jwt.png*

---

## 5.2. Software Maintenance (KFU1304)

**5.2.1.** Pada tahap pemeliharaan perangkat lunak, saya melakukan **monitoring dan troubleshooting** untuk memastikan sistem back-end tetap stabil dan responsif selama masa operasional. Pemantauan dilakukan melalui mekanisme *logging* Laravel yang mencatat setiap aktivitas dan kesalahan (*error*) yang terjadi pada aplikasi, sehingga potensi permasalahan dapat diidentifikasi dan ditindaklanjuti lebih cepat. Selain itu, penanganan *error* terpusat diterapkan melalui *exception handler* agar setiap kegagalan *request* menghasilkan respons yang konsisten dan tidak membocorkan informasi sensitif kepada pengguna. 📄 *monitoring.png*

**5.2.2.** Selama sistem berjalan, saya melakukan **update, patching, dan bug fixing** pada kode maupun struktur basis data yang sudah berjalan. Perbaikan bug dilakukan secara bertahap berdasarkan temuan di lapangan maupun masukan dari pengguna, kemudian setiap perubahan pada struktur basis data diterapkan melalui *migration* baru agar tetap terkontrol dan tidak merusak data yang sudah ada. Proses ini memastikan aplikasi dapat terus berkembang dan diperbaiki tanpa mengganggu layanan yang sedang berjalan. 📄 *bugfixing.png*

**5.2.3.** Sebagai bentuk penjaminan kualitas, saya menyusun **pengujian otomatis (automated testing)** menggunakan **PHPUnit** untuk memverifikasi bahwa setiap komponen back-end tetap berfungsi dengan benar setelah dilakukan perubahan (*regression testing*). Pengujian mencakup *Feature Test* yang mensimulasikan permintaan HTTP ke endpoint API serta *Unit Test* untuk memvalidasi logika bisnis pada *service layer*. Dengan adanya pengujian ini, proses *maintenance* menjadi lebih aman karena setiap perbaikan dapat diverifikasi agar tidak menimbulkan kesalahan baru pada bagian sistem yang lain. 📄 *testing.png*

**5.2.4.** Untuk mendukung kestabilan dan kemudahan pemeliharaan, saya melakukan **optimasi performa sistem** serta menyusun **dokumentasi maintenance**. Optimasi dilakukan antara lain melalui perbaikan *query* basis data (menghindari *N+1 query* dengan *eager loading* Eloquent) serta penerapan *pagination* dan *filtering* pada endpoint yang mengembalikan data dalam jumlah besar. Seluruh aktivitas pemeliharaan didokumentasikan sebagai laporan *maintenance* yang mencatat perubahan, perbaikan, serta hasil optimasi yang dilakukan, sehingga proses pengelolaan sistem tetap terpantau dan berkelanjutan. 📄 *optimasi.png*

**5.2.5.** Untuk menjaga konsistensi lingkungan serta kemudahan pemeliharaan dan *deployment* sistem, saya menerapkan strategi *containerization* menggunakan **Podman** sebagai *container runtime* (bersifat *rootless* dan *daemonless* sehingga lebih aman untuk lingkungan produksi). Konfigurasi didefinisikan melalui `docker-compose.yml` yang dijalankan menggunakan `podman-compose`, mencakup layanan aplikasi PHP-FPM (Laravel), basis data MySQL, serta **Adminer** sebagai antarmuka GUI basis data. Proses *deployment* dan pembaruan (*update/patching*) diotomatisasi melalui skrip `deploy.sh` yang mendeteksi *tool compose*, membangun ulang *container*, menjalankan `composer install`, serta melakukan pembersihan *image* yang tidak terpakai. Pendekatan ini memastikan lingkungan eksekusi aplikasi tetap konsisten pada berbagai perangkat maupun server, sehingga meminimalkan permasalahan akibat perbedaan konfigurasi antara lingkungan pengembangan dan produksi. 📄 *podman.png*

**5.2.6.** Untuk menjaga **ketersediaan (availability) dan keamanan layanan** selama masa operasional, saya mengonfigurasi **Nginx sebagai reverse proxy** yang berada di depan aplikasi PHP-FPM dan menangani *SSL termination*. Akses ke sistem diarahkan melalui **Cloudflare Tunnel** dengan pendekatan **Zero Trust**, sehingga layanan dapat diakses dari internet **tanpa perlu membuka (expose) port publik** pada server secara langsung. Nginx dikonfigurasi untuk mempercayai *header* `CF-Connecting-IP` dari Cloudflare (`set_real_ip_from` dan `real_ip_header`) agar alamat IP asli klien tetap dapat dikenali dan dicatat pada *log* di balik *tunnel* — hal ini penting untuk kebutuhan *monitoring* dan *troubleshooting*. Kombinasi ini meningkatkan keamanan, mengurangi permukaan serangan (*attack surface*), serta menjaga sistem tetap stabil dan responsif saat diakses pengguna. 📄 *nginx_cloudflare.png*

---

## 5.3. Pemrograman Framework Lanjut (KMU1007)

**5.3.1.** Dalam pengembangan sistem, saya mengembangkan back-end menggunakan **framework modern Laravel 13** dengan memanfaatkan fitur-fitur lanjutan yang disediakan, seperti *Service Container* dan *Dependency Injection*, *Eloquent ORM*, *Middleware*, *Form Request Validation*, serta *Routing* modular. Pemanfaatan fitur bawaan framework ini memungkinkan pengembangan sistem yang terstruktur, mudah dikembangkan, serta selaras dengan praktik terbaik (*best practice*) dalam pengembangan aplikasi berbasis Laravel. 📄 *laravel.png*

**5.3.2.** Saya melakukan **pengelolaan basis data (SQL) dan optimasi query** untuk memastikan performa aplikasi tetap efisien. Basis data relasional **MySQL** dikelola melalui Eloquent ORM dengan penerapan relasi antar model, *indexing* pada kolom yang sering digunakan untuk pencarian, serta optimasi *query* menggunakan *eager loading* guna mengurangi jumlah *query* yang dieksekusi. Selain itu, fitur pencarian, *filtering*, dan *pagination* diterapkan pada endpoint agar pengambilan data berskala besar tetap ringan dan cepat. 📄 *query.png*

**5.3.3.** Untuk menjaga **scalability dan performa aplikasi di level kode**, saya memanfaatkan fitur lanjutan Laravel dalam menangani beban permintaan, seperti *pagination* dan *filtering* pada endpoint yang mengembalikan data berskala besar, *eager loading* untuk menghindari *N+1 query*, serta pemisahan logika bisnis ke dalam *service layer* agar aplikasi tetap modular dan mudah dikembangkan seiring bertambahnya kebutuhan. Struktur kode yang rapi dan penerapan praktik terbaik Laravel ini memastikan aplikasi tetap efisien dan siap dikembangkan untuk menangani pertumbuhan data maupun pengguna di masa mendatang. 📄 *scalability.png*

---

## 5.4. Capstone Project (KSU1603)

**5.4.1.** Dalam kegiatan Capstone Project ini, saya mengikuti proses *onboarding* bersama Dosen FASILKOM dan Mitra yaitu Satuan Pengawas Internal selaku pihak yang memahami proses di lapangan. Melalui kegiatan *onboarding* ini, saya memperoleh pemahaman mendalam mengenai alur kerja, kebutuhan pengguna, serta konteks sistem yang akan dikembangkan dari sisi back-end. Kegiatan ini menjadi fondasi awal yang penting dalam memastikan arah pengembangan API dan layanan selaras dengan kebutuhan nyata di lapangan. 📷 *Onboarding.jpg* 📷 *Onboarding*

**5.4.2.** Melaksanakan bimbingan secara rutin dengan Dosen Pembimbing Lapangan (DPL) untuk mendapatkan arahan, masukan, serta memastikan bahwa rancangan arsitektur dan implementasi back-end yang dikembangkan telah sesuai dengan kebutuhan lapangan. Proses bimbingan ini berlangsung secara berkelanjutan sepanjang pelaksanaan Capstone Project sebagai bentuk kendali mutu terhadap hasil kerja yang dihasilkan. 📷 *Bimbingan*

**5.4.3.** Menyusun logbook kegiatan sebagai bentuk dokumentasi terstruktur atas seluruh aktivitas yang dilakukan selama pelaksanaan Capstone Project. Melalui logbook ini, saya mencatat setiap progres pengembangan sistem, pembelajaran yang diperoleh di setiap tahapan, serta arahan dan masukan dari Dosen Pembimbing Lapangan (DPL) maupun pihak mitra, sehingga seluruh proses kerja dapat terpantau dan terdokumentasi secara berkelanjutan. 📘 *Logbook Capstone Project*

---

## 5.5. Publikasi Produk (KSU1604)

**5.5.1.** Menyiapkan **dokumentasi API** yang lengkap dan mudah dipahami sebagai panduan penggunaan serta integrasi layanan back-end. Dokumentasi disusun dalam bentuk **Postman Collection** (`SIMPEG-RSKALISAT.postman_collection.json`) yang memuat seluruh endpoint yang tersedia beserta metode HTTP, parameter *request*, *request body*, contoh *response*, serta kebutuhan autentikasi (Bearer Token JWT) pada setiap layanan. Dengan adanya dokumentasi API yang terpusat dan interaktif ini, proses pengujian maupun integrasi dengan aplikasi front-end dapat dilakukan dengan lebih mudah dan efisien. 📄 *postman.png*

**5.5.2.** Menyiapkan **manual book** yang bertujuan mempermudah pengguna dalam memahami fitur serta cara menggunakan sistem. Dokumentasi ini disusun sebagai panduan penggunaan aplikasi yang lengkap dan mudah dipahami. 📕 *manual book spi.pdf*

**5.5.3.** Melakukan penyusunan **HKI** yang bertujuan untuk melindungi hasil karya atau ciptaan agar tidak disalahgunakan, ditiru, atau diklaim oleh pihak lain tanpa izin, serta memberikan kepastian hukum bagi penciptanya. 📷 *8. HKI*

**5.5.4.** Menyusun dan menyampaikan **presentasi** yang jelas dan menarik tentang produk untuk penyampaian pada saat presentasi expo capstone project. 🔗 *PPT*

---

## 5.6. PKL (KSU1701)

**5.6.1.** Dalam kegiatan Praktek Kerja Lapangan ini, saya mengikuti sesi *onboarding* yang melibatkan Dosen FASILKOM serta pihak Mitra dari Satuan Pengawas Internal yang memiliki pemahaman langsung terhadap proses operasional di lapangan. Kegiatan ini memberikan pemahaman awal mengenai alur bisnis, kebutuhan pengguna, serta konteks sistem yang akan dikembangkan dari sisi back-end. Tahap *onboarding* menjadi fondasi penting karena membantu saya memahami bagaimana logika sistem dan layanan API perlu dirancang agar sesuai dengan kebutuhan nyata di lingkungan operasional. 📷 *Onboarding.jpg* 📷 *Onboarding*

**5.6.2.** Melaksanakan kegiatan bimbingan secara berkala dengan Dosen Pembimbing Lapangan (DPL) minimal satu kali setiap dua minggu. Pada setiap sesi, saya memaparkan perkembangan implementasi back-end, termasuk pembuatan API, pengelolaan database, serta integrasi service menggunakan Laravel dan Eloquent ORM. Selain itu, saya menerima masukan terkait perbaikan struktur kode, desain arsitektur layanan, serta keamanan API seperti implementasi JWT dan pengelolaan middleware. Kegiatan ini berperan penting dalam menjaga kualitas pengembangan agar tetap sesuai dengan standar teknis dan arahan proyek. 📷 *Bimbingan*

**5.6.3.** Menyusun logbook harian yang berisi dokumentasi aktivitas pengembangan back-end selama pelaksanaan PKL. Logbook tersebut mencatat progres pembuatan API, proses debugging, pengujian endpoint, integrasi database MySQL, hingga optimasi logika bisnis pada *service layer*. Dokumentasi ini membantu dalam memantau perkembangan pekerjaan secara konsisten serta menjadi bukti pelaksanaan aktivitas harian selama pengembangan sistem. 📘 *Logbook PKL*
