@extends('layouts.mobile.app')
@section('content')
    {{-- <style>
        :root {
            --bg-body: #dff9fb;
            --bg-nav: #ffffff;
            --color-nav: #32745e;
            --color-nav-active: #58907D;
            --bg-indicator: #32745e;
            --color-nav-hover: #3ab58c;
        }
    </style> --}}
    <style>
        .webcam-capture {
            display: inline-block;
            width: 100% !important;
            margin: 0 !important;
            margin-top: 30px !important;
            margin-bottom: 60px !important;
            padding: 10px !important;
            height: calc(100vh - 120px) !important;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }

        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            border-radius: 15px;
            object-fit: cover;
        }

        #map {
            height: 120px;
            width: 50%;
            position: absolute;
            top: 55px;
            left: 20px;
            z-index: 10;
            opacity: 0.8;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        canvas {
            position: absolute;
            border-radius: 0;
            box-shadow: none;
        }

        #facedetection {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            height: 100%;
            margin: 0 !important;
            /* Menghapus margin */
            padding: 0 !important;
            /* Menghapus padding */
            width: 100% !important;
            /* Memastikan lebar penuh */
        }

        /* Tambahkan style untuk indikator loading maps */
        #map-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 5px;
        }

        /* Perbaikan untuk posisi content-section */
        #header-section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        #content-section {
            margin-top: 45px;
            padding: 0 !important;
            /* Menghapus padding */
            position: relative;
            z-index: 1;
            height: calc(100vh - 45px);
            overflow: hidden;
        }

        /* Style untuk tombol scan */
        .scan-buttons {
            position: absolute;
            bottom: 40px;
            left: 0;
            right: 0;
            z-index: 20;
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 0 10px;
        }

        .scan-button {
            height: 45px !important;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            width: 42%;
        }

        .scan-button ion-icon {
            margin-right: 5px;
        }

        /* Style untuk jam digital */
        .jam-digital-malasngoding {
            background-color: rgba(39, 39, 39, 0.7);
            position: absolute;
            top: 55px;
            /* Di bawah header */
            right: 15px;
            /* Menambah margin kanan */
            z-index: 20;
            width: 150px;
            border-radius: 10px;
            padding: 5px;
            backdrop-filter: blur(5px);
        }

        .jam-digital-malasngoding p {
            color: #fff;
            font-size: 16px;
            text-align: left;
            margin-top: 0;
            margin-bottom: 0;
        }

        /* Style modern untuk box deteksi wajah */
        .face-detection-box {
            border: 2px solid #4CAF50;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
            transition: all 0.3s ease;
        }

        .face-detection-box.unknown {
            border-color: #F44336;
            box-shadow: 0 0 10px rgba(244, 67, 54, 0.5);
        }

        .face-detection-label {
            background-color: rgba(76, 175, 80, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .face-detection-label.unknown {
            background-color: rgba(244, 67, 54, 0.8);
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="javascript:;" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">E-Presensi</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <div class="row" style="margin-top: 0; height: 100%;">
            <div class="col" id="facedetection">
                <div class="webcam-capture"></div>
                <div id="map">
                    <div id="map-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="mt-2">Memuat peta...</div>
                    </div>
                </div>
                <div class="jam-digital-malasngoding">
                    <p>{{ DateToIndo(date('Y-m-d')) }}</p>
                    <p id="jam"></p>
                    <p>{{ $jam_kerja->nama_jam_kerja }} </p>
                    <p style="display: flex; justify-content:space-between">
                        <span> Masuk</span>
                        <span>{{ date('H:i', strtotime($jam_kerja->jam_masuk)) }}</span>
                    </p>
                    <p style="display: flex; justify-content:space-between">
                        <span> Pulang</span>
                        <span>{{ date('H:i', strtotime($jam_kerja->jam_pulang)) }}</span>
                    </p>
                </div>
                <div class="scan-buttons">
                    <button class="btn btn-success bg-primary scan-button" id="absenmasuk" statuspresensi="masuk">
                        <ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon>
                        <span style="font-size:14px">Masuk</span>
                    </button>
                    <button class="btn btn-danger scan-button" id="absenpulang" statuspresensi="pulang">
                        <ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon>
                        <span style="font-size:14px">Pulang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <audio id="notifikasi_radius">
        <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_mulaiabsen">
        <source src="{{ asset('assets/sound/mulaiabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_akhirabsen">
        <source src="{{ asset('assets/sound/akhirabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_sudahabsen">
        <source src="{{ asset('assets/sound/sudahabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenmasuk">
        <source src="{{ asset('assets/sound/absenmasuk.wav') }}" type="audio/mpeg">
    </audio>


    <!--Pulang-->
    <audio id="notifikasi_sudahabsenpulang">
        <source src="{{ asset('assets/sound/sudahabsenpulang.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenpulang">
        <source src="{{ asset('assets/sound/absenpulang.mp3') }}" type="audio/mpeg">
    </audio>
@endsection
@push('myscript')
    <script type="text/javascript">
        window.onload = function() {
            jam();
        }

        function jam() {
            var e = document.getElementById('jam'),
                d = new Date(),
                h, m, s;
            h = d.getHours();
            m = set(d.getMinutes());
            s = set(d.getSeconds());

            e.innerHTML = h + ':' + m + ':' + s;

            setTimeout('jam()', 1000);
        }

        function set(e) {
            e = e < 10 ? '0' + e : e;
            return e;
        }
    </script>
    <script>
        $(function() {
            let lokasi;
            let lokasi_user;
            let notifikasi_radius = document.getElementById('notifikasi_radius');
            let notifikasi_mulaiabsen = document.getElementById('notifikasi_mulaiabsen');
            let notifikasi_akhirabsen = document.getElementById('notifikasi_akhirabsen');
            let notifikasi_sudahabsen = document.getElementById('notifikasi_sudahabsen');
            let notifikasi_absenmasuk = document.getElementById('notifikasi_absenmasuk');

            let notifikasi_sudahabsenpulang = document.getElementById('notifikasi_sudahabsenpulang');
            let notifikasi_absenpulang = document.getElementById('notifikasi_absenpulang');

            let faceRecognitionDetected = 0; // Inisialisasi variabel face recognition detected
            let faceRecognition = "{{ $general_setting->face_recognition }}";

            // Deteksi perangkat mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                .userAgent);

            // Fungsi untuk inisialisasi webcam
            function initWebcam() {
                // Inisialisasi webcam dengan pengaturan yang sesuai
                Webcam.set({
                    height: isMobile ? 360 : 480,
                    width: isMobile ? 480 : 640,
                    image_format: 'jpeg',
                    jpeg_quality: isMobile ? 70 : 80,
                    fps: isMobile ? 15 : 20
                });

                Webcam.attach('.webcam-capture');

                // Tambahkan event listener untuk memastikan webcam berjalan setelah refresh
                Webcam.on('load', function() {
                    console.log('Webcam loaded successfully');
                });

                Webcam.on('error', function(err) {
                    console.error('Webcam error:', err);
                    // Coba inisialisasi ulang webcam jika terjadi error
                    setTimeout(initWebcam, 1000);
                });
            }

            // Inisialisasi webcam
            initWebcam();

            // Tambahkan event listener untuk visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    // Jika halaman menjadi visible, cek apakah webcam perlu diinisialisasi ulang
                    if (!Webcam.isInitialized()) {
                        console.log('Reinitializing webcam after visibility change');
                        initWebcam();
                    }
                }
            });

            // Tampilkan Map
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
            }

            function successCallback(position) {
                try {
                    var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
                    var lokasi_kantor = "{{ $lokasi_kantor->lokasi_cabang }}";
                    lokasi = position.coords.latitude + "," + position.coords.longitude;
                    var lok = lokasi_kantor.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];
                    console.log(position.coords.latitude + "," + position.coords.longitude);
                    var radius = "{{ $lokasi_kantor->radius_cabang }}";

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
                    var circle = L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);

                    // Sembunyikan indikator loading setelah peta dimuat
                    document.getElementById('map-loading').style.display = 'none';

                    // Pastikan peta diperbarui setelah dimuat
                    setTimeout(function() {
                        map.invalidateSize();
                    }, 500);
                } catch (error) {
                    console.error("Error initializing map:", error);
                    document.getElementById('map-loading').style.display = 'none';
                }
            }

            function errorCallback() {
                console.error("Error getting geolocation");
                document.getElementById('map-loading').style.display = 'none';
            }

            if (faceRecognition == 1) {
                // Tambahkan indikator loading dengan styling yang lebih baik
                const loadingIndicator = document.createElement('div');
                loadingIndicator.id = 'face-recognition-loading';
                loadingIndicator.innerHTML = `
                    <div class="spinner-border text-light" role="status">
                        <span class="sr-only">Memuat pengenalan wajah...</span>
                    </div>
                    <div class="mt-2 text-light">Memuat model pengenalan wajah...</div>
                `;
                loadingIndicator.style.position = 'absolute';
                loadingIndicator.style.top = '50%';
                loadingIndicator.style.left = '50%';
                loadingIndicator.style.transform = 'translate(-50%, -50%)';
                loadingIndicator.style.zIndex = '1000';
                loadingIndicator.style.textAlign = 'center';
                document.getElementById('facedetection').appendChild(loadingIndicator);

                // Preload model di background
                const modelLoadingPromise = Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                ]);

                // Mulai pengenalan wajah setelah model dimuat
                modelLoadingPromise.then(() => {
                    document.getElementById('face-recognition-loading').remove();
                    startFaceRecognition();
                }).catch(err => {
                    console.error("Error loading models:", err);
                    document.getElementById('face-recognition-loading').remove();
                    // Coba muat ulang model jika terjadi error
                    setTimeout(() => {
                        console.log('Retrying to load face recognition models');
                        modelLoadingPromise.then(() => {
                            startFaceRecognition();
                        });
                    }, 2000);
                });

                async function getLabeledFaceDescriptions() {
                    const labels = [
                        "{{ $karyawan->nik }}-{{ getNamaDepan(strtolower($karyawan->nama_karyawan)) }}"
                    ];
                    let namakaryawan;
                    let jmlwajah = "{{ $wajah == 0 ? 1 : $wajah }}";

                    // Tambahkan indikator loading untuk memuat data wajah
                    const faceDataLoading = document.createElement('div');
                    faceDataLoading.id = 'face-data-loading';
                    faceDataLoading.innerHTML = `
                        <div class="spinner-border text-light" role="status">
                            <span class="sr-only">Memuat data wajah...</span>
                        </div>
                        <div class="mt-2 text-light">Memuat data wajah...</div>
                    `;
                    faceDataLoading.style.position = 'absolute';
                    faceDataLoading.style.top = '50%';
                    faceDataLoading.style.left = '50%';
                    faceDataLoading.style.transform = 'translate(-50%, -50%)';
                    faceDataLoading.style.zIndex = '1000';
                    faceDataLoading.style.textAlign = 'center';
                    document.getElementById('facedetection').appendChild(faceDataLoading);

                    try {
                        // Tambahkan timestamp untuk mencegah cache
                        const timestamp = new Date().getTime();
                        const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
                        const data = await response.json();
                        console.log('Data wajah yang diterima:', data);

                        const result = await Promise.all(
                            labels.map(async (label) => {
                                const descriptions = [];
                                let validFaceFound = false;

                                // Proses setiap data wajah yang diterima
                                for (const faceData of data) {
                                    try {
                                        console.log('Memproses data wajah:', faceData);
                                        console.log('NIK:', faceData.nik);
                                        console.log('Nama file wajah:', faceData.wajah);

                                        // Cek keberadaan file foto wajah terlebih dahulu
                                        const checkImage = async (label, wajahFile) => {
                                            try {
                                                // Tambahkan timestamp untuk mencegah cache
                                                const imagePath =
                                                    `/storage/uploads/facerecognition/${label}/${wajahFile}?t=${timestamp}`;
                                                console.log('Mencoba mengakses file:',
                                                    imagePath);

                                                const response = await fetch(imagePath);
                                                if (!response.ok) {
                                                    console.warn(
                                                        `File foto wajah ${wajahFile} tidak ditemukan untuk ${label}`
                                                    );
                                                    return null;
                                                }
                                                console.log('File wajah berhasil diakses:',
                                                    imagePath);
                                                return await faceapi.fetchImage(imagePath);
                                            } catch (err) {
                                                console.error(
                                                    `Error checking image ${wajahFile} for ${label}:`,
                                                    err);
                                                return null;
                                            }
                                        };

                                        // Gunakan nilai dari key wajah sebagai nama file
                                        const img = await checkImage(label, faceData.wajah);

                                        if (img) {
                                            try {
                                                console.log('Memulai deteksi wajah untuk file:',
                                                    faceData.wajah);
                                                // Deteksi wajah dengan SSD MobileNet dan threshold yang lebih seimbang
                                                const detections = await faceapi.detectSingleFace(
                                                        img, new faceapi.SsdMobilenetv1Options({
                                                            minConfidence: 0.7
                                                        }))
                                                    .withFaceLandmarks()
                                                    .withFaceDescriptor();

                                                if (detections) {
                                                    console.log(
                                                        'Wajah berhasil dideteksi dan descriptor dibuat'
                                                    );
                                                    descriptions.push(detections.descriptor);
                                                    validFaceFound = true;
                                                }
                                            } catch (err) {
                                                console.error(
                                                    `Error processing image ${faceData.wajah} for ${label}:`,
                                                    err);
                                            }
                                        }
                                    } catch (err) {
                                        console.error(`Error processing face data:`, err);
                                    }
                                }

                                if (!validFaceFound) {
                                    console.warn(`Tidak ditemukan wajah valid untuk ${label}`);
                                    namakaryawan = "unknown";
                                } else {
                                    namakaryawan = label;
                                }

                                return new faceapi.LabeledFaceDescriptors(namakaryawan,
                                    descriptions);
                            })
                        );

                        // Hapus indikator loading setelah data wajah dimuat
                        document.getElementById('face-data-loading').remove();
                        return result;
                    } catch (error) {
                        console.error('Error dalam getLabeledFaceDescriptions:', error);
                        document.getElementById('face-data-loading').remove();
                        throw error;
                    }
                }

                async function startFaceRecognition() {
                    try {
                        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
                        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.7);

                        const video = document.querySelector('.webcam-capture video');

                        if (!video || !video.readyState) {
                            console.log('Video not ready, waiting...');
                            setTimeout(startFaceRecognition, 1000);
                            return;
                        }

                        const canvas = faceapi.createCanvasFromMedia(video);
                        canvas.style.position = 'absolute';
                        canvas.style.top = '0';
                        canvas.style.left = '0';
                        canvas.style.zIndex = '1';
                        document.getElementById('facedetection').appendChild(canvas);

                        const ctx = canvas.getContext("2d");

                        const displaySize = {
                            width: video.videoWidth,
                            height: video.videoHeight
                        };
                        faceapi.matchDimensions(canvas, displaySize);

                        let lastDetectionTime = 0;
                        const detectionInterval = isMobile ? 1000 : 500;
                        let isProcessing = false;
                        let lastFaceDetected = false;
                        let consecutiveMatches = 0;
                        const requiredConsecutiveMatches = 3;

                        async function detectFaces() {
                            try {
                                const detection = await faceapi.detectSingleFace(video, new faceapi
                                        .SsdMobilenetv1Options({
                                            minConfidence: 0.7
                                        }))
                                    .withFaceLandmarks()
                                    .withFaceDescriptor();

                                return detection ? [detection] : [];
                            } catch (error) {
                                console.error("Error dalam deteksi wajah:", error);
                                return [];
                            }
                        }

                        function updateCanvas() {
                            if (!isProcessing) {
                                const now = Date.now();
                                if (now - lastDetectionTime > detectionInterval) {
                                    isProcessing = true;
                                    lastDetectionTime = now;

                                    detectFaces()
                                        .then(detections => {
                                            const resizedDetections = faceapi.resizeResults(detections,
                                                displaySize);
                                            ctx.clearRect(0, 0, canvas.width, canvas.height);

                                            if (resizedDetections.length > 0) {
                                                resizedDetections.forEach((detection) => {
                                                    if (detection.descriptor) {
                                                        const match = faceMatcher.findBestMatch(
                                                            detection.descriptor);
                                                        console.log('Hasil matching:', match
                                                            .toString());
                                                        console.log('Distance:', match.distance);

                                                        const box = detection.detection.box;
                                                        const isUnknown = match.toString().includes(
                                                            "unknown") || match.distance > 0.5;

                                                        // Menggunakan style modern untuk box deteksi wajah
                                                        ctx.strokeStyle = isUnknown ? '#F44336' :
                                                            '#4CAF50';
                                                        ctx.lineWidth = 3;
                                                        ctx.lineJoin = 'round';
                                                        ctx.lineCap = 'round';

                                                        // Menyesuaikan ukuran box agar lebih sesuai dengan wajah
                                                        // Menambahkan sedikit padding di sekitar wajah
                                                        const padding = 10;
                                                        const adjustedBox = {
                                                            x: box.x - padding,
                                                            y: box.y - padding,
                                                            width: box.width + (padding * 2),
                                                            height: box.height + (padding * 2)
                                                        };

                                                        // Gambar box dengan sudut membulat
                                                        const radius = 8;
                                                        ctx.beginPath();
                                                        ctx.moveTo(adjustedBox.x + radius,
                                                            adjustedBox.y);
                                                        ctx.lineTo(adjustedBox.x + adjustedBox
                                                            .width - radius, adjustedBox.y);
                                                        ctx.quadraticCurveTo(adjustedBox.x +
                                                            adjustedBox.width, adjustedBox.y,
                                                            adjustedBox.x + adjustedBox.width,
                                                            adjustedBox.y + radius);
                                                        ctx.lineTo(adjustedBox.x + adjustedBox
                                                            .width, adjustedBox.y + adjustedBox
                                                            .height - radius);
                                                        ctx.quadraticCurveTo(adjustedBox.x +
                                                            adjustedBox.width, adjustedBox.y +
                                                            adjustedBox.height, adjustedBox.x +
                                                            adjustedBox.width - radius,
                                                            adjustedBox.y + adjustedBox.height);
                                                        ctx.lineTo(adjustedBox.x + radius,
                                                            adjustedBox.y + adjustedBox.height);
                                                        ctx.quadraticCurveTo(adjustedBox.x,
                                                            adjustedBox.y + adjustedBox.height,
                                                            adjustedBox.x, adjustedBox.y +
                                                            adjustedBox.height - radius);
                                                        ctx.lineTo(adjustedBox.x, adjustedBox.y +
                                                            radius);
                                                        ctx.quadraticCurveTo(adjustedBox.x,
                                                            adjustedBox.y, adjustedBox.x +
                                                            radius, adjustedBox.y);
                                                        ctx.closePath();
                                                        ctx.stroke();

                                                        // Tambahkan efek glow
                                                        ctx.shadowColor = isUnknown ?
                                                            'rgba(244, 67, 54, 0.5)' :
                                                            'rgba(76, 175, 80, 0.5)';
                                                        ctx.shadowBlur = 10;
                                                        ctx.stroke();
                                                        ctx.shadowBlur = 0;

                                                        // Label dengan style modern
                                                        const label = isUnknown ?
                                                            'Wajah Tidak Dikenali' : match
                                                            .toString();
                                                        const fontSize = 16;
                                                        ctx.font =
                                                            `${fontSize}px 'Arial', sans-serif`;
                                                        const textWidth = ctx.measureText(label)
                                                            .width;

                                                        // Background untuk label
                                                        const labelPadding = 5;
                                                        const labelHeight = fontSize +
                                                            labelPadding * 2;
                                                        const labelWidth = textWidth +
                                                            labelPadding * 2;
                                                        const labelX = adjustedBox.x;
                                                        const labelY = adjustedBox.y + adjustedBox.height + 5;

                                                        // Gambar background label dengan sudut membulat
                                                        ctx.fillStyle = isUnknown ?
                                                            'rgba(244, 67, 54, 0.8)' :
                                                            'rgba(76, 175, 80, 0.8)';
                                                        ctx.beginPath();
                                                        ctx.moveTo(labelX + radius, labelY);
                                                        ctx.lineTo(labelX + labelWidth - radius,
                                                            labelY);
                                                        ctx.quadraticCurveTo(labelX + labelWidth,
                                                            labelY, labelX + labelWidth,
                                                            labelY + radius);
                                                        ctx.lineTo(labelX + labelWidth, labelY +
                                                            labelHeight - radius);
                                                        ctx.quadraticCurveTo(labelX + labelWidth,
                                                            labelY + labelHeight, labelX +
                                                            labelWidth - radius, labelY +
                                                            labelHeight);
                                                        ctx.lineTo(labelX + radius, labelY +
                                                            labelHeight);
                                                        ctx.quadraticCurveTo(labelX, labelY +
                                                            labelHeight, labelX, labelY +
                                                            labelHeight - radius);
                                                        ctx.lineTo(labelX, labelY + radius);
                                                        ctx.quadraticCurveTo(labelX, labelY,
                                                            labelX + radius, labelY);
                                                        ctx.closePath();
                                                        ctx.fill();

                                                        // Teks label
                                                        ctx.fillStyle = 'white';
                                                        ctx.textAlign = 'left';
                                                        ctx.textBaseline = 'middle';
                                                        ctx.fillText(label, labelX + labelPadding,
                                                            labelY + labelHeight / 2);

                                                        if (isUnknown) {
                                                            faceRecognitionDetected = 0;
                                                            consecutiveMatches = 0;
                                                        } else {
                                                            consecutiveMatches++;
                                                            if (consecutiveMatches >=
                                                                requiredConsecutiveMatches) {
                                                                faceRecognitionDetected = 1;
                                                            }
                                                        }
                                                    } else {
                                                        // Hanya gambar box untuk wajah tidak dikenali
                                                        const box = detection.detection.box;
                                                        ctx.strokeStyle = '#F44336';
                                                        ctx.lineWidth = 3;
                                                        ctx.lineJoin = 'round';
                                                        ctx.lineCap = 'round';

                                                        // Menyesuaikan ukuran box agar lebih sesuai dengan wajah
                                                        // Menambahkan sedikit padding di sekitar wajah
                                                        const padding = 10;
                                                        const adjustedBox = {
                                                            x: box.x - padding,
                                                            y: box.y - padding,
                                                            width: box.width + (padding * 2),
                                                            height: box.height + (padding * 2)
                                                        };

                                                        // Gambar box dengan sudut membulat
                                                        const radius = 8;
                                                        ctx.beginPath();
                                                        ctx.moveTo(adjustedBox.x + radius,
                                                            adjustedBox.y);
                                                        ctx.lineTo(adjustedBox.x + adjustedBox
                                                            .width - radius, adjustedBox.y);
                                                        ctx.quadraticCurveTo(adjustedBox.x +
                                                            adjustedBox.width, adjustedBox.y,
                                                            adjustedBox.x + adjustedBox.width,
                                                            adjustedBox.y + radius);
                                                        ctx.lineTo(adjustedBox.x + adjustedBox
                                                            .width, adjustedBox.y + adjustedBox
                                                            .height - radius);
                                                        ctx.quadraticCurveTo(adjustedBox.x +
                                                            adjustedBox.width, adjustedBox.y +
                                                            adjustedBox.height, adjustedBox.x +
                                                            adjustedBox.width - radius,
                                                            adjustedBox.y + adjustedBox.height);
                                                        ctx.lineTo(adjustedBox.x + radius,
                                                            adjustedBox.y + adjustedBox.height);
                                                        ctx.quadraticCurveTo(adjustedBox.x,
                                                            adjustedBox.y + adjustedBox.height,
                                                            adjustedBox.x, adjustedBox.y +
                                                            adjustedBox.height - radius);
                                                        ctx.lineTo(adjustedBox.x, adjustedBox.y +
                                                            radius);
                                                        ctx.quadraticCurveTo(adjustedBox.x,
                                                            adjustedBox.y, adjustedBox.x +
                                                            radius, adjustedBox.y);
                                                        ctx.closePath();
                                                        ctx.stroke();

                                                        // Tambahkan efek glow
                                                        ctx.shadowColor = 'rgba(244, 67, 54, 0.5)';
                                                        ctx.shadowBlur = 10;
                                                        ctx.stroke();
                                                        ctx.shadowBlur = 0;

                                                        // Label dengan style modern
                                                        const label = "Wajah Tidak Dikenali";
                                                        const fontSize = 16;
                                                        ctx.font =
                                                            `${fontSize}px 'Arial', sans-serif`;
                                                        const textWidth = ctx.measureText(label)
                                                            .width;

                                                        // Background untuk label
                                                        const labelPadding = 5;
                                                        const labelHeight = fontSize +
                                                            labelPadding * 2;
                                                        const labelWidth = textWidth +
                                                            labelPadding * 2;
                                                        const labelX = adjustedBox.x;
                                                        const labelY = adjustedBox.y + adjustedBox.height + 5;

                                                        // Gambar background label dengan sudut membulat
                                                        ctx.fillStyle = 'rgba(244, 67, 54, 0.8)';
                                                        ctx.beginPath();
                                                        ctx.moveTo(labelX + radius, labelY);
                                                        ctx.lineTo(labelX + labelWidth - radius,
                                                            labelY);
                                                        ctx.quadraticCurveTo(labelX + labelWidth,
                                                            labelY, labelX + labelWidth,
                                                            labelY + radius);
                                                        ctx.lineTo(labelX + labelWidth, labelY +
                                                            labelHeight - radius);
                                                        ctx.quadraticCurveTo(labelX + labelWidth,
                                                            labelY + labelHeight, labelX +
                                                            labelWidth - radius, labelY +
                                                            labelHeight);
                                                        ctx.lineTo(labelX + radius, labelY +
                                                            labelHeight);
                                                        ctx.quadraticCurveTo(labelX, labelY +
                                                            labelHeight, labelX, labelY +
                                                            labelHeight - radius);
                                                        ctx.lineTo(labelX, labelY + radius);
                                                        ctx.quadraticCurveTo(labelX, labelY,
                                                            labelX + radius, labelY);
                                                        ctx.closePath();
                                                        ctx.fill();

                                                        // Teks label
                                                        ctx.fillStyle = 'white';
                                                        ctx.textAlign = 'left';
                                                        ctx.textBaseline = 'middle';
                                                        ctx.fillText(label, labelX + labelPadding,
                                                            labelY + labelHeight / 2);

                                                        // Set wajah tidak dikenali
                                                        faceRecognitionDetected = 0;
                                                        consecutiveMatches = 0;
                                                    }
                                                });

                                                // Reset status deteksi
                                                lastFaceDetected = true;
                                            } else {
                                                // Tidak ada wajah terdeteksi
                                                faceRecognitionDetected = 0;
                                                lastFaceDetected = false;
                                                consecutiveMatches = 0;
                                            }

                                            isProcessing = false;
                                        })
                                        .catch(err => {
                                            console.error("Face detection error:", err);
                                            isProcessing = false;
                                        });
                                }
                            }

                            // Lanjutkan loop animasi
                            requestAnimationFrame(updateCanvas);
                        }

                        // Mulai loop animasi
                        updateCanvas();
                    } catch (error) {
                        console.error("Error starting face recognition:", error);
                        // Coba inisialisasi ulang face recognition jika terjadi error
                        setTimeout(() => {
                            console.log('Retrying face recognition initialization');
                            startFaceRecognition();
                        }, 2000);
                    }
                }
            }

            $("#absenmasuk").click(function() {
                // alert(lokasi);
                $("#absenmasuk").prop('disabled', true);
                $("#absenpulang").prop('disabled', true);
                $("#absenmasuk").html(
                    '<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span style="font-size:16px">Loading...</span>'

                );
                let status = '1';
                Webcam.snap(function(uri) {
                    image = uri;
                });

                // alert(faceRecognitionDetected);
                // return false;
                if (faceRecognitionDetected == 0 && faceRecognition == 1) {
                    swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajah tidak terdeteksi',
                        didClose: function() {
                            $("#absenmasuk").prop('disabled', false);
                            $("#absenpulang").prop('disabled', false);
                            $("#absenmasuk").html(
                                '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Masuk</span>'
                            );
                        }
                    })
                    return false;
                } else {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('presensi.store') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            image: image,
                            status: status,
                            lokasi: lokasi,
                            kode_jam_kerja: "{{ $jam_kerja->kode_jam_kerja }}"
                        },
                        success: function(data) {
                            if (data.status == true) {
                                notifikasi_absenmasuk.play();
                                swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 4000
                                }).then(function() {
                                    window.location.href = '/dashboard';
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON.notifikasi == "notifikasi_radius") {
                                notifikasi_radius.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_mulaiabsen") {
                                notifikasi_mulaiabsen.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_akhirabsen") {
                                notifikasi_akhirabsen.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_sudahabsen") {
                                notifikasi_sudahabsen.play();
                            }
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message,
                                didClose: function() {
                                    $("#absenmasuk").prop('disabled', false);
                                    $("#absenpulang").prop('disabled', false);
                                    $("#absenmasuk").html(
                                        '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Masuk</span>'
                                    );
                                }

                            });
                        }
                    });
                }

            });

            $("#absenpulang").click(function() {
                // alert(lokasi);
                $("#absenmasuk").prop('disabled', true);
                $("#absenpulang").prop('disabled', true);
                $("#absenpulang").html(
                    '<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span style="font-size:16px">Loading...</span>'

                );
                let status = '2';
                Webcam.snap(function(uri) {
                    image = uri;
                });
                if (faceRecognitionDetected == 0 && faceRecognition == 1) {
                    swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajah tidak terdeteksi',
                        didClose: function() {
                            $("#absenmasuk").prop('disabled', false);
                            $("#absenpulang").prop('disabled', false);
                            $("#absenmasuk").html(
                                '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Masuk</span>'
                            );
                        }
                    })
                    return false;
                } else {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('presensi.store') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            image: image,
                            status: status,
                            lokasi: lokasi,
                            kode_jam_kerja: "{{ $jam_kerja->kode_jam_kerja }}"
                        },
                        success: function(data) {
                            if (data.status == true) {
                                notifikasi_absenpulang.play();
                                swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 4000
                                }).then(function() {
                                    window.location.href = '/dashboard';
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON.notifikasi == "notifikasi_radius") {
                                notifikasi_radius.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_mulaiabsen") {
                                notifikasi_mulaiabsen.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_akhirabsen") {
                                notifikasi_akhirabsen.play();
                            } else if (xhr.responseJSON.notifikasi == "notifikasi_sudahabsen") {
                                notifikasi_sudahabsenpulang.play();
                            }
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message,
                                didClose: function() {
                                    $("#absenmasuk").prop('disabled', false);
                                    $("#absenpulang").prop('disabled', false);
                                    $("#absenpulang").html(
                                        '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Pulang</span>'
                                    );
                                }

                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
